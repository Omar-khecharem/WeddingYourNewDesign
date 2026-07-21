<?php
namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Helpers\Session;
use App\Helpers\Security;

class PageController extends BaseAdminController
{
    public function index(Request $request, Response $response): string
    {
        $this->setMeta('Manage Pages');
        $pdo = Database::getInstance()->getConnection();
        try {
            $pages = $pdo->query("SELECT * FROM sg_pages ORDER BY sort_order ASC, title ASC")->fetchAll();
        } catch (\PDOException $e) {
            $pdo->exec("CREATE TABLE IF NOT EXISTS sg_pages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL UNIQUE,
                content LONGTEXT,
                meta_title VARCHAR(255),
                meta_description TEXT,
                status TINYINT DEFAULT 1,
                sort_order INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )");
            $pages = [];
        }
        return $this->view('admin.pages.index', ['pages' => $pages]);
    }

    public function create(Request $request, Response $response): string
    {
        $this->setMeta('Create Page');
        return $this->view('admin.pages.form');
    }

    public function store(Request $request, Response $response): void
    {
        $data = [
            'title' => $request->input('title', ''),
            'slug' => $request->input('slug') ?: slugify($request->input('title', '')),
            'content' => $request->input('content', ''),
            'meta_title' => $request->input('meta_title', ''),
            'meta_description' => $request->input('meta_description', ''),
            'status' => (int)$request->input('status', 1),
            'sort_order' => (int)$request->input('sort_order', 0),
        ];

        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("INSERT INTO sg_pages (title, slug, content, meta_title, meta_description, status, sort_order) VALUES (:t, :s, :c, :mt, :md, :st, :so)");
        $stmt->execute([':t' => $data['title'], ':s' => $data['slug'], ':c' => $data['content'], ':mt' => $data['meta_title'], ':md' => $data['meta_description'], ':st' => $data['status'], ':so' => $data['sort_order']]);

        Session::flash('success', 'Page created successfully.');
        $this->redirect(url('13091998/pages'));
    }

    public function edit(Request $request, Response $response): string
    {
        $id = (int)$request->param('id');
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM sg_pages WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $page = $stmt->fetch();
        if (!$page) { http_response_code(404); exit; }

        $this->setMeta('Edit Page');
        return $this->view('admin.pages.form', ['page' => $page]);
    }

    public function update(Request $request, Response $response): void
    {
        $id = (int)$request->param('id');
        $data = [
            'title' => $request->input('title', ''),
            'slug' => $request->input('slug', ''),
            'content' => $request->input('content', ''),
            'meta_title' => $request->input('meta_title', ''),
            'meta_description' => $request->input('meta_description', ''),
            'status' => (int)$request->input('status', 1),
            'sort_order' => (int)$request->input('sort_order', 0),
        ];

        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("UPDATE sg_pages SET title = :t, slug = :s, content = :c, meta_title = :mt, meta_description = :md, status = :st, sort_order = :so WHERE id = :id");
        $stmt->execute([':t' => $data['title'], ':s' => $data['slug'], ':c' => $data['content'], ':mt' => $data['meta_title'], ':md' => $data['meta_description'], ':st' => $data['status'], ':so' => $data['sort_order'], ':id' => $id]);

        Session::flash('success', 'Page updated successfully.');
        $this->redirect(url('13091998/pages'));
    }

    public function destroy(Request $request, Response $response): void
    {
        $id = (int)$request->input('id', 0);
        if ($id) {
            $pdo = Database::getInstance()->getConnection();
            $pdo->prepare("DELETE FROM sg_pages WHERE id = :id")->execute([':id' => $id]);
            Session::flash('success', 'Page deleted successfully.');
        }
        $this->redirect(url('13091998/pages'));
    }
}
