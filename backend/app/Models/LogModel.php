<?php

namespace App\Models;

use CodeIgniter\Model;

class LogModel extends BaseModel
{
    protected $table = 'app_logs';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'level', 
        'message',
        'context', 
        'ip_address',
    ];
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    public function info($message, $context = [], $ip_address = null)
    {
        $params = [
            'level' => 'info',
            'message' => $message,
            'context' => json_encode($context),
            'ip_address' => $ip_address
        ];
        $this->insert($params);
    }
    public function warning($message, $context = [], $ip_address = null)
    {
        $params = [
            'level' => 'warning',
            'message' => $message,
            'context' => json_encode($context),
            'ip_address' => $ip_address
        ];
        $this->insert($params);
    }
    public function error($message, $context = [], $ip_address = null)
    {
        $params = [
            'level' => 'error',
            'message' => $message,
            'context' => json_encode($context),
            'ip_address' => $ip_address
        ];
        $this->insert($params);
    }
    public function getLogPaginated(
        int $page,
        int $pageSize,
        string $level = '',
        string $keywords = '',
        string $date = '',
        string $startTime = '',
        string $endTime = '',
    ): array {
        $offset = max(0, ($page - 1) * $pageSize);

        $baseBuilder = $this->db->table('app_logs');
        $this->applyLogFilters($baseBuilder, $level, $keywords, $date, $startTime, $endTime);

        $totalItems = (int) $baseBuilder->countAllResults(false);
        $items = $baseBuilder
            ->select('id, level, message, context, ip_address, created_at, updated_at')
            ->orderBy('id', 'DESC')
            ->get($pageSize, $offset)
            ->getResultArray();

        return [
            'items' => $items,
            'total_items' => $totalItems,
        ];
    }

    public function clearFilteredLogs(
        string $level = '',
        string $keywords = '',
        string $date = '',
        string $startTime = '',
        string $endTime = '',
    ): int {
        $builder = $this->db->table('app_logs');
        $this->applyLogFilters($builder, $level, $keywords, $date, $startTime, $endTime);
        $builder->delete();

        return $this->db->affectedRows();
    }

    private function applyLogFilters(
        $builder,
        string $level = '',
        string $keywords = '',
        string $date = '',
        string $startTime = '',
        string $endTime = '',
    ): void {
        if ($level !== '') {
            $builder->where('level', $level);
        }

        if ($keywords !== '') {
            $builder->groupStart()
                ->like('message', $keywords)
                ->orLike('context', $keywords)
                ->orLike('ip_address', $keywords)
                ->groupEnd();
        }

        $startDateTime = $this->buildBoundaryDateTime($date, $startTime, true);
        $endDateTime = $this->buildBoundaryDateTime($date, $endTime, false);

        if ($startDateTime !== null) {
            $builder->where('created_at >=', $startDateTime);
        }

        if ($endDateTime !== null) {
            $builder->where('created_at <=', $endDateTime);
        }
    }

    private function buildBoundaryDateTime(string $date, string $time, bool $isStart): ?string
    {
        if ($date === '') {
            return null;
        }

        $normalizedTime = trim($time);
        if ($normalizedTime === '') {
            $normalizedTime = $isStart ? '00:00' : '23:59';
        }

        return $date . ' ' . $normalizedTime . ($isStart ? ':00' : ':59');
    }
}
