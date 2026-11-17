<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Akun demo (khusus pengembangan lokal):
     *   Pemilik : admin@kontrakan.test / password
     *   Penyewa : penyewa@kontrakan.test / password  (atau login pakai 081200000001)
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Tri Suci Handayani',
            'email' => 'admin@kontrakan.test',
            'no_whatsapp' => '081100000000',
        ]);
        $admin->forceFill(['role' => Role::Admin])->save();

        $this->call(KontrakanSeeder::class);
    }
}
