<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // ক্যাশ রিসেট (permission:cache-reset কমান্ডের সমতুল্য, সিডিং-এর আগে জরুরি)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ============================
        // ১. সব পারমিশন তৈরি
        // ============================
        $permissions = [
            // Worker Profile
            'view workers',
            'create workers',
            'edit workers',
            'delete workers',
            'upload worker documents',

            // Job Categories
            'view job categories',
            'manage job categories',

            // Job Order / Demand Management
            'view job orders',
            'create job orders',
            'edit job orders',
            'delete job orders',
            'manage shortlisting',
            'update worker interest status',
            'export worker cvs',

            // Placement
            'view placements',
            'create placements',
            'edit placements',
            'delete placements',

            // Sensitive financial fields
            'view billing rates',
            'edit billing rates',

            // Payroll / Billing
            'manage monthly payroll',
            'generate invoices',
            'generate salary slips',

            // Dashboard
            'view financial dashboard',

            // Kafala (Medina office specific)
            'manage kafala transfers',

            // User management
            'manage users',
            'manage roles',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ============================
        // ২. রোল তৈরি + পারমিশন এসাইন
        // ============================

        // ---- Super Admin: সব পারমিশন ----
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        // ---- Manager ----
        $manager = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $manager->syncPermissions([
            'view workers', 'create workers', 'edit workers',
            'upload worker documents',
            'view job categories',
            'view job orders', 'create job orders', 'edit job orders',
            'manage shortlisting', 'update worker interest status', 'export worker cvs',
            'view placements', 'create placements', 'edit placements',
            // মার্জিন/প্রফিট রিপোর্ট সীমিত — billing rate পারমিশন এখানে নেই
            'manage monthly payroll',
        ]);

        // ---- Office Staff (Data Entry) ----
        $officeStaff = Role::firstOrCreate(['name' => 'Office Staff', 'guard_name' => 'web']);
        $officeStaff->syncPermissions([
            'view workers', 'create workers', 'edit workers',
            'upload worker documents',
            'view job categories',
            'view job orders',
            'manage shortlisting', 'update worker interest status', 'export worker cvs',
            // rate/margin/profit কিছুই নেই
        ]);

        // ---- Accounts/Finance Staff ----
        $accounts = Role::firstOrCreate(['name' => 'Accounts Staff', 'guard_name' => 'web']);
        $accounts->syncPermissions([
            'view workers', // দেখতে পারবে কিন্তু এডিট করতে পারবে না (Resource পলিসিতে নিয়ন্ত্রণ হবে)
            'view job orders',
            'view placements', 'create placements', 'edit placements',
            'view billing rates', 'edit billing rates',
            'manage monthly payroll',
            'generate invoices', 'generate salary slips',
            'view financial dashboard',
        ]);

        // ---- Medina Office Staff ----
        $medinaStaff = Role::firstOrCreate(['name' => 'Medina Office Staff', 'guard_name' => 'web']);
        $medinaStaff->syncPermissions([
            'view workers', 'edit workers', // কাফালা ট্রান্সফার সংক্রান্ত ফিল্ড আপডেট
            'upload worker documents',
            'view job orders',
            'update worker interest status',
            'manage kafala transfers',
        ]);
    }
}