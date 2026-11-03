<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Eventia API',
    description: 'API REST de gestion des événements et des inscriptions. Les routes protégées utilisent des jetons Laravel Sanctum.'
)]
#[OA\Server(url: '/', description: 'Serveur de développement')]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Bearer',
    description: 'Utilisez le jeton Sanctum obtenu à l’inscription ou à la connexion.'
)]
#[OA\Tag(name: 'Authentification', description: 'Inscription, connexion et gestion du jeton')]
#[OA\Tag(name: 'Événements', description: 'Consultation des événements et gestion réservée aux administrateurs')]
#[OA\Tag(name: 'Inscriptions', description: 'Gestion des inscriptions de l’utilisateur connecté')]
#[OA\Schema(
    schema: 'User',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Amina User'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'amina@example.com'),
        new OA\Property(property: 'role', type: 'string', example: 'user'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'AuthToken',
    properties: [
        new OA\Property(property: 'user', ref: '#/components/schemas/User'),
        new OA\Property(property: 'token', type: 'string', example: '1|sanctum-personal-access-token'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'Category',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 2),
        new OA\Property(property: 'name', type: 'string', example: 'Atelier'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'Event',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 7),
        new OA\Property(property: 'category_id', type: 'integer', example: 2),
        new OA\Property(property: 'title', type: 'string', example: 'Fabrique ton premier site'),
        new OA\Property(property: 'description', type: 'string', example: 'Un atelier pratique pour découvrir le HTML.'),
        new OA\Property(property: 'date', type: 'string', format: 'date-time', example: '2026-12-10T14:00:00.000000Z'),
        new OA\Property(property: 'location', type: 'string', example: 'Cotonou'),
        new OA\Property(property: 'capacity', type: 'integer', example: 30),
        new OA\Property(property: 'registrations_count', type: 'integer', example: 12),
        new OA\Property(property: 'category', ref: '#/components/schemas/Category'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'Registration',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 4),
        new OA\Property(property: 'user_id', type: 'integer', example: 1),
        new OA\Property(property: 'event_id', type: 'integer', example: 7),
        new OA\Property(property: 'event', ref: '#/components/schemas/Event'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'ValidationError',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Les données fournies sont invalides.'),
        new OA\Property(
            property: 'errors',
            type: 'object',
            additionalProperties: new OA\AdditionalProperties(
                type: 'array',
                items: new OA\Items(type: 'string')
            )
        ),
    ],
    type: 'object'
)]
class ApiSpec
{
}
