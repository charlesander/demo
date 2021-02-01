<?php

namespace Tests;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class TestCase extends TestCase
{
    const EXIGO_DB_CONNECTION = 'exigo';

    const EXIGO_SANDBOX_DB_CONNECTION = 'exigoSandbox';

    const OBSERVER_DB_CONNECTION = 'observer';

    /**
     * If true, setup has run at least once.
     * @var boolean
     */
    protected static $setUpHasRunOnce = false;

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setup();

        if (!static::$setUpHasRunOnce) {
            Artisan::call('config:cache --env=testing');
            Artisan::call('migrate:fresh');
            Artisan::call('migrate:fresh', ['--path' => 'database/migrations/exigo']);
            Artisan::call('migrate:fresh', ['--path' => 'database/migrations/observer']);
            Artisan::call('db:seed');
            static::$setUpHasRunOnce = true;
        }
    }

    protected function truncateTable(string $modelClass): void
    {
        if (!class_exists($modelClass)) {
            throw new ModelNotFoundException($modelClass . ' not found.');
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $modelClass::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

}
