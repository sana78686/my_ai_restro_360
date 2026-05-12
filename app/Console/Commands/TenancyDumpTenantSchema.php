<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Tenancy\TenantSchemaExporter;
use Illuminate\Console\Command;

/**
 * Writes database/schema/tenant-schema.sql from a fully migrated tenant (PHP export, no mysqldump).
 * Commit this file so new tenant provisioning loads the baseline in seconds.
 */
class TenancyDumpTenantSchema extends Command
{
    protected $signature = 'tenancy:dump-tenant-schema {tenant? : Central tenant id}';

    protected $description = 'Export tenant DB schema + migration rows to database/schema/tenant-schema.sql (speeds up new tenants)';

    public function handle(): int
    {
        $id = $this->argument('tenant');
        $tenant = $id
            ? Tenant::query()->find($id)
            : Tenant::query()->orderBy('id')->first();

        if (! $tenant) {
            $this->error('No tenant found. Create a tenant or pass a valid tenant id.');

            return self::FAILURE;
        }

        $path = database_path('schema/tenant-schema.sql');

        $this->info("Using tenant: {$tenant->id}");
        $this->warn('This tenant DB must already have all tenant migrations applied (e.g. tenants:migrate).');

        tenancy()->initialize($tenant);

        try {
            TenantSchemaExporter::exportTo($path);
        } finally {
            tenancy()->end();
        }

        if (is_file($path)) {
            $kb = round(filesize($path) / 1024, 1);
            $this->info("Wrote {$path} ({$kb} KB). Commit this file for fast new-tenant setup.");
        }

        return self::SUCCESS;
    }
}
