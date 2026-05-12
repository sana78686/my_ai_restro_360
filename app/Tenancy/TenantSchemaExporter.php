<?php

declare(strict_types=1);

namespace App\Tenancy;

use Illuminate\Support\Facades\DB;

/**
 * Exports a fully migrated tenant database to database/schema/tenant-schema.sql
 * without requiring mysqldump (works on Windows if PHP can connect to MySQL).
 */
final class TenantSchemaExporter
{
    public static function exportTo(string $path): void
    {
        $database = DB::connection('tenant')->getDatabaseName();
        if (! is_string($database) || $database === '') {
            throw new \RuntimeException('Tenant database name is not set. Initialize tenancy first.');
        }

        $tables = DB::connection('tenant')->select(
            'SELECT TABLE_NAME AS n FROM information_schema.tables WHERE table_schema = ? ORDER BY TABLE_NAME',
            [$database]
        );

        $lines = [
            '/* Tenant baseline schema — regenerate with: php artisan tenancy:dump-tenant-schema */',
            'SET NAMES utf8mb4;',
            'SET FOREIGN_KEY_CHECKS=0;',
            'SET SESSION sql_mode = "NO_ENGINE_SUBSTITUTION";',
            '',
        ];

        foreach ($tables as $row) {
            $name = $row->n;
            if ($name === 'migrations') {
                continue;
            }
            $create = DB::connection('tenant')->selectOne('SHOW CREATE TABLE `'.str_replace('`', '``', $name).'`');
            $ddl = $create->{'Create Table'} ?? null;
            if (! is_string($ddl)) {
                continue;
            }
            $lines[] = 'DROP TABLE IF EXISTS `'.str_replace('`', '``', $name).'`;';
            $lines[] = $ddl.';';
            $lines[] = '';
        }

        // migrations: structure + data (so migrate sees prior runs)
        $mCreate = DB::connection('tenant')->selectOne('SHOW CREATE TABLE `migrations`');
        $mDdl = $mCreate->{'Create Table'} ?? null;
        if (is_string($mDdl)) {
            $lines[] = 'DROP TABLE IF EXISTS `migrations`;';
            $lines[] = $mDdl.';';
            $lines[] = '';
            $rows = DB::connection('tenant')->table('migrations')->orderBy('id')->get();
            foreach ($rows as $r) {
                $migration = str_replace(["\\", "'"], ["\\\\", "\\'"], (string) $r->migration);
                $batch = (int) $r->batch;
                $id = isset($r->id) ? (int) $r->id : 0;
                if ($id > 0) {
                    $lines[] = "INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ({$id}, '{$migration}', {$batch});";
                } else {
                    $lines[] = "INSERT INTO `migrations` (`migration`, `batch`) VALUES ('{$migration}', {$batch});";
                }
            }
            $lines[] = '';
        }

        $lines[] = 'SET FOREIGN_KEY_CHECKS=1;';

        $dir = dirname($path);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($path, implode("\n", $lines));
    }
}
