<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $guard = 'api';
        $permissions = [
            'User.show',
            'User.create',
            'User.edit',
            'User.delete',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => $guard],
                ['name' => $name, 'guard_name' => $guard]
            );
        }   
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Permission::where('guard_name', 'api')
            ->whereIn('name', ['User.show', 'User.create', 'User.edit', 'User.delete'])
            ->delete();
    }
};
