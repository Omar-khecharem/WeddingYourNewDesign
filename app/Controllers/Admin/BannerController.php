<?php

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\Security;
use App\Helpers\Image;

class BannerController extends BaseAdminController
{
    public function index(Request $request, Response $response): string
    {
        $this->setMeta('Banners');
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $banners = $pdo->query("SELECT * FROM sg_banners ORDER BY position, sort_order")->fetchAll(\PDO::FETCH_ASSOC);
        return $this->view('admin.banners.index', ['banners' => $banners]);
    }

    public function create(Request $request, Response $response): string
    {
        $this->setMeta('Add Banner');
        return $this->view('admin.banners.form', ['banner' => null]);
    }

    public function store(Request $request, Response $response): void
    {
        $pos = $request->input('position', 'hero_right');
        $nameMap = ['hero_left'=>'Hero Video','hero_right'=>'Hero Image','bride_video'=>'Bride Video'];
        $data = [
            'name' => $nameMap[$pos] ?? 'Banner',
            'title' => $request->input('title', ''),
            'description' => $request->input('description', ''),
            'link' => $request->input('link', ''),
            'position' => $pos,
            'sort_order' => (int)$request->input('sort_order', 0),
            'is_active' => (int)(bool)$request->input('is_active', 1),
        ];

        $pdo = \App\Core\Database::getInstance()->getConnection();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $isVideo = in_array($ext, ['mp4', 'webm', 'ogg', 'mov']);
            $dir = $isVideo ? UPLOADS_DIR . DS . 'videos' : UPLOADS_DIR;
            $filename = Image::upload($file, $dir, $pos . '-' . uniqid());
            if ($filename) $data['image'] = $filename;
        }

        $stmt = $pdo->prepare("INSERT INTO sg_banners (name, title, description, image, link, position, sort_order, is_active, created_at) VALUES (:name, :title, :description, :image, :link, :position, :sort_order, :is_active, NOW())");
        $stmt->execute($data);

        $this->success('Banner created.', url('13091998/banners'));
    }

    public function edit(Request $request, Response $response): string
    {
        $id = (int)$request->param('id');
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM sg_banners WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $banner = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$banner) {
            $this->flash('error', 'Banner not found.');
            $this->redirect(url('13091998/banners'));
        }

        $this->setMeta('Edit Banner');
        return $this->view('admin.banners.form', ['banner' => $banner]);
    }

    public function update(Request $request, Response $response): void
    {
        $id = (int)$request->param('id');
        $pos = $request->input('position', 'hero_right');
        $nameMap = ['hero_left'=>'Hero Video','hero_right'=>'Hero Image','bride_video'=>'Bride Video'];
        $data = [
            'name' => $nameMap[$pos] ?? 'Banner',
            'title' => $request->input('title', ''),
            'description' => $request->input('description', ''),
            'link' => $request->input('link', ''),
            'position' => $pos,
            'sort_order' => (int)$request->input('sort_order', 0),
            'is_active' => (int)(bool)$request->input('is_active', 1),
            ':id' => $id,
        ];

        $pdo = \App\Core\Database::getInstance()->getConnection();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $isVideo = in_array($ext, ['mp4', 'webm', 'ogg', 'mov']);
            $dir = $isVideo ? UPLOADS_DIR . DS . 'videos' : UPLOADS_DIR;
            $filename = Image::upload($file, $dir, $pos . '-' . uniqid());
            if ($filename) {
                $pdo->prepare("UPDATE sg_banners SET image = :img WHERE id = :id")->execute([':img' => $filename, ':id' => $id]);
            }
        }

        $stmt = $pdo->prepare("UPDATE sg_banners SET name = :name, title = :title, description = :description, link = :link, position = :position, sort_order = :sort_order, is_active = :is_active WHERE id = :id");
        $stmt->execute($data);

        $this->success('Banner updated.', url('13091998/banners'));
    }

    public function destroy(Request $request, Response $response): void
    {
        $id = (int)$request->input('id');
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $pdo->prepare("DELETE FROM sg_banners WHERE id = :id")->execute([':id' => $id]);
        $this->success('Banner deleted.', url('13091998/banners'));
    }
}
