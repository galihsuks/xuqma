<?php

namespace App\Controllers;

class LogController extends BaseController
{
    public function create()
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = '';
        $this->RouterCode = 'LOG-CREATE';
        $body = $this->getBody();

        $signature = $this->request->getHeaderLine('X-Signature');
        $timestamp = $this->request->getHeaderLine('X-Timestamp');
        $signatureError = $this->checkingSignature($signature, $timestamp, $body);
        if ($signatureError) {
            return $this->responseByStatus($signatureError['status'], $signatureError['message']);
        }

        $rules = [
            'level' => 'required|in_list[info,warning,error]',
            'message' => 'required|min_length[1]',
            'context' => 'permit_empty',
        ];
        if (!$this->validateData($body, $rules)) {
            return $this->validationError($this->validator->getErrors());
        }

        try {
            $context = $body['context'] ?? [];
            if (!is_array($context)) {
                $context = ['value' => $context];
            }

            if ($body['level'] === 'warning') {
                $this->logModel->warning($body['message'], $context, $this->request->getIPAddress());
            } else if ($body['level'] === 'error') {
                $this->logModel->error($body['message'], $context, $this->request->getIPAddress());
            } else {
                $this->logModel->info($body['message'], $context, $this->request->getIPAddress());
            }

            return $this->created('Log saved successfully');
        } catch (\Throwable $th) {
            return $this->serverError("There's a problem with the server, Contact us!", [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);
        }
    }

    public function index()
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'LOG-INDEX';

        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $defaultPageSize = (int) $this->logModel->LIMIT_DATA;
        $pageSize = max(1, min(100, (int) ($this->request->getGet('page_size') ?? $defaultPageSize)));
        $level = (string) ($this->request->getGet('level') ?? '');
        $keywords = trim((string) ($this->request->getGet('keywords') ?? $this->request->getGet('q') ?? ''));
        $date = trim((string) ($this->request->getGet('date') ?? ''));
        $startTime = trim((string) ($this->request->getGet('start_time') ?? ''));
        $endTime = trim((string) ($this->request->getGet('end_time') ?? ''));

        $result = $this->logModel->getLogPaginated($page, $pageSize, $level, $keywords, $date, $startTime, $endTime);
        $items = $result['items'];
        $totalItems = (int) $result['total_items'];

        $totalPages = max(1, (int) ceil($totalItems / $pageSize));
        $pagination = [
            'page' => $page,
            'page_size' => $pageSize,
            'total_items' => $totalItems,
            'total_pages' => $totalPages,
            'has_next' => $page < $totalPages,
            'has_prev' => $page > 1,
        ];

        return $this->success('List log', $items, $pagination);
    }

    public function clear()
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'LOG-CLEAR';
        $level = (string) ($this->request->getGet('level') ?? '');
        $keywords = trim((string) ($this->request->getGet('keywords') ?? $this->request->getGet('q') ?? ''));
        $date = trim((string) ($this->request->getGet('date') ?? ''));
        $startTime = trim((string) ($this->request->getGet('start_time') ?? ''));
        $endTime = trim((string) ($this->request->getGet('end_time') ?? ''));

        try {
            $deletedRows = $this->logModel->clearFilteredLogs($level, $keywords, $date, $startTime, $endTime);

            return $this->success($deletedRows > 0 ? 'Filtered logs cleared successfully' : 'No logs matched the current filter');
        } catch (\Throwable $th) {
            return $this->serverError("There's a problem with the server, Contact us!", [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);
        }
    }
}
