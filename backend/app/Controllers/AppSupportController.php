<?php

namespace App\Controllers;

class AppSupportController extends BaseController
{
    public function index()
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'APP-SUPPORT-INDEX';
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $defaultPageSize = (int) $this->appSupportModel->LIMIT_DATA;
        $pageSize = max(1, min(100, (int) ($this->request->getGet('page_size') ?? $defaultPageSize)));
        $keywords = trim((string) ($this->request->getGet('keywords') ?? ''));
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', [
            'path' => $this->request->getPath(),
            'query' => $this->request->getGet(),
        ]);

        $result = $this->appSupportModel->getAppSupportListPaginated($page, $pageSize, $keywords);
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
        return $this->success('List app support', $items, $pagination);
    }

    public function detail($id)
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'APP-SUPPORT-DETAIL';
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', [
            'path' => $this->request->getPath(),
        ]);

        $data = $this->appSupportModel->getAppSupportById($id);
        if (!$data) {
            return $this->notFound('App support not found!');
        }

        return $this->success('Detail app support', $data);
    }

    public function create()
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'APP-SUPPORT-CREATE';
        $body = $this->getBody();
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', [
            'path' => $this->request->getPath(),
            'request' => $body,
        ]);

        $rules = [
            'key' => 'required|min_length[2]|max_length[100]',
            'value' => 'required',
            'datatype' => 'required|in_list[string,number,json,boolean]',
        ];
        if (!$this->validateData($body, $rules)) {
            return $this->validationError($this->validator->getErrors());
        }

        if ($this->appSupportModel->getAppSupportByKey($body['key'])) {
            return $this->badRequest('Key already used!');
        }

        try {
            $this->appSupportModel->insertAppSupport([
                'key' => $body['key'],
                'value' => $this->normalizeValueByDatatype($body['value'], $body['datatype']),
                'datatype' => $body['datatype'],
            ]);
            $created = $this->appSupportModel->getAppSupportByKey($body['key']);

            return $this->created('App support created successfully', $created);
        } catch (\Throwable $th) {
            return $this->serverError("There's a problem with the server, Contact us!", [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);
        }
    }

    public function edit($id)
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'APP-SUPPORT-EDIT';
        $body = $this->getBody();
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', [
            'path' => $this->request->getPath(),
            'request' => $body,
        ]);

        $current = $this->appSupportModel->getAppSupportById($id);
        if (!$current) {
            return $this->notFound('App support not found!');
        }

        $rules = [
            'key' => 'required|min_length[2]|max_length[100]',
            'value' => 'required',
            'datatype' => 'required|in_list[string,number,json,boolean]',
        ];
        if (!$this->validateData($body, $rules)) {
            return $this->validationError($this->validator->getErrors());
        }

        $duplicate = $this->appSupportModel->getAppSupportByKey($body['key']);
        if ($duplicate && (string) $duplicate['id'] !== (string) $id) {
            return $this->badRequest('Key already used!');
        }

        try {
            $updated = $this->appSupportModel->updateAppSupportById($id, [
                'key' => $body['key'],
                'value' => $this->normalizeValueByDatatype($body['value'], $body['datatype']),
                'datatype' => $body['datatype'],
            ]);

            return $this->success('App support updated successfully', $updated);
        } catch (\Throwable $th) {
            return $this->serverError("There's a problem with the server, Contact us!", [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);
        }
    }

    public function destroy($id)
    {
        $this->UUID = strtoupper(uniqid());
        $this->UserID = $this->request->user['id'] ?? '';
        $this->RouterCode = 'APP-SUPPORT-DESTROY';
        $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Request API', [
            'path' => $this->request->getPath(),
        ]);

        try {
            $deleted = $this->appSupportModel->destroyAppSupportById($id);
            if (!$deleted) {
                return $this->notFound('App support not found!');
            }

            return $this->success('App support deleted successfully', $deleted);
        } catch (\Throwable $th) {
            return $this->serverError("There's a problem with the server, Contact us!", [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);
        }
    }

    private function normalizeValueByDatatype($value, string $datatype): string
    {
        if ($datatype === 'json') {
            if (is_string($value)) {
                json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $value;
                }
            }

            return json_encode($value, JSON_UNESCAPED_SLASHES);
        }

        if ($datatype === 'boolean') {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
        }

        if ($datatype === 'number') {
            return (string) (is_numeric($value) ? $value : 0);
        }

        return (string) $value;
    }
}
