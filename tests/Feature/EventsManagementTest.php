<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventsManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitors_can_browse_and_filter_upcoming_events(): void
    {
        $technology = Category::factory()->create(['name' => 'Technologie']);
        $culture = Category::factory()->create(['name' => 'Culture']);
        $date = now()->addDays(10)->format('Y-m-d');

        $event = Event::factory()->for($technology)->create([
            'title' => 'Conférence Laravel',
            'date' => $date.' 14:00:00',
        ]);
        Event::factory()->for($culture)->create([
            'title' => 'Soirée culturelle',
            'date' => now()->addDays(11),
        ]);

        $this->get(route('events.index'))
            ->assertOk()
            ->assertSee('Conférence Laravel')
            ->assertSee('Soirée culturelle');

        $this->get(route('events.index', ['q' => 'Laravel', 'category' => $technology->id, 'date' => $date]))
            ->assertOk()
            ->assertSee('Conférence Laravel')
            ->assertDontSee('Soirée culturelle');

        $this->get(route('events.show', $event))
            ->assertOk()
            ->assertSee('Technologie')
            ->assertSee('14:00');
    }

    public function test_missing_event_returns_not_found(): void
    {
        $this->get('/events/999999')->assertNotFound();
    }

    public function test_event_administration_requires_an_admin_account(): void
    {
        // La page de connexion appartient à l'autre membre ; la réponse JSON
        // permet de vérifier la protection sans inventer sa route ici.
        $this->getJson(route('admin.events.index'))->assertUnauthorized();

        $ordinaryUser = User::factory()->create();
        $this->actingAs($ordinaryUser)
            ->get(route('admin.events.index'))
            ->assertForbidden();
    }

    public function test_navigation_changes_for_authenticated_users(): void
    {
        $this->get(route('events.index'))
            ->assertOk()
            ->assertSee('Créer un compte');

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('registrations.index'))
            ->assertOk();

        $this->get(route('events.index'))
            ->assertOk()
            ->assertSee('Mes inscriptions')
            ->assertSee('Déconnexion')
            ->assertDontSee('Créer un compte');
    }

    public function test_admin_can_create_update_and_delete_an_event(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $category = Category::factory()->create();
        $this->actingAs($admin);

        $payload = [
            'category_id' => $category->id,
            'title' => 'Atelier de découverte',
            'description' => 'Une rencontre pratique autour du web.',
            'date' => now()->addDays(20)->format('Y-m-d\TH:i'),
            'location' => 'IFRI, Abomey-Calavi',
            'capacity' => 40,
        ];

        $this->post(route('admin.events.store'), $payload)
            ->assertRedirect(route('admin.events.index'))
            ->assertSessionHas('success');

        $event = Event::where('title', $payload['title'])->firstOrFail();
        $this->assertDatabaseHas('events', ['id' => $event->id, 'capacity' => 40]);

        $payload['title'] = 'Atelier Laravel';
        $payload['capacity'] = 55;
        $this->put(route('admin.events.update', $event), $payload)
            ->assertRedirect(route('admin.events.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('events', ['id' => $event->id, 'title' => 'Atelier Laravel', 'capacity' => 55]);

        $this->delete(route('admin.events.destroy', $event))
            ->assertRedirect(route('admin.events.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    public function test_admin_event_form_rejects_invalid_values(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $this->actingAs($admin);

        $this->post(route('admin.events.store'), [
            'category_id' => 999,
            'title' => '',
            'description' => '',
            'date' => now()->subDay()->format('Y-m-d\TH:i'),
            'location' => '',
            'capacity' => 0,
        ])->assertSessionHasErrors(['category_id', 'title', 'description', 'date', 'location', 'capacity']);

        $this->assertDatabaseCount('events', 0);
    }
}
