<?php

declare(strict_types=1);

namespace App\Tenancy;

use Illuminate\Support\Facades\DB;

/**
 * Loads database/schema/tenant-schema.sql into the current tenant MySQL connection using PDO
 * (no mysql CLI required — fixes slow Windows provisioning and servers without mysql in PATH).
 */
final class TenantSchemaBaselineLoader
{
    public static function path(): string
    {
        return database_path('schema/tenant-schema.sql');
    }

    public static function exists(): bool
    {
        return is_file(self::path()) && filesize(self::path()) > 100;
    }

    /**
     * Execute a mysqldump-style SQL file (may contain multiple statements, triggers, etc.).
     */
    public static function loadFile(string $path): void
    {
        $sql = file_get_contents($path);
        if ($sql === false || $sql === '') {
            throw new \InvalidArgumentException("Cannot read schema file: {$path}");
        }

        $pdo = DB::connection('tenant')->getPdo();
        if (defined('PDO::MYSQL_ATTR_MULTI_STATEMENTS')) {
            $pdo->setAttribute(\PDO::MYSQL_ATTR_MULTI_STATEMENTS, true);
        }

        $pdo->exec($sql);
    }
}
