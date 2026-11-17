<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Enums\StatusUnit;
use App\Models\KontrakSewa;
use App\Models\Lokasi;
use App\Models\Penyewa;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KontrakanTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => Role::Admin])->save();

        return $user;
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/admin/unit')->assertRedirect('/login');
    }

    public function test_penyewa_cannot_open_admin_modules(): void
    {
        $penyewa = User::factory()->create();

        $this->actingAs($penyewa)->get('/admin/lokasi')->assertForbidden();
        $this->actingAs($penyewa)->get('/admin/unit')->assertForbidden();
        $this->actingAs($penyewa)->post('/admin/lokasi', ['nama' => 'X', 'alamat' => 'Y'])->assertForbidden();
        $this->assertDatabaseCount('lokasi', 0);
    }

    public function test_dashboards_render_with_seeded_data(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::where('email', 'admin@kontrakan.test')->first();
        $this->actingAs($admin)->get('/dashboard')
            ->assertOk()
            ->assertSee('Dashboard Pemilik')
            ->assertSee('wa.me/62', false);

        $penyewa = User::where('email', 'penyewa@kontrakan.test')->first();
        $this->actingAs($penyewa)->get('/dashboard')
            ->assertOk()
            ->assertSee('Dashboard Penyewa')
            ->assertSee('U-01');
    }

    public function test_login_with_whatsapp_number(): void
    {
        User::factory()->create(['no_whatsapp' => '081211112222']);

        $this->post('/login', ['email' => '0812-1111-2222', 'password' => 'password']);

        $this->assertAuthenticated();
    }

    public function test_login_with_garbage_identifier_does_not_match_users_without_whatsapp(): void
    {
        User::factory()->create(['no_whatsapp' => null]);

        $this->post('/login', ['email' => 'bukan-email', 'password' => 'password']);

        $this->assertGuest();
    }

    public function test_active_contract_survives_newer_pending_contract(): void
    {
        $lokasi = Lokasi::create(['nama' => 'L', 'alamat' => 'A']);
        $unit = Unit::create(['lokasi_id' => $lokasi->id, 'nomor_unit' => 'X-1', 'tarif_bulanan' => 1, 'status' => 'terisi']);
        $a = Penyewa::create(['nama' => 'A', 'no_whatsapp' => '081211110001']);
        $b = Penyewa::create(['nama' => 'B', 'no_whatsapp' => '081211110002']);

        $aktif = KontrakSewa::create([
            'unit_id' => $unit->id, 'penyewa_id' => $a->id, 'periode' => 'bulanan',
            'tanggal_mulai' => '2026-06-05', 'tanggal_selesai' => '2027-06-04', 'tarif' => 1, 'status' => 'aktif',
        ]);
        KontrakSewa::create([
            'unit_id' => $unit->id, 'penyewa_id' => $b->id, 'periode' => 'bulanan',
            'tanggal_mulai' => '2027-06-05', 'tanggal_selesai' => '2028-06-04', 'tarif' => 1, 'status' => 'diajukan',
        ]);

        $this->assertTrue($unit->kontrakAktif->is($aktif));
        $this->assertTrue(Unit::with('kontrakAktif')->find($unit->id)->kontrakAktif->is($aktif));
    }

    public function test_login_throttle_shares_bucket_across_whatsapp_spellings(): void
    {
        User::factory()->create(['no_whatsapp' => '081233334444']);
        $spellings = ['081233334444', '0812-3333-4444', '+6281233334444', 'a081233334444', '62 812 3333 4444'];

        foreach ($spellings as $s) {
            $this->post('/login', ['email' => $s, 'password' => 'salah']);
        }

        // Percobaan ke-6 dengan kata sandi benar tetap ditolak karena kena batas.
        $this->post('/login', ['email' => '0812 3333 4444', 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_account_cannot_be_deleted(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->delete('/profile', ['password' => 'password'])->assertForbidden();
        $this->assertModelExists($admin);
    }

    public function test_whatsapp_validation_checks_digits_not_separators(): void
    {
        $base = ['name' => 'N', 'password' => 'password', 'password_confirmation' => 'password'];

        $this->post('/register', $base + ['email' => 'a@example.com', 'no_whatsapp' => '08-------'])
            ->assertSessionHasErrors('no_whatsapp');

        $this->post('/register', $base + ['email' => 'b@example.com', 'no_whatsapp' => '+62 812-3456-7890'])
            ->assertSessionHasNoErrors();
        $this->assertSame('6281234567890', User::firstWhere('email', 'b@example.com')->no_whatsapp);
    }

    public function test_admin_can_crud_lokasi_and_unit(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/lokasi', [
            'nama' => 'Lokasi Uji',
            'alamat' => 'Jl. Uji No. 1',
            'latitude' => '-6.3',
            'longitude' => '107.3',
        ])->assertRedirect('/admin/lokasi');

        $lokasi = Lokasi::firstWhere('nama', 'Lokasi Uji');
        $this->assertStringContainsString('-6.3,107.3', $lokasi->mapsUrl());

        $this->actingAs($admin)->post('/admin/unit', [
            'lokasi_id' => $lokasi->id,
            'nomor_unit' => 'T-01',
            'tarif_bulanan' => 900000,
            'jumlah_kamar' => 2,
            'status' => 'kosong',
            'izin_hewan' => '1',
        ])->assertSessionHasNoErrors();

        $unit = Unit::firstWhere('nomor_unit', 'T-01');
        $this->assertTrue($unit->izin_hewan);

        $this->actingAs($admin)->put("/admin/unit/{$unit->id}", [
            'lokasi_id' => $lokasi->id,
            'nomor_unit' => 'T-01',
            'tarif_bulanan' => 950000,
            'jumlah_kamar' => 2,
            'status' => 'perbaikan',
            'izin_hewan' => '0',
        ])->assertSessionHasNoErrors();

        $unit->refresh();
        $this->assertSame(950000, $unit->tarif_bulanan);
        $this->assertSame(StatusUnit::Perbaikan, $unit->status);
        $this->assertFalse($unit->izin_hewan);

        // Lokasi yang masih punya unit tidak boleh dihapus.
        $this->actingAs($admin)->delete("/admin/lokasi/{$lokasi->id}");
        $this->assertModelExists($lokasi);

        $this->actingAs($admin)->delete("/admin/unit/{$unit->id}")->assertRedirect('/admin/unit');
        $this->actingAs($admin)->delete("/admin/lokasi/{$lokasi->id}")->assertRedirect('/admin/lokasi');
        $this->assertModelMissing($lokasi);
    }
}
