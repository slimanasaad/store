<?php

namespace Database\Seeders;

use App\Models\Center;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CenterSeeder extends Seeder
{
    public function run(): void
    {
        $centers = [
            ['name' => 'مركز المدينة'],
            ['name' => 'مركز الشفاء'],
        ];

        foreach ($centers as $centerData) {
            // إنشاء المركز
            $center = Center::create($centerData);

            // إنشاء المدير العام
            $admin = User::create([
                'name' => 'مدير ' . $center->name,
                'email' => 'admin_' . $center->id . '@example.com',
                'password' => Hash::make('password'),
                'center_id' => $center->id,
                'is_center_admin' => true,
            ]);

            // إنشاء الصلاحيات الخاصة بالمركز
            $permissions = ['view_inventory', 'edit_inventory', 'view_sales', 'manage_employees'];

            $permissionModels = [];
            foreach ($permissions as $perm) {
                $permissionModels[] = Permission::firstOrCreate([
                    'name' => $perm,
                    'guard_name' => 'web',
                    'center_id' => $center->id,
                ]);
            }

            // إنشاء دور المدير وربطه بكل الصلاحيات
            $adminRole = Role::create([
                'name' => 'center_admin',
                'guard_name' => 'web',
                'center_id' => $center->id,
            ]);

            $adminRole->syncPermissions($permissionModels);
            $admin->assignRole($adminRole);

            // إنشاء دور الموظف بصلاحيات محددة
            $employeeRole = Role::create([
                'name' => 'employee',
                'guard_name' => 'web',
                'center_id' => $center->id,
            ]);

            $employeePermissions = Permission::where('center_id', $center->id)
                ->whereIn('name', ['view_inventory', 'view_sales'])
                ->get();

            $employeeRole->syncPermissions($employeePermissions);

            // إنشاء موظفين وربطهم بالدور
            for ($i = 1; $i <= 2; $i++) {
                $employee = User::create([
                    'name' => "موظف {$i} - " . $center->name,
                    'email' => "employee{$i}_center{$center->id}@example.com",
                    'password' => Hash::make('password'),
                    'center_id' => $center->id,
                    'is_center_admin' => false,
                ]);

                $employee->assignRole($employeeRole);
            }
        }
    }
}
