<?php

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\Security;
use App\Helpers\Image;

class GalleryController extends BaseAdminController
{
    public function index(Request $request, Response $response): string
    {
        $this->setMeta('Gallery');
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $gallery = $pdo->query("SELECT * FROM sg_gallery ORDER BY created_at DESC")->fetchAll(\PDO::FETCH_ASSOC);
        return $this->view('admin.gallery.index', ['gallery' => $gallery]);
    }

    public function create(Request $request, Response $response): string
    {
        $this->setMeta('Add to Gallery');
        return $this->view('admin.gallery.form', ['item' => null]);
    }

    public function store(Request $request, Response $response): void
    {
        $pdo = \App\Core\Database::getInstance()->getConnection();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = Image::upload($file, UPLOADS_DIR . DS . 'gallery', 'gallery-' . uniqid());
            if ($filename) {
                $title = pathinfo($file['name'], PATHINFO_FILENAME);
                $stmt = $pdo->prepare("INSERT INTO sg_gallery (title, image, type, created_at) VALUES (:title, :image, 'image', NOW())");
                $stmt->execute([':title' => $title, ':image' => $filename]);
                $this->success('Image added to gallery.', url('13091998/gallery'));
                return;
            }
        }

        $this->flash('error', 'Image is required.');
        $this->redirectBack();
    }

    public function edit(Request $request, Response $response): string
    {
        $id = (int)$request->param('id');
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM sg_gallery WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $item = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$item) $this->notFound('Gallery item not found.');
        $this->setMeta('Edit Gallery Item');
        return $this->view('admin.gallery.form', ['item' => $item]);
    }

    public function update(Request $request, Response $response): void
    {
        $id = (int)$request->param('id');
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $data = [];

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = Image::upload($file, UPLOADS_DIR . DS . 'gallery', 'gallery-' . uniqid());
            if ($filename) {
                $title = pathinfo($file['name'], PATHINFO_FILENAME);
                $data['title'] = $title;
                $data['image'] = $filename;
            }
        }

        if (!empty($data)) {
            $setClauses = array_map(fn($k) => "{$k} = :{$k}", array_keys($data));
            $data[':id'] = $id;
            $pdo->prepare("UPDATE sg_gallery SET " . implode(', ', $setClauses) . " WHERE id = :id")->execute($data);
        }
        $this->success('Gallery item updated.', url('13091998/gallery'));
    }

    public function destroy(Request $request, Response $response): void
    {
        $id = (int)$request->input('id');
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $pdo->prepare("DELETE FROM sg_gallery WHERE id = :id")->execute([':id' => $id]);
        $this->success('Image removed from gallery.', url('13091998/gallery'));
    }
}
