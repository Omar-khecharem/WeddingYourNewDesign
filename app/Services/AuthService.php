<?php
/**
 * Auth Service
 * 
 * Handles user authentication, registration, password management,
 * session management, and account verification.
 *
 * @package App\Services
 */

namespace App\Services;

use App\Helpers\Security;
use App\Helpers\Session;
use App\Helpers\Validation;
use App\Core\Database;

class AuthService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Register a new user
     */
    public function register(array $data): array
    {
        $validator = new Validation();
        $validator->validate($data, [
            'name' => 'required|min:2|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'phone',
            'password' => 'required|min:8|max:100',
            'password_confirmation' => 'required|match:password'
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors()];
        }

        $pdo = $this->db->getConnection();

        // Lookup the User role ID
        $roleStmt = $pdo->prepare("SELECT id FROM sg_roles WHERE slug = 'user' LIMIT 1");
        $roleStmt->execute();
        $customerRole = $roleStmt->fetch();
        $roleId = $customerRole ? (int)$customerRole['id'] : null;

        $userData = [
            'name' => Security::sanitize($data['name']),
            'email' => Security::sanitizeEmail($data['email']),
            'phone' => $data['phone'] ?? null,
            'password' => Security::hashPassword($data['password']),
            'role_id' => $roleId,
            'status' => STATUS_ACTIVE,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $stmt = $pdo->prepare("
            INSERT INTO sg_users (name, email, phone, password, role_id, status, created_at)
            VALUES (:name, :email, :phone, :password, :role_id, :status, :created_at)
        ");
        $stmt->execute($userData);

        $userId = $pdo->lastInsertId();

        // Auto login after registration
        $this->loginUser($userId);

        logActivity('register', "New user registered: {$userData['email']}");

        return ['success' => true, 'user_id' => $userId];
    }

    /**
     * Authenticate a user
     */
    public function login(string $email, string $password, bool $skipRateLimit = false): array
    {
        if (!$skipRateLimit) {
            $rateKey = 'login_' . ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
            if (!Security::checkRateLimit($rateKey, MAX_LOGIN_ATTEMPTS, LOGIN_LOCKOUT_TIME * 60)) {
                return ['success' => false, 'message' => 'Too many login attempts. Please try again after ' . LOGIN_LOCKOUT_TIME . ' minutes.'];
            }
        }

        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("SELECT u.*, r.slug AS role FROM sg_users u LEFT JOIN sg_roles r ON r.id = u.role_id WHERE u.email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if (!$user) {
            return ['success' => false, 'message' => 'Invalid email or password.'];
        }

        if (!Security::verifyPassword($password, $user['password'])) {
            return ['success' => false, 'message' => 'Invalid email or password.'];
        }

        if ((int)$user['status'] !== STATUS_ACTIVE) {
            return ['success' => false, 'message' => 'Your account has been deactivated. Please contact support.'];
        }

        $this->loginUser($user['id']);

        // Update last login
        $stmt = $pdo->prepare("UPDATE sg_users SET last_login_at = NOW(), last_login_ip = :ip WHERE id = :id");
        $stmt->execute([
            ':ip' => Security::getClientIp(),
            ':id' => $user['id']
        ]);

        logActivity('login', "User logged in: {$email}");

        return ['success' => true, 'user' => $user];
    }

    /**
     * Log out the current user
     */
    public function logout(): void
    {
        $user = Session::get('user');
        if ($user) {
            logActivity('logout', "User logged out: {$user['email']}");
        }
        Session::destroy();
    }

    /**
     * Set user session after login
     */
    private function loginUser(int $userId): void
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("
            SELECT u.id, u.name, u.email, u.phone, r.slug AS role, u.avatar
            FROM sg_users u
            LEFT JOIN sg_roles r ON r.id = u.role_id
            WHERE u.id = :id
        ");
        $stmt->execute([':id' => $userId]);
        $user = $stmt->fetch();

        Session::set('user', $user);
        Session::regenerate();
        Session::regenerateCsrf();
    }

    /**
     * Send password reset request to admin (no email sent to user)
     */
    public function forgotPassword(string $email): array
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("SELECT id, name FROM sg_users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if (!$user) {
            return ['success' => true, 'message' => 'If the email exists, the admin has been notified.'];
        }

        $expires = date('Y-m-d H:i:s', time() + 86400); // 24 hours

        $stmt = $pdo->prepare("INSERT INTO sg_password_resets (email, token, expires_at, admin_request) VALUES (:email, :token, :expires, 1)");
        $stmt->execute([':email' => $email, ':token' => '', ':expires' => $expires]);

        logActivity('password_reset', "Password reset requested for: {$email} (pending admin)");

        return ['success' => true, 'message' => 'Your request has been sent to the admin. Please wait for them to reset your password.'];
    }

    /**
     * Reset password with token
     */
    public function resetPassword(string $token, string $password): array
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM sg_password_resets WHERE token = :token AND used = 0 AND expires_at > NOW() LIMIT 1");
        $stmt->execute([':token' => $token]);
        $reset = $stmt->fetch();

        if (!$reset) {
            return ['success' => false, 'message' => 'Invalid or expired reset token.'];
        }

        $hashed = Security::hashPassword($password);
        $stmt = $pdo->prepare("UPDATE sg_users SET password = :password WHERE email = :email");
        $stmt->execute([':password' => $hashed, ':email' => $reset['email']]);

        $stmt = $pdo->prepare("UPDATE sg_password_resets SET used = 1 WHERE id = :id");
        $stmt->execute([':id' => $reset['id']]);

        logActivity('password_reset', "Password reset completed: {$reset['email']}");

        return ['success' => true, 'message' => 'Password has been reset successfully.'];
    }

    /**
     * Send password reset email
     */
    private function sendResetEmail(string $email, string $token): void
    {
        $subject = 'Reset Your ' . APP_NAME . ' Password';
        $resetUrl = url('reset-password/' . $token);

        $message = "Hello,\n\n";
        $message .= "You have requested a password reset for your " . APP_NAME . " account.\n\n";
        $message .= "Click the link below to reset your password:\n";
        $message .= $resetUrl . "\n\n";
        $message .= "This link will expire in 1 hour.\n\n";
        $message .= "If you did not request this, please ignore this email.\n\n";
        $message .= "Regards,\n" . APP_NAME . " Team";

        @mail($email, $subject, $message, "From: " . CONTACT_EMAIL);
    }

    /**
     * Get current user details
     */
    public function getCurrentUser(): ?array
    {
        return Session::get('user');
    }

    /**
     * Update user profile
     */
    public function updateProfile(int $userId, array $data): array
    {
        $validator = new Validation();
        $rules = [
            'name' => 'required|min:2|max:100',
            'phone' => 'phone',
        ];

        if (!empty($data['email'])) {
            $rules['email'] = "required|email|unique:users,email,{$userId},id";
        }

        $validator->validate($data, $rules);

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors()];
        }

        $updateData = [
            'name' => Security::sanitize($data['name']),
            'phone' => $data['phone'] ?? null,
        ];

        if (!empty($data['email'])) {
            $updateData['email'] = Security::sanitizeEmail($data['email']);
        }

        $pdo = $this->db->getConnection();
        $sets = [];
        $params = [];
        foreach ($updateData as $key => $value) {
            $sets[] = "{$key} = :{$key}";
            $params[":{$key}"] = $value;
        }
        $params[':id'] = $userId;

        $sql = "UPDATE sg_users SET " . implode(', ', $sets) . " WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Update session
        $this->loginUser($userId);

        logActivity('profile_update', "Profile updated for user ID: {$userId}");

        return ['success' => true, 'message' => 'Profile updated successfully.'];
    }

    /**
     * Change password
     */
    public function changePassword(int $userId, string $currentPassword, string $newPassword): array
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("SELECT password FROM sg_users WHERE id = :id");
        $stmt->execute([':id' => $userId]);
        $user = $stmt->fetch();

        if (!Security::verifyPassword($currentPassword, $user['password'])) {
            return ['success' => false, 'message' => 'Current password is incorrect.'];
        }

        $hashed = Security::hashPassword($newPassword);
        $stmt = $pdo->prepare("UPDATE sg_users SET password = :password WHERE id = :id");
        $stmt->execute([':password' => $hashed, ':id' => $userId]);

        logActivity('password_change', "Password changed for user ID: {$userId}");

        return ['success' => true, 'message' => 'Password changed successfully.'];
    }

    /**
     * Check if user is logged in
     */
    public function isLoggedIn(): bool
    {
        return Session::has('user');
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        $user = Session::get('user');
        return $user && in_array($user['role'] ?? '', ['admin']);
    }
}
