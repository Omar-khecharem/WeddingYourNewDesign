<?php
namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Helpers\Session;
use App\Helpers\Security;
use App\Helpers\Image;
use App\Models\Blog;

class BlogController extends BaseAdminController
{
    public function index(Request $request, Response $response): string
    {
        $this->setMeta('Manage Blog Posts');
        $posts = Blog::getAll();
        return $this->view('admin.blog.index', ['posts' => $posts]);
    }

    public function create(Request $request, Response $response): string
    {
        $this->setMeta('Create Blog Post');
        return $this->view('admin.blog.form');
    }

    public function store(Request $request, Response $response): void
    {
        $pdo = Database::getInstance()->getConnection();

        $slug = $request->input('slug') ?: slugify($request->input('title', ''));

        $featuredImage = '';
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            $errors = Image::validate($_FILES['featured_image']);
            if (empty($errors)) {
                $uploaded = Image::upload($_FILES['featured_image'], UPLOADS_DIR . DS . 'blogs');
                if ($uploaded) {
                    $featuredImage = $uploaded;
                }
            }
        }

        $stmt = $pdo->prepare("INSERT INTO sg_blogs (category_id, author_id, title, slug, excerpt, content, featured_image, meta_title, meta_description, is_published, published_at) VALUES (:c, :a, :t, :s, :e, :co, :fi, :mt, :md, :ip, :pa)");
        $stmt->execute([
            ':c' => Blog::resolveCategoryId($request->input('category_name', '')) ?: null,
            ':a' => (int)$request->input('author_id', 0) ?: null,
            ':t' => $request->input('title', ''),
            ':s' => $slug,
            ':e' => $request->input('excerpt', ''),
            ':co' => $request->input('content', ''),
            ':fi' => $featuredImage,
            ':mt' => $request->input('meta_title', ''),
            ':md' => $request->input('meta_description', ''),
            ':ip' => (int)$request->input('is_published', 0),
            ':pa' => $request->input('published_at') ?: date('Y-m-d H:i:s'),
        ]);

        Session::flash('success', 'Blog post created successfully.');
        $this->redirect(url('13091998/blog'));
    }

    public function edit(Request $request, Response $response): string
    {
        $id = (int)$request->param('id');
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM sg_blogs WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $post = $stmt->fetch();
        if (!$post) { http_response_code(404); exit; }

        $this->setMeta('Edit Blog Post');
        $post['category_name'] = Blog::getCategoryName((int)($post['category_id'] ?? 0));
        return $this->view('admin.blog.form', ['post' => $post]);
    }

    public function update(Request $request, Response $response): void
    {
        $id = (int)$request->param('id');
        $pdo = Database::getInstance()->getConnection();

        $featuredImage = $request->input('existing_image', '');

        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            $errors = Image::validate($_FILES['featured_image']);
            if (empty($errors)) {
                $uploaded = Image::upload($_FILES['featured_image'], UPLOADS_DIR . DS . 'blogs');
                if ($uploaded) {
                    if ($featuredImage) {
                        Image::delete($featuredImage, UPLOADS_DIR . DS . 'blogs');
                    }
                    $featuredImage = $uploaded;
                }
            }
        }

        $stmt = $pdo->prepare("UPDATE sg_blogs SET category_id = :c, title = :t, slug = :s, excerpt = :e, content = :co, featured_image = :fi, meta_title = :mt, meta_description = :md, is_published = :ip, published_at = :pa WHERE id = :id");
        $stmt->execute([
            ':c' => Blog::resolveCategoryId($request->input('category_name', '')) ?: null,
            ':t' => $request->input('title', ''),
            ':s' => $request->input('slug', ''),
            ':e' => $request->input('excerpt', ''),
            ':co' => $request->input('content', ''),
            ':fi' => $featuredImage,
            ':mt' => $request->input('meta_title', ''),
            ':md' => $request->input('meta_description', ''),
            ':ip' => (int)$request->input('is_published', 0),
            ':pa' => $request->input('published_at') ?: date('Y-m-d H:i:s'),
            ':id' => $id,
        ]);

        Session::flash('success', 'Blog post updated successfully.');
        $this->redirect(url('13091998/blog'));
    }

    public function destroy(Request $request, Response $response): void
    {
        $id = (int)$request->input('id', 0);
        if ($id) {
            $pdo = Database::getInstance()->getConnection();
            $stmt = $pdo->prepare("SELECT featured_image FROM sg_blogs WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $post = $stmt->fetch();
            if ($post && $post['featured_image']) {
                Image::delete($post['featured_image'], UPLOADS_DIR . DS . 'blogs');
            }
            $pdo->prepare("DELETE FROM sg_blogs WHERE id = :id")->execute([':id' => $id]);
            Session::flash('success', 'Blog post deleted successfully.');
        }
        $this->redirect(url('13091998/blog'));
    }
}
