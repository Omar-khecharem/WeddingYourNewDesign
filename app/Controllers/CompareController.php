<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Compare;

class CompareController extends Controller
{
    public function index(Request $request, Response $response): string
    {
        $userId = $this->getUserId();
        $sessionId = $this->getSessionId();
        $products = Compare::getList($userId, $sessionId);
        $count = Compare::getCount($userId, $sessionId);

        $this->setMeta('Compare Products - WeddingYour', 'Compare your favorite Bengali wedding products side by side.');
        $this->setBreadcrumb([
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'Comparer'],
        ]);

        return $this->view('products.compare', compact('products', 'count'));
    }

    public function toggle(Request $request, Response $response): void
    {
        $productId = (int)$request->input('product_id', 0);
        if (!$productId) {
            $this->json(['success' => false, 'message' => 'Invalid product.']);
            return;
        }

        $userId = $this->getUserId();
        $sessionId = $this->getSessionId();
        $result = Compare::toggle($userId, $sessionId, $productId);

        \App\Helpers\Session::set('compare.count', $result['count']);
        $this->json(['success' => true, 'action' => $result['action'], 'count' => $result['count']]);
    }

    public function count(Request $request, Response $response): void
    {
        $userId = $this->getUserId();
        $sessionId = $this->getSessionId();
        $this->json(['count' => Compare::getCount($userId, $sessionId)]);
    }

    public function ids(Request $request, Response $response): void
    {
        $userId = $this->getUserId();
        $sessionId = $this->getSessionId();
        $pdo = \App\Core\Database::getInstance()->getConnection();
        if ($userId) {
            $stmt = $pdo->prepare("SELECT product_id FROM sg_compare WHERE user_id = :u");
            $stmt->execute([':u' => $userId]);
        } else {
            $stmt = $pdo->prepare("SELECT product_id FROM sg_compare WHERE session_id = :s");
            $stmt->execute([':s' => $sessionId]);
        }
        $ids = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        $this->json(['success' => true, 'ids' => array_map('intval', $ids), 'count' => count($ids)]);
    }

    public function clear(Request $request, Response $response): void
    {
        $userId = $this->getUserId();
        $sessionId = $this->getSessionId();
        Compare::clear($userId, $sessionId);
        $this->json(['success' => true]);
    }

    private function getUserId(): ?int
    {
        return \App\Helpers\Session::get('user.id') ?: null;
    }

    private function getSessionId(): ?string
    {
        return session_id() ?: null;
    }
}
