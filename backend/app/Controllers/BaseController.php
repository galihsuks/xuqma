<?php

namespace App\Controllers;

use App\Models\AppSupportModel;
use App\Models\LogModel;
use App\Models\MenuControlModel;
use App\Models\MenuModel;
use App\Models\RoleModel;
use App\Models\RoleMenuControlModel;
use App\Models\TokenModel;
use App\Models\UserModel;
use App\Models\UserRoleModel;
use App\Models\AuthModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */

abstract class BaseController extends Controller
{
    /**
     * @var LogModel
    */
    public $logModel;
    
    /**
     * @var AppSupportModel
    */
    public $appSupportModel;
    
    /**
     * @var UserModel
    */
    protected $userModel;
    
    /**
     * @var AuthModel
    */
    protected $authModel;
    
    /**
     * @var TokenModel
    */
    protected $tokenModel;

    /**
     * @var RoleModel
    */
    protected $roleModel;

    /**
     * @var UserRoleModel
    */
    protected $userRoleModel;

    /**
     * @var MenuModel
    */
    protected $menuModel;

    /**
     * @var MenuControlModel
    */
    protected $menuControlModel;

    /**
     * @var RoleMenuControlModel
    */
    protected $roleMenuControlModel;
    
    protected $UUID;
    protected $UserID;
    protected $RouterCode;
    protected const CUSTOM_ENV_KEYS = [
        'API_SECRET',
        'JWT_SECRET',
        'ALLOWED_ORIGINS',
    ];
    public function __construct()
    {
        $this->logModel = new LogModel();
        $this->appSupportModel = new AppSupportModel();
        $this->userModel = new UserModel();
        $this->authModel = new AuthModel();
        $this->tokenModel = new TokenModel();
        $this->roleModel = new RoleModel();
        $this->userRoleModel = new UserRoleModel();
        $this->menuModel = new MenuModel();
        $this->menuControlModel = new MenuControlModel();
        $this->roleMenuControlModel = new RoleMenuControlModel();
        $this->UUID = '';
        $this->UserID = '';
        $this->RouterCode = '';
    }

    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = service('session');
    }

    public function infoLog($message, $content = []) {
        $this->logModel->info($message, $content, $this->request->getIPAddress());
    }
    public function errorLog($message, $content = []) {
        $this->logModel->error($message, $content, $this->request->getIPAddress());
    }
    public function warningLog($message, $content = []) {
        $this->logModel->warning($message, $content, $this->request->getIPAddress());
    }

    // Jika ada API unprotected maka wajib kirim signature di headernya
    public function checkingSignature($signature, $timestamp, $payload) {
        if ($this->shouldBypassSignatureCheck()) {
            return false;
        }

        if (!$signature || !$timestamp) {
            return [
                'message' => 'Signature required!',
                'status' => 400
            ];
        }
        if (abs(time() - (int)$timestamp) > 3) {
            return [
                'message' => 'Request expired',
                'status' => 400
            ];
        }

        $secret = (string) $this->getCustomEnv('API_SECRET', '');
        $message = json_encode([
            'data' => $payload,
            'timestamp' => (int)$timestamp
        ], JSON_UNESCAPED_SLASHES);
        $expectedSignature = hash_hmac('sha256', $message, $secret);
        if (!hash_equals($expectedSignature, $signature)) {
            return [
                'message' => 'Invalid signature',
                'status' => 400
            ];
        }
        return false;
    }

    protected function shouldBypassSignatureCheck(): bool
    {
        return (bool) $this->getAppSupportValue('skip_check_signature');
    }

    protected function getAppSupportValue(string $key)
    {
        return $this->appSupportModel->getValue($key);
    }

    protected function getCustomEnv(string $key, $default = null)
    {
        if (!$this->isAllowedCustomEnvKey($key)) {
            return $default;
        }

        return env($key, $default);
    }

    protected function getCustomEnvKeys(): array
    {
        return self::CUSTOM_ENV_KEYS;
    }

    private function isAllowedCustomEnvKey(string $key): bool
    {
        return in_array($key, self::CUSTOM_ENV_KEYS, true);
    }

    protected function getLoginUser(?string $key = null)
    {
        $user = $this->request->user ?? null;
        if (!is_array($user)) {
            return null;
        }

        if ($key === null || $key === '') {
            return $user;
        }

        return $user[$key] ?? null;
    }

    protected function apiResponse(int $status, $message = null, $data = null, $pagination = null): ResponseInterface
    {
        $payload = [
            'message' => $message,
        ];
        if ($data !== null) {
            $payload['data'] = $data;
        }
        if ($pagination !== null) {
            $payload['pagination'] = $pagination;
        }
        $responsePayload = $payload;
        $skipResponseLog = $this->shouldSkipApiResponseLog($status);

        if (!$skipResponseLog && in_array($status, [200, 201])) {
            $this->infoLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Response API', ['data' => $payload]);
        } else if (!$skipResponseLog && $status === 500) {
            $this->errorLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Response API', ['data' => $payload]);
            unset($responsePayload['data']);
            unset($responsePayload['pagination']);
        } else if (!$skipResponseLog) {
            $this->warningLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Response API', ['data' => $payload]);
        } else if ($status === 500) {
            unset($responsePayload['data']);
            unset($responsePayload['pagination']);
        }
        return $this->response->setJSON($responsePayload)->setStatusCode($status);
    }

    protected function shouldSkipApiResponseLog(int $status): bool
    {
        if (!str_starts_with((string) $this->RouterCode, 'LOG-')) {
            return false;
        }

        // Untuk API log, semua response tidak di-log kecuali error 500 (tetap dari catch/serverError).
        return $status !== 500;
    }

    protected function responseByStatus(int $status, $message = null, $data = null, $pagination = null): ResponseInterface
    {
        return $this->apiResponse($status, $message, $data, $pagination);
    }

    protected function success($message = null, $data = null, $pagination = null): ResponseInterface
    {
        return $this->apiResponse(200, $message, $data, $pagination);
    }

    protected function created($message = null, $data = null, $pagination = null): ResponseInterface
    {
        return $this->apiResponse(201, $message, $data, $pagination);
    }

    protected function badRequest($message = null, $data = null, $pagination = null): ResponseInterface
    {
        return $this->apiResponse(400, $message, $data, $pagination);
    }

    protected function unauthorized($message = null, $data = null, $pagination = null): ResponseInterface
    {
        return $this->apiResponse(401, $message, $data, $pagination);
    }

    protected function forbidden($message = null, $data = null, $pagination = null): ResponseInterface
    {
        return $this->apiResponse(403, $message, $data, $pagination);
    }

    protected function notFound($message = null, $data = null, $pagination = null): ResponseInterface
    {
        return $this->apiResponse(404, $message, $data, $pagination);
    }

    protected function unprocessable($message = null, $data = null, $pagination = null): ResponseInterface
    {
        return $this->apiResponse(422, $message, $data, $pagination);
    }

    protected function serverError($message = null, $data = null, $pagination = null): ResponseInterface
    {
        return $this->apiResponse(500, $message, $data, $pagination);
    }

    protected function validationError(array $errors): ResponseInterface
    {
        $humanizedErrors = $this->humanizeValidationErrors($errors);
        $firstError = reset($humanizedErrors) ?: 'Input validation failed.';
        $this->warningLog('[' . $this->UUID . '][' . $this->RouterCode . '][' . $this->UserID . '] Response API : Input validation!', ['errors' => $errors]);
        return $this->unprocessable($firstError, $humanizedErrors);
    }

    // ─── Helpers ──────────────────────────────────────────────────────

    /**
     * Get JSON body from request
     */
    protected function getBody(): array
    {
        return (array) $this->request->getJSON(true);
    }

    private function humanizeValidationErrors(array $errors): array
    {
        $result = [];
        foreach ($errors as $field => $message) {
            $result[$field] = $this->humanizeValidationMessage((string) $field, (string) $message);
        }

        return $result;
    }

    private function humanizeValidationMessage(string $field, string $message): string
    {
        $label = $this->humanizeFieldLabel($field);

        if (preg_match('/^The\s+.+?\s+field is required\.?$/i', $message)) {
            return "{$label} is required.";
        }

        if (preg_match('/at least\s+(\d+)\s+characters/i', $message, $m)) {
            return "{$label} must be at least {$m[1]} characters.";
        }

        if (preg_match('/cannot exceed\s+(\d+)\s+characters/i', $message, $m)) {
            return "{$label} cannot exceed {$m[1]} characters.";
        }

        if (str_contains(strtolower($message), 'valid email')) {
            return "{$label} must be a valid email address.";
        }

        if (str_contains(strtolower($message), 'must be an integer')) {
            return "{$label} must be an integer.";
        }

        if (str_contains(strtolower($message), 'is not in the list')) {
            return "{$label} has an invalid value.";
        }

        if (str_contains(strtolower($message), 'must contain a unique value')) {
            return "{$label} is already in use.";
        }

        return $message;
    }

    private function humanizeFieldLabel(string $field): string
    {
        return ucfirst(str_replace('_', ' ', $field));
    }
}
