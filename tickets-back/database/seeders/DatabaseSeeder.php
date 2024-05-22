<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Status;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        //Create Status
        Status::create([
            'name' => 'Pending',
            'description' => 'The ticket is open',
        ]);

        Status::create([
            'name' => 'In Progress',
            'description' => 'The ticket is in progress',
        ]);

        Status::create([
            'name' => 'Completed',
            'description' => 'The ticket is closed',
        ]);

        //Create Users
        $user_admin = User::create([
            'identification' => '144048955',
            'first_name' => 'admin',
            'last_name' => 'ticket',
            'email' => 'admin@ticket.com',
            'password' => bcrypt('admin'),
        ]);

        $user_guest = User::create([
            'identification' => '11448975',
            'first_name' => 'guest',
            'last_name' => 'ticket',
            'email' => 'guest@ticket.com',
            'password' => bcrypt('guest'),
        ]);

        //Create Roles
        $admin = Role::create(['name' => 'admin']);
        $guest = Role::create(['name' => 'guest']);

        //Assign Roles
        $user_admin->assignRole($admin);
        $user_guest->assignRole($guest);
    }
}
