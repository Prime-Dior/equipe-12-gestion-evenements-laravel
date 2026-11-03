
@extends('layouts.app')

@section('title', 'Gestion des événements')

@section('content')
    <section class="admin-page">
        <div class="admin-title-row">
            <div>
                <p class="eyebrow">Administration</p>
                <h1>Événements</h1>
                <p>Créer et mettre à jour le calendrier.</p>
            </div>
            <a class="button" href="{{ route('admin.events.create') }}">＋ Ajouter un événement</a>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ÉVÉNEMENT</th>
                        <th>CATÉGORIE</th>
                        <th>DATE</th>
                        <th>LIEU</th>
                        <th>PLACES</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($events as $event)
                        <tr>
                            <td><strong>{{ $event->title }}</strong></td>
                            <td>{{ $event->category->name }}</td>
                            <td>{{ $event->date->translatedFormat('d M Y · H:i') }}</td>
                            <td>{{ $event->location }}</td>
                            <td>{{ $event->places_remaining }} / {{ $event->capacity }}</td>
                            <td class="table-actions">
                                <a href="{{ route('admin.events.edit', $event) }}">Modifier</a>
                                <form method="POST" action="{{ route('admin.events.destroy', $event) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="table-empty">Aucun événement enregistré.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">{{ $events->links() }}</div>
    </section>
@endsection
