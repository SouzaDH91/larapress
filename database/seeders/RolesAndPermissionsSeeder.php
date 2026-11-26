<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\Option;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

final class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Granular Capabilities / Permissions
        $permissions = [
            // Posts & Pages
            'edit_posts',
            'edit_others_posts',
            'edit_published_posts',
            'publish_posts',
            'delete_posts',
            'delete_others_posts',
            'delete_published_posts',
            'read_private_posts',
            'edit_pages',
            'edit_others_pages',
            'edit_published_pages',
            'publish_pages',
            'delete_pages',
            'delete_others_pages',
            'delete_published_pages',

            // Media
            'upload_files',
            'edit_files',
            'delete_files',

            // Taxonomies
            'manage_categories',

            // Comments
            'moderate_comments',

            // Themes, Plugins, Options & Users
            'switch_themes',
            'edit_theme_options',
            'activate_plugins',
            'edit_plugins',
            'install_plugins',
            'manage_options',
            'list_users',
            'promote_users',
            'create_users',
            'edit_users',
            'delete_users',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        // 2. Define Roles
        // Subscriber
        $subscriber = Role::firstOrCreate(['name' => 'subscriber', 'guard_name' => 'web']);
        // Subscriber has basic read permissions

        // Contributor
        $contributor = Role::firstOrCreate(['name' => 'contributor', 'guard_name' => 'web']);
        $contributor->syncPermissions([
            'edit_posts',
            'delete_posts',
        ]);

        // Author
        $author = Role::firstOrCreate(['name' => 'author', 'guard_name' => 'web']);
        $author->syncPermissions([
            'edit_posts',
            'edit_published_posts',
            'publish_posts',
            'delete_posts',
            'delete_published_posts',
            'upload_files',
        ]);

        // Editor
        $editor = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $editor->syncPermissions([
            'edit_posts',
            'edit_others_posts',
            'edit_published_posts',
            'publish_posts',
            'delete_posts',
            'delete_others_posts',
            'delete_published_posts',
            'read_private_posts',
            'edit_pages',
            'edit_others_pages',
            'edit_published_pages',
            'publish_pages',
            'delete_pages',
            'delete_others_pages',
            'delete_published_pages',
            'upload_files',
            'edit_files',
            'delete_files',
            'manage_categories',
            'moderate_comments',
        ]);

        // Administrator
        $adminRole = Role::firstOrCreate(['name' => 'administrator', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        // 3. Create Default Administrator User
        $admin = User::firstOrCreate(
            ['email' => 'admin@larapress.test'],
            [
                'name' => 'Super Administrator',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'bio' => 'LaraPress System Master Administrator',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $admin->assignRole($adminRole);

        // 4. Default System Options
        $defaultOptions = [
            'site_name' => 'LaraPress CMS',
            'site_description' => 'Modern, Secure, High-Performance Laravel Content Management System',
            'site_url' => config('app.url', 'http://localhost'),
            'admin_email' => 'admin@larapress.test',
            'timezone' => 'America/Sao_Paulo',
            'date_format' => 'd/m/Y',
            'time_format' => 'H:i',
            'permalink_structure' => '/%category%/%postname%/',
            'active_theme' => 'laratwentysix',
            'active_plugins' => [],
            'comments_moderation' => true,
            'default_comment_status' => 'open',
        ];

        foreach ($defaultOptions as $key => $val) {
            Option::firstOrCreate(
                ['option_name' => $key],
                [
                    'option_value' => is_array($val) ? json_encode($val) : (string) $val,
                    'autoload' => true,
                ]
            );
        }
    }
}
