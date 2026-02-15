<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPanelAccessSeeder extends Seeder
{
    /**
     * Seed panel-level roles and default super admin assignment.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $roles = [
            'super_admin',
            'admin',
            'hotel_manager',
            'ferry_manager',
            'ride_manager',
            'game_manager',
            'user',
        ];

        foreach ($roles as $role) {
            Role::findOrCreate($role, 'web');
        }

        $superAdmin = User::query()
            ->where('email', 'test@admin.com')
            ->first();

        if ($superAdmin) {
            $superAdmin->syncRoles(['super_admin']);
        }
    }
}
