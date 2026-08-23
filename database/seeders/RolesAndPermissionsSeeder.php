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
        // 1. إعادة إرسال وتفريغ الـ Cache الخاص بالصلاحيات
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. تعريف قائمة الصلاحيات الشاملة للنظام
        $permissions = [
            // إدارة المستخدمين والأدوار (المدير فقط)
            'view-users', 'create-users', 'edit-users', 'delete-users',
            'view-roles', 'create-roles', 'edit-roles', 'delete-roles',

            // إدارة الأصناف والمنتجات والواجهات (المدير + الموظف)
            'view-products', 'create-products', 'edit-products', 'delete-products',
            'view-categories', 'create-categories', 'edit-categories', 'delete-categories',
            'manage-banners', 'manage-settings', 'manage-features', 'manage-partners','manage-wallets',
            'permission:manage-wallets'<

            // إدارة جميع الطلبات (المدير + الموظف)
            'view-all-orders', 'edit-all-orders', 'delete-all-orders',

            // صلاحيات العميل المحلي
            'view-local-orders',
            'create-local-orders',
            'manage-local-profile',

            // صلاحيات العميل الدولي
            'view-intl-orders',
            'create-intl-orders',
            'view-shipping-documents',
            'manage-intl-profile',
            'view-intl-notifications',
        ];

        // 3. إنشاء الصلاحيات في قاعدة البيانات باستخدام guard الـ sanctum
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'sanctum');
        }

        // 4. إنشاء وتعيين الأدوار الأربعة:

        // أ) مدير النظام (Admin): يملك كافة الصلاحيات
        $adminRole = Role::findOrCreate('admin', 'sanctum');
        $adminRole->givePermissionTo(Permission::all());

        // ب) الموظف (Employee): يعرض ويتحكم بالطلبات، المنتجات، والأصناف فقط
        $employeeRole = Role::findOrCreate('employee', 'sanctum');
        $employeeRole->givePermissionTo([
            'view-products', 'create-products', 'edit-products', 'delete-products',
            'view-categories', 'create-categories', 'edit-categories', 'delete-categories',
            'view-all-orders', 'edit-all-orders', 'delete-all-orders',
        ]);

        // ج) العميل المحلي (Local Client): بروفايل وطلبات محلية
        $localClientRole = Role::findOrCreate('local-client', 'sanctum');
        $localClientRole->givePermissionTo([
            'view-local-orders',
            'create-local-orders',
            'manage-local-profile',
        ]);

        // د) العميل الدولي (International Client): بروفايل، طلبات دولية، مستندات، وإشعارات
        $intlClientRole = Role::findOrCreate('international-client', 'sanctum');
        $intlClientRole->givePermissionTo([
            'view-intl-orders',
            'create-intl-orders',
            'view-shipping-documents',
            'manage-intl-profile',
            'view-intl-notifications',
        ]);
    }
}
