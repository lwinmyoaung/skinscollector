@extends('admin.layout')

@section('page_title', 'Game Images')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 text-dark">Game Card Images</h1>
            <p class="text-secondary">Manage game card images displayed on the homepage.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif


    <div class="card admin-card bg-dark text-white">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Game</th>
                            <th>Current Image</th>
                            <th>Last Updated</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($games as $game)
                            <tr>
                                <td>
                                    <span class="fw-bold">{{ $game->game_name }}</span>
                                    <br>
                                    <small class="text-muted">{{ $game->game_code }}</small>
                                </td>
                                <td>
                                    <img src="{{ asset('storage/' . $game->image_path) }}" alt="{{ $game->game_name }}" class="img-thumbnail" style="max-height: 80px;">
                                </td>
                                <td>{{ $game->updated_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.game-images.edit', $game->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Change Photo
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
