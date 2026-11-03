<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class EventController extends Controller
{
    #[OA\Get(
        path: '/api/v1/events',
        summary: 'Lister les événements à venir',
        tags: ['Événements'],
        parameters: [
            new OA\Parameter(name: 'q', in: 'query', description: 'Recherche dans le titre.', schema: new OA\Schema(type: 'string', maxLength: 120)),
            new OA\Parameter(name: 'category', in: 'query', description: 'Identifiant de catégorie.', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'date', in: 'query', description: 'Filtrer par date (AAAA-MM-JJ).', schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'per_page', in: 'query', description: 'Résultats par page (1 à 100).', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, default: 15)),
            new OA\Parameter(name: 'page', in: 'query', description: 'Numéro de page.', schema: new OA\Schema(type: 'integer', minimum: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Liste paginée des événements.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Event')),
                        new OA\Property(property: 'current_page', type: 'integer', example: 1),
                        new OA\Property(property: 'last_page', type: 'integer', example: 3),
                        new OA\Property(property: 'per_page', type: 'integer', example: 15),
                        new OA\Property(property: 'total', type: 'integer', example: 36),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 422, description: 'Filtres invalides.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'category' => ['nullable', 'integer', 'exists:categories,id'],
            'date' => ['nullable', 'date_format:Y-m-d'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $events = Event::query()
            ->with('category')
            ->withCount('registrations')
            ->upcoming()
            ->when(
                $filters['q'] ?? null,
                fn ($query, string $term) => $query->where('title', 'like', "%{$term}%")
            )
            ->when(
                $filters['category'] ?? null,
                fn ($query, int $categoryId) => $query->where('category_id', $categoryId)
            )
            ->when(
                $filters['date'] ?? null,
                fn ($query, string $date) => $query->whereDate('date', $date)
            )
            ->orderBy('date')
            ->paginate($filters['per_page'] ?? 15);

        return response()->json($events);
    }

    #[OA\Post(
        path: '/api/v1/events',
        summary: 'Créer un événement (administrateur)',
        tags: ['Événements'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['category_id', 'title', 'description', 'date', 'location', 'capacity'],
                properties: [
                    new OA\Property(property: 'category_id', type: 'integer', example: 2),
                    new OA\Property(property: 'title', type: 'string', maxLength: 160, example: 'Fabrique ton premier site'),
                    new OA\Property(property: 'description', type: 'string', maxLength: 5000, example: 'Un atelier pratique.'),
                    new OA\Property(property: 'date', type: 'string', format: 'date-time', example: '2026-12-10T14:00:00'),
                    new OA\Property(property: 'location', type: 'string', maxLength: 180, example: 'Cotonou'),
                    new OA\Property(property: 'capacity', type: 'integer', minimum: 1, maximum: 100000, example: 30),
                ],
                type: 'object'
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Événement créé.', content: new OA\JsonContent(ref: '#/components/schemas/Event')),
            new OA\Response(response: 401, description: 'Authentification requise.'),
            new OA\Response(response: 403, description: 'Rôle administrateur requis.'),
            new OA\Response(response: 422, description: 'Données invalides.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $event = Event::create($this->validatedEvent($request));
        $event->load('category')->loadCount('registrations');

        return response()->json($event, 201);
    }

    #[OA\Get(
        path: '/api/v1/events/{event}',
        summary: 'Afficher un événement',
        tags: ['Événements'],
        parameters: [new OA\Parameter(name: 'event', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Événement trouvé.', content: new OA\JsonContent(ref: '#/components/schemas/Event')),
            new OA\Response(response: 404, description: 'Événement introuvable.'),
        ]
    )]
    public function show(Event $event): JsonResponse
    {
        $event->load('category')->loadCount('registrations');

        return response()->json($event);
    }

    #[OA\Put(
        path: '/api/v1/events/{event}',
        summary: 'Modifier un événement (administrateur)',
        tags: ['Événements'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'event', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['category_id', 'title', 'description', 'date', 'location', 'capacity'],
                properties: [
                    new OA\Property(property: 'category_id', type: 'integer', example: 2),
                    new OA\Property(property: 'title', type: 'string', maxLength: 160, example: 'Fabrique ton premier site'),
                    new OA\Property(property: 'description', type: 'string', maxLength: 5000, example: 'Un atelier pratique.'),
                    new OA\Property(property: 'date', type: 'string', format: 'date-time', example: '2026-12-10T14:00:00'),
                    new OA\Property(property: 'location', type: 'string', maxLength: 180, example: 'Cotonou'),
                    new OA\Property(property: 'capacity', type: 'integer', minimum: 1, maximum: 100000, example: 30),
                ],
                type: 'object'
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Événement modifié.', content: new OA\JsonContent(ref: '#/components/schemas/Event')),
            new OA\Response(response: 401, description: 'Authentification requise.'),
            new OA\Response(response: 403, description: 'Rôle administrateur requis.'),
            new OA\Response(response: 404, description: 'Événement introuvable.'),
            new OA\Response(response: 422, description: 'Données invalides ou capacité insuffisante.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function update(Request $request, Event $event): JsonResponse
    {
        $data = $this->validatedEvent($request);

        if ($event->registrations()->count() > $data['capacity']) {
            throw ValidationException::withMessages([
                'capacity' => ['La capacité ne peut pas être inférieure au nombre d’inscriptions existantes.'],
            ]);
        }

        $event->update($data);
        $event->load('category')->loadCount('registrations');

        return response()->json($event);
    }

    #[OA\Delete(
        path: '/api/v1/events/{event}',
        summary: 'Supprimer un événement (administrateur)',
        tags: ['Événements'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'event', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 204, description: 'Événement supprimé.'),
            new OA\Response(response: 401, description: 'Authentification requise.'),
            new OA\Response(response: 403, description: 'Rôle administrateur requis.'),
            new OA\Response(response: 404, description: 'Événement introuvable.'),
            new OA\Response(response: 409, description: 'L’événement possède des inscriptions.'),
        ]
    )]
    public function destroy(Event $event): JsonResponse
    {
        if ($event->registrations()->exists()) {
            return response()->json([
                'message' => 'Cet événement possède des inscriptions. Il ne peut pas être supprimé.',
            ], 409);
        }

        $event->delete();

        return response()->json(null, 204);
    }

    private function validatedEvent(Request $request): array
    {
        return $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:5000'],
            'date' => ['required', 'date', 'after:now'],
            'location' => ['required', 'string', 'max:180'],
            'capacity' => ['required', 'integer', 'min:1', 'max:100000'],
        ]);
    }
}
