@extends('admin.layout')

@section('page_title', 'Edit Game Image - ' . $game->game_name)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 text-dark">Edit Game Image: {{ $game->game_name }}</h1>
            <a href="{{ route('admin.game-images.index') }}" class="btn btn-secondary btn-sm mt-2">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 col-lg-6">
            <div class="card admin-card bg-dark text-white">
                <div class="card-header border-secondary">
                    <h5 class="card-title mb-0">Update Image</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.game-images.update', $game->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="form-label text-white-50">Current Image</label>
                            <div class="d-block mb-2">
                                <img src="{{ asset('adminimages/' . $game->image_path) }}" alt="Current Image" class="img-thumbnail" style="max-height: 200px;">
                            </div>
                            <small class="text-white-50">This is the current image displayed on the website.</small>
                        </div>

                        <div class="mb-4">
                            <label for="image" class="form-label">New Image</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-white-50">Supported formats: JPG, PNG, WEBP, GIF. Max size: 2MB.</div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
