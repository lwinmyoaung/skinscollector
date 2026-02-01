@extends('admin.layout')

@section('page_title', 'Error Logs')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 text-dark">Error Logs</h4>
        <div class="d-flex gap-2">
            <form action="{{ route('admin.error-logs.fetch') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sync-alt me-2"></i>Fetch from Log File
                </button>
            </form>
            <form action="{{ route('admin.error-logs.clear') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear all logs?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash me-2"></i>Clear All Logs
                </button>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" style="width: 50px;">ID</th>
                            <th>Time</th>
                            <th>User</th>
                            <th>Method</th>
                            <th>URL</th>
                            <th>Message</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="ps-4">#{{ $log->id }}</td>
                                <td>
                                    <div>{{ $log->created_at->format('Y-m-d H:i:s') }}</div>
                                    <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    @if($log->user)
                                        <div class="fw-bold">{{ $log->user->name ?? 'User' }}</div>
                                        <small class="text-muted">ID: {{ $log->user_id }}</small>
                                    @else
                                        <span class="badge bg-secondary">Guest</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $log->method }}</span>
                                </td>
                                <td class="text-break" style="max-width: 300px;">
                                    {{ $log->url }}
                                </td>
                                <td class="text-break" style="max-width: 400px;">
                                    {{ Str::limit($log->message, 100) }}
                                </td>
                                <td class="text-end pe-4">
                                    <button type="button" class="btn btn-sm btn-outline-primary me-2" 
                                            onclick="showStackTrace({{ $log->id }}, {{ json_encode($log->message) }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <form action="{{ route('admin.error-logs.destroy', $log) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this log?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                                        <p class="mb-0">No error logs found.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="p-3">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Stack Trace Modal -->
<div class="modal fade" id="stackTraceModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="errorTitle">Error Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" id="errorMessage"></div>
                <h6>Stack Trace:</h6>
                <pre class="bg-light p-3 rounded code-block" id="stackTrace" style="white-space: pre-wrap; font-size: 0.85rem;"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    function showStackTrace(id, message) {
        const modal = new bootstrap.Modal(document.getElementById('stackTraceModal'));
        document.getElementById('errorMessage').textContent = message;
        document.getElementById('stackTrace').textContent = 'Loading...';
        
        modal.show();
        
        fetch(`{{ url('admin/error-logs') }}/${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('stackTrace').textContent = data.stack_trace;
                document.getElementById('errorMessage').textContent = data.message;
            })
            .catch(err => {
                document.getElementById('stackTrace').textContent = 'Failed to load stack trace.';
            });
    }
</script>
@endsection
