<?php
namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\Security;

class MenuItemController extends BaseAdminController
{
    public function index(Request $request, Response $response): string
    {
        $this->setMeta('Menu Items');
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $menus = $pdo->query("SELECT * FROM sg_menus ORDER BY name")->fetchAll(\PDO::FETCH_ASSOC);
        $currentMenuId = (int)($request->query('menu_id', $menus[0]['id'] ?? 0));

        $stmt = $pdo->prepare("
            SELECT mi.*, m.name AS menu_name
            FROM sg_menu_items mi
            JOIN sg_menus m ON m.id = mi.menu_id
            WHERE mi.menu_id = :menu_id
            ORDER BY mi.sort_order ASC
        ");
        $stmt->execute([':menu_id' => $currentMenuId]);
        $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $tree = $this->buildTree($items);
        $flat = $this->flattenTree($tree);

        return $this->view('admin.menus.index', [
            'menus' => $menus,
            'currentMenuId' => $currentMenuId,
            'items' => $flat,
            'tree' => $tree,
        ]);
    }

    public function create(Request $request, Response $response): string
    {
        $this->setMeta('Add Menu Item');
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $menus = $pdo->query("SELECT * FROM sg_menus ORDER BY name")->fetchAll(\PDO::FETCH_ASSOC);
        $menuId = (int)($request->query('menu_id', $menus[0]['id'] ?? 0));

        $parents = $pdo->prepare("SELECT id, title FROM sg_menu_items WHERE menu_id = :menu_id ORDER BY sort_order");
        $parents->execute([':menu_id' => $menuId]);

        return $this->view('admin.menus.form', [
            'item' => null,
            'menus' => $menus,
            'selectedMenuId' => $menuId,
            'parents' => $parents->fetchAll(\PDO::FETCH_ASSOC),
        ]);
    }

    public function store(Request $request, Response $response): void
    {
        $type = Security::sanitize($request->input('type', 'custom'));
        $referenceId = $request->input('reference_id');
        $data = [
            'menu_id' => (int)$request->input('menu_id'),
            'parent_id' => $request->input('parent_id') ? (int)$request->input('parent_id') : null,
            'title' => Security::sanitize($request->input('title', '')),
            'url' => $type === 'category' ? null : Security::sanitize($request->input('url', '')),
            'type' => $type,
            'reference_id' => $referenceId ? (int)$referenceId : null,
            'sort_order' => (int)$request->input('sort_order', 0),
            'is_active' => (int)(bool)$request->input('is_active', 1),
        ];

        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("INSERT INTO sg_menu_items (menu_id, parent_id, title, url, type, reference_id, sort_order, is_active, created_at) VALUES (:menu_id, :parent_id, :title, :url, :type, :reference_id, :sort_order, :is_active, NOW())");
        $stmt->execute($data);

        $this->success('Menu item created.', url('13091998/menus?menu_id=' . $data['menu_id']));
    }

    public function edit(Request $request, Response $response): string
    {
        $id = (int)$request->param('id');
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM sg_menu_items WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $item = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$item) {
            $this->flash('error', 'Menu item not found.');
            $this->redirect(url('13091998/menus'));
        }

        $this->setMeta('Edit: ' . $item['title']);
        $menus = $pdo->query("SELECT * FROM sg_menus ORDER BY name")->fetchAll(\PDO::FETCH_ASSOC);
        $parents = $pdo->prepare("SELECT id, title FROM sg_menu_items WHERE menu_id = :menu_id AND id != :id ORDER BY sort_order");
        $parents->execute([':menu_id' => $item['menu_id'], ':id' => $id]);

        return $this->view('admin.menus.form', [
            'item' => $item,
            'menus' => $menus,
            'selectedMenuId' => $item['menu_id'],
            'parents' => $parents->fetchAll(\PDO::FETCH_ASSOC),
        ]);
    }

    public function update(Request $request, Response $response): void
    {
        $id = (int)$request->param('id');
        $type = Security::sanitize($request->input('type', 'custom'));
        $referenceId = $request->input('reference_id');
        $data = [
            'menu_id' => (int)$request->input('menu_id'),
            'parent_id' => $request->input('parent_id') ? (int)$request->input('parent_id') : null,
            'title' => Security::sanitize($request->input('title', '')),
            'url' => $type === 'category' ? null : Security::sanitize($request->input('url', '')),
            'type' => $type,
            'reference_id' => $referenceId ? (int)$referenceId : null,
            'sort_order' => (int)$request->input('sort_order', 0),
            'is_active' => (int)(bool)$request->input('is_active', 1),
            ':id' => $id,
        ];

        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("UPDATE sg_menu_items SET menu_id = :menu_id, parent_id = :parent_id, title = :title, url = :url, type = :type, reference_id = :reference_id, sort_order = :sort_order, is_active = :is_active WHERE id = :id");
        $stmt->execute($data);

        $this->success('Menu item updated.', url('13091998/menus?menu_id=' . $data['menu_id']));
    }

    public function destroy(Request $request, Response $response): void
    {
        $id = (int)$request->input('id');
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $pdo->prepare("DELETE FROM sg_menu_items WHERE id = :id")->execute([':id' => $id]);
        $this->success('Menu item deleted.', url('13091998/menus'));
    }

    private function buildTree(array $items, int $parentId = null): array
    {
        $tree = [];
        foreach ($items as $item) {
            if ($item['parent_id'] === $parentId) {
                $children = $this->buildTree($items, (int)$item['id']);
                if ($children) {
                    $item['children'] = $children;
                }
                $tree[] = $item;
            }
        }
        return $tree;
    }

    private function flattenTree(array $tree, int $depth = 0): array
    {
        $result = [];
        foreach ($tree as $item) {
            $item['depth'] = $depth;
            $children = $item['children'] ?? [];
            unset($item['children']);
            $result[] = $item;
            if (!empty($children)) {
                $result = array_merge($result, $this->flattenTree($children, $depth + 1));
            }
        }
        return $result;
    }
}
