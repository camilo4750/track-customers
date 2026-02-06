<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        if (! class_exists(Role::class)) {
            return;
        }

        $roles = ['admin', 'user'];

        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'api',
            ]);
        }
    }

    public function down(): void
    {
        if (! class_exists(Role::class)) {
            return;
        }

        Role::whereIn('name', ['admin', 'user'])
            ->where('guard_name', 'api')
            ->delete();
    }
};
