@extends('admin.layout')

@section('page_title', 'User Activity Logs')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-history me-2"></i> User Activity Logs
        </h1>
        
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cleanupModal">
                <i class="fas fa-trash-alt me-1"></i> Cleanup Old Logs
            </button>

            <form action="{{ route('admin.activity.logs') }}" method="GET" class="d-flex align-items-center">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-calendar"></i>
                    </span>
                    <input type="date" name="date" class="form-control border-start-0 ps-0" 
                           value="{{ $date }}" onchange="this.form.submit()">
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Logs for {{ \Carbon\Carbon::parse($date)->format('F j, Y') }}</h6>
        </div>
        <div class="card-body">
            @if($logs->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-list fa-3x text-gray-300 mb-3"></i>
                    <p class="text-muted">No activities found for this date.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 15%">Time</th>
                                <th style="width: 20%">User</th>
                                <th style="width: 60%">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $index => $log)
                                <tr>
                                    <td>{{ $logs->firstItem() + $index }}</td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $log->created_at->format('H:i:s') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                {{ strtoupper(substr($log->user->name ?? $log->user->email ?? 'U', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $log->user->name ?? 'Unknown' }}</div>
                                                <div class="small text-muted">{{ $log->user->email ?? '' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-start">
                                            @if(str_contains(strtolower($log->title), 'topup'))
                                                <i class="fas fa-wallet text-info mt-1 me-2"></i>
                                            @elseif(str_contains(strtolower($log->title), 'purchase'))
                                                <i class="fas fa-shopping-bag text-success mt-1 me-2"></i>
                                            @else
                                                <i class="fas fa-info-circle text-secondary mt-1 me-2"></i>
                                            @endif
                                            <div>
                                                {{ $log->message }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $logs->appends(['date' => $date])->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Cleanup Modal -->
<div class="modal fade" id="cleanupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cleanup Old Logs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.activity.delete_old') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Delete logs older than (days):</label>
                        <input type="number" name="days" class="form-control" value="30" min="1" required>
                        <div class="form-text text-danger">
                            <i class="fas fa-exclamation-triangle me-1"></i> Warning: This action cannot be undone.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Logs</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
