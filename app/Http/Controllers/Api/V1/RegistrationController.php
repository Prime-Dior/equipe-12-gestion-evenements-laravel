<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class RegistrationController extends Controller
{
    #[OA\Get(
        path: '/api/v1/registrations',
        summary: 'Lister mes inscriptions',
        tags: ['Inscriptions'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', description: 'Numéro de page.', schema: new OA\Schema(type: 'integer', minimum: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Liste paginée des inscriptions de l’utilisateur connecté.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Registration')),
                        new OA\Property(property: 'current_page', type: 'integer', example: 1),
                        new OA\Property(property: 'last_page', type: 'integer', example: 2),
                        new OA\Property(property: 'per_page', type: 'integer', example: 15),
                        new OA\Property(property: 'total', type: 'integer', example: 20),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 401, description: 'Jeton absent ou invalide.'),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $registrations = $request->user()
            ->registrations()
            ->with('event.category')
            ->latest()
            ->paginate(15);

        return response()->json($registrations);
    }

    #[OA\Post(
        path: '/api/v1/registrations',
        summary: 'S’inscrire à un événement',
        tags: ['Inscriptions'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['event_id'],
                properties: [new OA\Property(property: 'event_id', type: 'integer', example: 7)],
                type: 'object'
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Inscription créée.', content: new OA\JsonContent(ref: '#/components/schemas/Registration')),
            new OA\Response(response: 401, description: 'Jeton absent ou invalide.'),
            new OA\Response(response: 404, description: 'Événement introuvable.'),
            new OA\Response(response: 422, description: 'Événement passé, complet, déjà réservé ou identifiant invalide.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'event_id' => ['required', 'integer', 'exists:events,id'],
        ]);

        $result = DB::transaction(function () use ($request, $data): string {
            $event = Event::query()
                ->whereKey($data['event_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($event->isPast()) {
                return 'past';
            }

            if ($event->registrations()->where('user_id', $request->user()->getKey())->exists()) {
                return 'already_registered';
            }

            if ($event->registrations()->count() >= $event->capacity) {
                return 'full';
            }

            $event->registrations()->create([
                'user_id' => $request->user()->getKey(),
            ]);

            return 'registered';
        });

        if ($result !== 'registered') {
            $message = match ($result) {
                'past' => 'Les inscriptions sont closes car cet événement est passé.',
                'already_registered' => 'Vous êtes déjà inscrit à cet événement.',
                'full' => 'Cet événement affiche complet.',
            };

            throw ValidationException::withMessages(['event_id' => [$message]]);
        }

        $registration = $request->user()
            ->registrations()
            ->with('event.category')
            ->where('event_id', $data['event_id'])
            ->firstOrFail();

        return response()->json($registration, 201);
    }

    #[OA\Get(
        path: '/api/v1/registrations/{registration}',
        summary: 'Afficher une de mes inscriptions',
        tags: ['Inscriptions'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'registration', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Inscription trouvée.', content: new OA\JsonContent(ref: '#/components/schemas/Registration')),
            new OA\Response(response: 401, description: 'Jeton absent ou invalide.'),
            new OA\Response(response: 404, description: 'Inscription introuvable ou ne vous appartenant pas.'),
        ]
    )]
    public function show(Request $request, Registration $registration): JsonResponse
    {
        $registration = $request->user()
            ->registrations()
            ->with('event.category')
            ->findOrFail($registration->getKey());

        return response()->json($registration);
    }

    #[OA\Delete(
        path: '/api/v1/registrations/{registration}',
        summary: 'Annuler une de mes inscriptions',
        tags: ['Inscriptions'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'registration', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 204, description: 'Inscription annulée.'),
            new OA\Response(response: 401, description: 'Jeton absent ou invalide.'),
            new OA\Response(response: 404, description: 'Inscription introuvable ou ne vous appartenant pas.'),
        ]
    )]
    public function destroy(Request $request, Registration $registration): JsonResponse
    {
        $registration = $request->user()
            ->registrations()
            ->findOrFail($registration->getKey());
        $registration->delete();

        return response()->json(null, 204);
    }
}
