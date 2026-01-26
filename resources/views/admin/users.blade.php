@extends('admin.layout')

@section('page_title', 'Users')

@section('content')

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 text-dark">User Management</h4>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-6 col-md-6">
            <div class="card bg-primary text-white h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white-50 small text-uppercase fw-bold mb-1">Total Users</div>
                            <div class="h2 mb-0 fw-bold">{{ number_format($totalUsers) }}</div>
                        </div>
                        <div class="fs-1 text-white-50">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    {{-- Main Card --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0 text-dark">Registered Users</h5>
                </div>
                <div class="col-auto">
                    <form action="{{ route('admin.users') }}" method="GET" class="d-flex">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search by email..." value="{{ request('search') }}">
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                            @if(request('search'))
                                <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary" title="Clear Search">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>User</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined Date</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td class="ps-4 text-muted">#{{ $user->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3 bg-light text-primary fw-bold d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px;">
                                        {{ strtoupper(substr($user->email, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $user->name ?? explode('@', $user->email)[0] }}</div>
                                        <div class="small text-muted">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($user->role === 'admin')
                                    <span class="badge bg-danger">Admin</span>
                                @else
                                    <span class="badge bg-secondary">User</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success">Active</span>
                            </td>
                            <td class="text-muted small">
                                <div>{{ $user->created_at->format('M d, Y') }}</div>
                                <div>{{ $user->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    @if(auth()->id() !== $user->id)
                                        <button type="button" class="btn btn-sm btn-outline-primary" title="Send Message" data-bs-toggle="modal" data-bs-target="#sendMessageModal" data-user-id="{{ $user->id }}" data-user-email="{{ $user->email }}">
                                            <i class="fas fa-envelope"></i>
                                        </button>
                                        <form action="{{ route('admin.users.updateRole', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            @if($user->role === 'admin')
                                                <input type="hidden" name="role" value="user">
                                                <button type="submit" class="btn btn-sm btn-outline-warning" title="Demote to User" onclick="return confirm('Are you sure you want to remove admin privileges from this user?')">
                                                    <i class="fas fa-user-shield"></i>
                                                </button>
                                            @else
                                                <input type="hidden" name="role" value="admin">
                                                <button type="submit" class="btn btn-sm btn-outline-info" title="Promote to Admin" onclick="return confirm('Are you sure you want to promote this user to Admin?')">
                                                    <i class="fas fa-shield-alt"></i>
                                                </button>
                                            @endif
                                        </form>

                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="if(confirm('Are you sure you want to delete this user?')) document.getElementById('delete-form-{{ $user->id }}').submit();" title="Delete User">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    @endif
                                </div>

                                <form id="delete-form-{{ $user->id }}" action="{{ route('admin.users.delete', $user->id) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-search fa-3x mb-3 text-gray-300"></i>
                                <p>No users found matching your search.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($users->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $users->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

<div class="modal fade" id="sendMessageModal" tabindex="-1" aria-labelledby="sendMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="sendMessageModalLabel">Send Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">To</label>
                        <input type="text" class="form-control" id="message-user-email" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="message-title" class="form-label">Title</label>
                        <input type="text" name="title" id="message-title" class="form-control" maxlength="255" required>
                    </div>
                    <div class="mb-3">
                        <label for="message-body" class="form-label">Message</label>
                        <textarea name="message" id="message-body" class="form-control" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modal = document.getElementById('sendMessageModal');
        if (!modal) return;
        modal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            if (!button) return;
            var userId = button.getAttribute('data-user-id');
            var userEmail = button.getAttribute('data-user-email');
            var userEmailInput = document.getElementById('message-user-email');
            if (userEmailInput) userEmailInput.value = userEmail || '';
            var form = modal.querySelector('form');
            if (form && userId) {
                form.action = "{{ url('admin/users') }}/" + encodeURIComponent(userId) + "/message";
            }
        });
    });
</script>
@endsection

@endsection
