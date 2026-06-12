<?php

namespace App\Models;

use CodeIgniter\Model;

class BaseModel extends Model
{
    public $LIMIT_DATA = 20;

    public function generateIdItem(?string $prefix = null): string
    {
        $finalPrefix = $prefix !== null && $prefix !== ''
            ? strtoupper($prefix)
            : $this->buildPrefixFromTable();

        return $finalPrefix . round(microtime(true) * 1000);
    }

    private function buildPrefixFromTable(): string
    {
        $tableName = (string) ($this->table ?? '');
        if ($tableName === '') {
            return '';
        }

        $parts = array_filter(explode('_', $tableName));
        $prefix = '';
        foreach ($parts as $part) {
            $prefix .= strtoupper(substr($part, 0, 1));
        }

        return $prefix;
    }
}
