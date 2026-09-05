<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. إعادة ضبط وتفريغ كاش الصلاحيات
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = 'sanctum';

        // 2. قائمة الصلاحيات
        $permissions = [
            // إدارة المستخدمين والأدوار
            'view-users', 'create-users', 'update-users', 'delete-users',
            'view-roles', 'create-roles', 'update-roles', 'delete-roles',

            // تبويب التصنيفات
            'view-categories', 'create-categories', 'update-categories', 'delete-categories',

            // تبويب المنتجات وملحقاتها
            'view-products', 'create-products', 'update-products', 'delete-products',
            'manage-product-images', 'manage-product-units',

            // تبويب الطلبات المحلية
            'view-orders', 'update-orders-status', 'cancel-orders', 'delete-orders',

            // تبويب طلبات الاستيراد (محجوب تماماً عن الموظف والعميل المحلي)
            'view-import-requests', 'create-import-requests', 'manage-import-requests', 'review-import-requests',

            // المحتوى المساعد والإعدادات
            'manage-banners', 'manage-features', 'manage-partners', 
            'manage-settings', 'manage-wallets', 'manage-reviews',
        ];

        // إنشاؤها بـ guard = sanctum
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, $guard);
        }

        // 3. دور المدير (admin) - كافة الصلاحيات
        $adminRole = Role::findOrCreate('admin', $guard);
        $adminRole->syncPermissions(Permission::where('guard_name', $guard)->get());

        // 4. دور الموظف (employee) - التصنيفات، المنتجات، الطلبات المحلية، المحتوى
        $employeeRole = Role::findOrCreate('employee', $guard);
        $employeeRole->syncPermissions([
            // التصنيفات
            'view-categories', 'create-categories', 'update-categories', 'delete-categories',
            // المنتجات وملحقاتها
            'view-products', 'create-products', 'update-products', 'delete-products',
            'manage-product-images', 'manage-product-units',
            // الطلبات المحلية
            'view-orders', 'update-orders-status',
            // الملحقات
            'manage-banners', 'manage-features', 'manage-partners', 'manage-reviews',
        ]);

        // 5. دور العميل المحلي (local-client)
        Role::findOrCreate('local-client', $guard);

        // 6. دور العميل المستورد (import-client)
        $importClientRole = Role::findOrCreate('import-client', $guard);
        $importClientRole->syncPermissions([
            'view-import-requests',
            'create-import-requests',
        ]);
    }
}
