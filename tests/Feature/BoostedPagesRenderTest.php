<?php

namespace Tests\Feature;

use App\Models\Intercesion;
use App\Models\PrayerRequest;
use App\Models\Relationship;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BoostedPagesRenderTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('Admin');

        Permission::findOrCreate('Reportes.view');

        $this->user = User::create([
            'fullname' => 'Pastor de Prueba',
            'email' => 'pastor@test.com',
            'born_date' => '1990-01-01',
            'sex' => 'M',
            'phone' => '5491122334455',
            'doc_number' => '12345678',
            'password' => Hash::make('secret'),
        ]);

        $this->user->assignRole('Admin');
        $this->user->givePermissionTo('Reportes.view');

        $disciple = User::create([
            'fullname' => 'Discípulo Uno',
            'email' => 'discipulo1@test.com',
            'born_date' => '1995-05-05',
            'sex' => 'M',
            'phone' => '5491122334444',
            'doc_number' => '87654321',
            'password' => Hash::make('secret'),
        ]);

        Relationship::create([
            'mentor_id' => $this->user->id,
            'disciple_id' => $disciple->id,
            'f_meet' => now(),
        ]);

        Relationship::create([
            'mentor_id' => $this->user->id,
            'disciple_id' => $disciple->id,
            'f_meet' => now()->subWeek(),
        ]);

        $grandchild = User::create([
            'fullname' => 'Discípulo Dos',
            'email' => 'discipulo2@test.com',
            'born_date' => '2000-01-01',
            'sex' => 'M',
            'phone' => '5491122334333',
            'doc_number' => '11112222',
            'password' => Hash::make('secret'),
        ]);

        Relationship::create([
            'mentor_id' => $disciple->id,
            'disciple_id' => $grandchild->id,
            'f_meet' => now(),
        ]);

        Intercesion::create([
            'calendar_day' => 1,
            'email' => 'intercesor@test.com',
        ]);

        PrayerRequest::create([
            'user_id' => $this->user->id,
            'name' => 'Juana',
            'request' => 'Oración por la familia',
        ]);
    }

    public function test_red_page_renders_full_network(): void
    {
        $response = $this->actingAs($this->user)->get('/relacionamiento/mired');

        $response->assertOk()
            ->assertSee('Discípulo Dos');

        $this->assertSame(1, substr_count($response->getContent(), 'Discípulo Uno'));
    }

    public function test_calendar_page_renders(): void
    {
        $this->actingAs($this->user)
            ->get('/calendario')
            ->assertOk()
            ->assertSee('Calendario');
    }

    public function test_reportes_page_renders_charts_and_cards(): void
    {
        $this->actingAs($this->user)
            ->get('/reportes')
            ->assertOk()
            ->assertSee('Informes de grupo de conexión')
            ->assertSee('Informes de discipulado');
    }

    public function test_reportes_system_wide_lists_render(): void
    {
        $this->actingAs($this->user)
            ->get('/reportes/conexion')
            ->assertOk()
            ->assertSee('Todos los informes registrados en el sistema');

        $this->actingAs($this->user)
            ->get('/reportes/discipulado')
            ->assertOk()
            ->assertSee('Todos los informes de discipulado registrados en el sistema');
    }

    public function test_oracion_page_renders_prayer_requests(): void
    {
        $this->actingAs($this->user)
            ->get('/oracion')
            ->assertOk()
            ->assertSee('Pedidos de oración')
            ->assertSee('Oración por la familia');
    }
}
