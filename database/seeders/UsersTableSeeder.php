<?php 
namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the user if it doesn't exist
        $user = \App\Models\User::updateOrCreate(
            ['email' => 'super_admin@app.com'], // Unique field
            [
                'first_name' => 'super',
                'last_name' => 'admin',
                'password' => bcrypt('123456')
            ]
        );

        // Retrieve the role
        $role = Role::where('name', 'super_admin')->first();

        // Check if the user already has the role before attaching
        if (!$user->hasRole('super_admin')) {
            // Attach the role to the user
            $user->attachRole($role);
        }

        // Get all existing permissions
        $permissions = Permission::all();

        // Attach each permission to the role if not already attached
        foreach ($permissions as $permission) {
            // Check if the permission is already attached
            if (!$role->hasPermission($permission->name)) {
                // Attach the permission to the role
                $role->attachPermission($permission);
                $this->command->info("Attached permission: " . $permission->name); // Output to console
            }

            if (!$user->hasPermission($permission->name)) {
                $user->attachPermission($permission);  // This will populate the `permission_user` table
                $this->command->info("Attached permission to user: " . $permission->name);
            }
        }
    }
}
