<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_visitor_can_browse_events_and_open_an_event(): void
    {
        $event = $this->createEvent();

        $this->get(route('events.index'))
            ->assertOk()
            ->assertSee($event->title);

        $this->get(route('events.show', $event))
            ->assertOk()
            ->assertSee('Connectez-vous pour participer à cet événement.');
    }

    public function test_an_authenticated_user_can_register_and_cancel_registration(): void
    {
        $event = $this->createEvent();
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('registrations.store', $event));

        $response->assertRedirect(route('events.show', $event));
        $this->assertDatabaseHas('registrations', [
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);

        $registration = Registration::query()->firstOrFail();

        $this->delete(route('registrations.destroy', $registration))
            ->assertRedirect(route('events.show', $event));

        $this->assertDatabaseMissing('registrations', [
            'id' => $registration->id,
        ]);
    }

    public function test_an_event_cannot_be_overbooked_or_registered_twice(): void
    {
        $event = $this->createEvent(capacity: 1);
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($user)->post(route('registrations.store', $event));

        $this->post(route('registrations.store', $event))
            ->assertSessionHas('error');

        $this->actingAs($otherUser)
            ->post(route('registrations.store', $event))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('registrations', 1);
    }

    private function createEvent(int $capacity = 10): Event
    {
        $category = Category::create(['name' => 'Atelier']);

        return Event::create([
            'category_id' => $category->id,
            'title' => 'Événement de test',
            'description' => 'Description du test',
            'date' => now()->addDays(3),
            'location' => 'Cotonou',
            'capacity' => $capacity,
        ]);
    }
}
