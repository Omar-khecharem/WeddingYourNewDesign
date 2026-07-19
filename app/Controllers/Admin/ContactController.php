<?php

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Models\ContactMessage;
use App\Helpers\Security;

class ContactController extends BaseAdminController
{
    public function index(Request $request, Response $response): string
    {
        $this->setMeta('Contact Messages');
        $page = max(1, (int)$request->query('page', 1));
        $search = $request->query('search', '');
        $status = $request->query('status', '');
        $result = ContactMessage::getAll($page, PAGINATION_ADMIN_PER_PAGE, $search, $status);

        \App\Helpers\Session::set('contacts_viewed_at', time());

        return $this->view('admin.contacts.index', [
            'messages' => $result['items'],
            'pagination' => $result,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function show(Request $request, Response $response): string
    {
        $id = (int)$request->param('id');
        $message = ContactMessage::findById($id);

        if (!$message) {
            $this->flash('error', 'Message not found.');
            $this->redirect(url('13091998/contacts'));
            return '';
        }

        ContactMessage::markAsRead($id);
        $this->setMeta('Message from ' . $message['name']);

        return $this->view('admin.contacts.show', ['message' => $message]);
    }

    public function reply(Request $request, Response $response): void
    {
        $id = (int)$request->param('id');
        $message = ContactMessage::findById($id);

        if (!$message) {
            $this->flash('error', 'Message not found.');
            $this->redirect(url('13091998/contacts'));
            return;
        }

        $subject = $request->input('subject', 'Re: ' . ($message['subject'] ?? 'Your Inquiry'));
        $replyBody = Security::sanitize($request->input('reply', ''));

        if (empty($replyBody)) {
            $this->flash('error', 'Reply content is required.');
            $this->redirectBack();
            return;
        }

        $headers = "From: " . CONTACT_EMAIL . "\r\n";
        $headers .= "Reply-To: " . CONTACT_EMAIL . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        $fullMessage = "Dear {$message['name']},\n\n";
        $fullMessage .= $replyBody . "\n\n";
        $fullMessage .= "---\n";
        $fullMessage .= APP_NAME . " Team\n";
        $fullMessage .= CONTACT_EMAIL . "\n";

        $sent = @mail($message['email'], $subject, $fullMessage, $headers);

        if ($sent) {
            ContactMessage::markAsReplied($id);
            $this->flash('success', 'Reply sent to ' . $message['email']);
        } else {
            $this->flash('error', 'Failed to send email. Please try again.');
        }

        $this->redirect(url('13091998/contacts/' . $id));
    }

    public function destroy(Request $request, Response $response): void
    {
        $id = (int)$request->input('id');
        $message = ContactMessage::findById($id);

        if ($message) {
            ContactMessage::deleteById($id);
            $this->flash('success', 'Message deleted successfully.');
        } else {
            $this->flash('error', 'Message not found.');
        }

        $this->redirect(url('13091998/contacts'));
    }
}
