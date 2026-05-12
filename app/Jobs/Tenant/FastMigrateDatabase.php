<?php

declare(strict_types=1);

namespace App\Jobs\Tenant;

use App\Tenancy\TenantSchemaBaselineLoader;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Stancl\Tenancy\Contracts\TenantWithDatabase;

/**
 * Loads a pre-built SQL baseline (when present) then runs tenants:migrate for any newer migrations.
 * Keeps the same provisioning flow as Stancl MigrateDatabase, but cuts time from minutes to seconds.
 */
class FastMigrateDatabase implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected TenantWithDatabase $tenant
    ) {}

    public function handle(): void
    {
        $key = $this->tenant->getTenantKey();

        if (TenantSchemaBaselineLoader::exists()) {
            tenancy()->initialize($this->tenant);
            try {
                TenantSchemaBaselineLoader::loadFile(TenantSchemaBaselineLoader::path());
            } finally {
                tenancy()->end();
            }
        }

        Artisan::call('tenants:migrate', [
            '--tenants' => [$key],
        ]);
    }
}
