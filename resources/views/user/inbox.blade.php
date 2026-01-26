@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12 mb-3 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h4 mb-1">Inbox</h2>
                <p class="text-muted mb-0">Messages and updates about your orders.</p>
            </div>
            @if($notifications->count() > 0)
                <form method="POST" action="{{ route('notifications.markAllRead') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary btn-sm">Mark all read</button>
                </form>
            @endif
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($notifications->isEmpty())
                <div class="p-4 text-center text-muted">
                    <i class="fas fa-inbox fa-2x mb-2"></i>
                    <p class="mb-0">No messages yet.</p>
                </div>
            @else
                <div class="list-group list-group-flush">
                    @foreach($notifications as $notification)
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div>
                                <div class="d-flex align-items-center mb-1">
                                    <span class="badge me-2
                                        @if($notification->type === 'success') bg-success-subtle text-success
                                        @elseif($notification->type === 'error') bg-danger-subtle text-danger
                                        @elseif($notification->type === 'warning') bg-warning-subtle text-warning
                                        @else bg-primary-subtle text-primary
                                        @endif
                                    ">
                                        {{ ucfirst($notification->type ?? 'info') }}
                                    </span>
                                    <strong>{{ $notification->title }}</strong>
                                </div>
                                <div class="small mb-1">{{ $notification->message }}</div>
                                <div class="text-muted small">{{ $notification->created_at->diffForHumans() }}</div>
                            </div>
                            @if(!$notification->is_read)
                                <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-link btn-sm text-decoration-none">
                                        Mark read
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="p-3">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

