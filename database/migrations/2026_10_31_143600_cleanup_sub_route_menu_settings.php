<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Remove sub-page routes (e.g. sales.create, sales-quotations.create) that were
     * accidentally registered as standalone menus via "Sync dari Routes".
     * These pages now inherit access from their parent menu (sales, sales-quotations).
     */
    public function up(): void
    {
        $suffixes = ['create', 'edit', 'show', 'view', 'detail', 'pdf', 'stream', 'print', 'download', 'export'];

        DB::table('menu_settings')
            ->where(function ($q) use ($suffixes) {
                foreach ($suffixes as $suffix) {
                    $q->orWhere('route_name', 'like', '%.' . $suffix);
                }
            })
            ->delete();
    }

    public function down(): void
    {
        // Irreversible cleanup; sub-routes are no longer managed as standalone menus.
    }
};
