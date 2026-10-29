<?php

namespace App\Support;

use Carbon\Carbon;

/** Données temporaires pour visualiser les vues avant la création du backend métier. */
class EventiaPreview
{
    public static function events(): array
    {
        $today = Carbon::today();

        return [
            ['id' => 1, 'title' => 'Les voix du futur', 'category' => 'Conférence', 'starts_at' => $today->copy()->addDays(8)->setTime(9, 30), 'location' => 'IFRI, Université d’Abomey-Calavi', 'description' => 'Une matinée pour parler des métiers du numérique et des idées qui transforment nos villes.', 'long_description' => 'Étudiantes, étudiants et professionnels partagent leurs expériences autour des technologies, de l’entrepreneuriat et des nouveaux usages. La rencontre se termine par une discussion ouverte avec les intervenants.', 'duration' => '3 heures', 'capacity' => 120, 'registered' => 84, 'remaining' => 36, 'tone' => 'coral', 'symbol' => '✳'],
            ['id' => 2, 'title' => 'Fabrique ton premier site', 'category' => 'Atelier', 'starts_at' => $today->copy()->addDays(13)->setTime(14, 0), 'location' => 'Epitech Bénin, Cotonou', 'description' => 'Un atelier pratique pour découvrir le HTML et donner forme à sa première page web.', 'long_description' => 'Pas besoin d’expérience préalable. Nous avancerons étape par étape, du premier titre jusqu’à une page personnelle que vous pourrez continuer à faire évoluer après l’atelier.', 'duration' => '2 heures', 'capacity' => 30, 'registered' => 19, 'remaining' => 11, 'tone' => 'lilac', 'symbol' => '⌘'],
            ['id' => 3, 'title' => 'Une soirée au musée', 'category' => 'Culture', 'starts_at' => $today->copy()->addDays(19)->setTime(18, 0), 'location' => 'Musée Honmè, Porto-Novo', 'description' => 'Une visite guidée pour découvrir autrement les histoires et les collections du musée.', 'long_description' => 'Au fil des salles, notre guide fera dialoguer objets, architecture et mémoire. Un moment de découverte ouvert à toutes et tous, suivi d’un échange convivial dans la cour du musée.', 'duration' => '2 heures', 'capacity' => 50, 'registered' => 31, 'remaining' => 19, 'tone' => 'mint', 'symbol' => '◒'],
            ['id' => 4, 'title' => 'Cotonou en mouvement', 'category' => 'Communauté', 'starts_at' => $today->copy()->addDays(24)->setTime(8, 0), 'location' => 'Place de l’Amazone, Cotonou', 'description' => 'Une promenade collective pour rencontrer les personnes qui font bouger la ville.', 'long_description' => 'Nous partirons à pied à la rencontre de projets locaux, de lieux partagés et de celles et ceux qui les font vivre. Prévoyez des chaussures confortables et votre curiosité.', 'duration' => '2 heures 30', 'capacity' => 60, 'registered' => 27, 'remaining' => 33, 'tone' => 'blue', 'symbol' => '↗'],
            ['id' => 5, 'title' => 'Les mots en partage', 'category' => 'Culture', 'starts_at' => $today->copy()->addDays(29)->setTime(16, 30), 'location' => 'Médiathèque de l’IFB, Cotonou', 'description' => 'Lecture, poésie et conversation autour des nouvelles voix de la littérature béninoise.', 'long_description' => 'Des autrices et auteurs invités liront des extraits de leurs textes avant un temps d’échange avec le public. Vous pourrez aussi repartir avec quelques recommandations de lecture.', 'duration' => '1 heure 30', 'capacity' => 45, 'registered' => 38, 'remaining' => 7, 'tone' => 'yellow', 'symbol' => '“'],
            ['id' => 6, 'title' => 'Café des idées', 'category' => 'Rencontre', 'starts_at' => $today->copy()->addDays(35)->setTime(10, 0), 'location' => 'Café Maison Rouge, Cotonou', 'description' => 'Un rendez-vous simple pour échanger des idées de projets et trouver des partenaires.', 'long_description' => 'Chacun peut présenter une idée, demander conseil ou simplement écouter. La rencontre est informelle et ouverte aux curieux qui souhaitent mieux connaître les initiatives locales.', 'duration' => '2 heures', 'capacity' => 25, 'registered' => 12, 'remaining' => 13, 'tone' => 'peach', 'symbol' => '○'],
        ];
    }

    public static function registrations(): array
    {
        return [
            ['id' => 1, 'participant_name' => 'Aïcha Soglo', 'participant_email' => 'aicha@example.test', 'event_title' => 'Les voix du futur', 'created_at' => now()->subDays(3), 'status' => 'active'],
            ['id' => 2, 'participant_name' => 'Kévin Hounkpatin', 'participant_email' => 'kevin@example.test', 'event_title' => 'Fabrique ton premier site', 'created_at' => now()->subDays(2), 'status' => 'active'],
            ['id' => 3, 'participant_name' => 'Mariam Adanhoume', 'participant_email' => 'mariam@example.test', 'event_title' => 'Les mots en partage', 'created_at' => now()->subDay(), 'status' => 'cancelled'],
        ];
    }
}
