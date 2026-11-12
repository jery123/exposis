<?php
namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (! is_string($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \RuntimeException('Set a valid ADMIN_EMAIL before seeding the EXPOSIS administrator.');
        }

        if (! is_string($password) || strlen($password) < 12) {
            throw new \RuntimeException('Set an ADMIN_PASSWORD with at least 12 characters before seeding the EXPOSIS administrator.');
        }

        User::updateOrCreate(['email' => $email], [
            'name' => 'EXPOSIS',
            'organization_name' => 'EXPOSIS',
            'password' => Hash::make($password),
            'role' => 'admin',
            'role_id' => Role::where('slug', 'admin')->value('id'),
            'email_verified_at' => now(),
        ]);
    }
}
