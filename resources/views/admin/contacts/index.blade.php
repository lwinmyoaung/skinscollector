@extends('admin.layout')

@section('page_title', 'Contact Manager')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-2 mb-4">
        <div>
            <h1 class="h4 mb-1 fw-bold">Contact Manager</h1>
            <div class="text-muted">Manage contact information displayed to users</div>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addContactModal">
            <i class="fas fa-plus me-2"></i>Add Contact
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Platform</th>
                            <th>Value</th>
                            <th>Icon</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contacts as $contact)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        @if($contact->icon)
                                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background-color: {{ $contact->color ? $contact->color.'20' : '#e9ecef' }}">
                                                <i class="{{ $contact->icon }}" style="color: {{ $contact->color ?? '#6c757d' }}"></i>
                                            </div>
                                        @endif
                                        <span class="fw-medium">{{ $contact->platform }}</span>
                                    </div>
                                </td>
                                <td>{{ $contact->value }}</td>
                                <td><code>{{ $contact->icon }}</code></td>
                                <td>
                                    @if($contact->is_active)
                                        <span class="badge bg-success bg-opacity-10 text-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#editContactModal{{ $contact->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.contacts.destroy', $contact->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this contact?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editContactModal{{ $contact->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Contact</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('admin.contacts.update', $contact->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Platform Name</label>
                                                    <input type="text" name="platform" class="form-control" value="{{ $contact->platform }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Value (Link or Number)</label>
                                                    <input type="text" name="value" class="form-control" value="{{ $contact->value }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Icon Class (FontAwesome)</label>
                                                    <input type="text" name="icon" class="form-control" value="{{ $contact->icon }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Color (Hex Code)</label>
                                                    <input type="color" name="color" class="form-control form-control-color" value="{{ $contact->color ?? '#000000' }}">
                                                </div>
                                                <div class="mb-3 form-check">
                                                    <input type="hidden" name="is_active" value="0">
                                                    <input type="checkbox" class="form-check-input" name="is_active" value="1" id="isActive{{ $contact->id }}" {{ $contact->is_active ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="isActive{{ $contact->id }}">Active</label>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Update Contact</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-address-book fa-3x mb-3 opacity-50"></i>
                                    <p class="mb-0">No contacts found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addContactModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Contact</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.contacts.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Platform Name</label>
                        <input type="text" name="platform" class="form-control" placeholder="e.g. Telegram, WhatsApp" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Value (Link or Number)</label>
                        <input type="text" name="value" class="form-control" placeholder="e.g. https://t.me/admin" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Icon Class (Optional)</label>
                        <input type="text" name="icon" class="form-control" placeholder="e.g. fab fa-telegram">
                        <div class="form-text">Leave blank to auto-detect for common platforms</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Color (Optional)</label>
                        <input type="color" name="color" class="form-control form-control-color" value="#000000">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Contact</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
