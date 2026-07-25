<?php

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;

class FaqController extends BaseAdminController
{
    public function index(Request $request, Response $response): string
    {
        $this->setMeta('FAQ');
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $faqs = $pdo->query("SELECT * FROM sg_faq ORDER BY sort_order ASC, created_at DESC")->fetchAll(\PDO::FETCH_ASSOC);
        return $this->view('admin.faqs.index', ['faqs' => $faqs]);
    }

    public function create(Request $request, Response $response): string
    {
        $this->setMeta('Add FAQ');
        return $this->view('admin.faqs.form', ['faq' => null]);
    }

    public function store(Request $request, Response $response): void
    {
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $question = $request->input('question');
        $answer = $request->input('answer');
        $sortOrder = (int)($request->input('sort_order', 0));
        $status = (int)($request->input('status', 1));

        if (empty($question) || empty($answer)) {
            $this->flash('error', 'Question and answer are required.');
            $this->redirectBack();
            return;
        }

        $stmt = $pdo->prepare("INSERT INTO sg_faq (question, answer, sort_order, status, created_at) VALUES (:question, :answer, :sort_order, :status, NOW())");
        $stmt->execute([
            ':question' => $question,
            ':answer' => $answer,
            ':sort_order' => $sortOrder,
            ':status' => $status,
        ]);
        $this->success('FAQ added successfully.', url('13091998/faqs'));
    }

    public function edit(Request $request, Response $response): string
    {
        $id = (int)$request->param('id');
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM sg_faq WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $faq = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$faq) $this->notFound('FAQ not found.');
        $this->setMeta('Edit FAQ');
        return $this->view('admin.faqs.form', ['faq' => $faq]);
    }

    public function update(Request $request, Response $response): void
    {
        $id = (int)$request->param('id');
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $question = $request->input('question');
        $answer = $request->input('answer');
        $sortOrder = (int)($request->input('sort_order', 0));
        $status = (int)($request->input('status', 1));

        if (empty($question) || empty($answer)) {
            $this->flash('error', 'Question and answer are required.');
            $this->redirectBack();
            return;
        }

        $stmt = $pdo->prepare("UPDATE sg_faq SET question = :question, answer = :answer, sort_order = :sort_order, status = :status WHERE id = :id");
        $stmt->execute([
            ':question' => $question,
            ':answer' => $answer,
            ':sort_order' => $sortOrder,
            ':status' => $status,
            ':id' => $id,
        ]);
        $this->success('FAQ updated successfully.', url('13091998/faqs'));
    }

    public function destroy(Request $request, Response $response): void
    {
        $id = (int)$request->input('id');
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $pdo->prepare("DELETE FROM sg_faq WHERE id = :id")->execute([':id' => $id]);
        $this->success('FAQ deleted.', url('13091998/faqs'));
    }
}
