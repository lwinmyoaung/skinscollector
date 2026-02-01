@extends('admin.layout')

@section('page_title', 'Payment Methods')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-2 mb-4">
        <div>
            <h1 class="h4 mb-1 fw-bold">Payment Methods</h1>
            <div class="text-muted">Manage your payment gateways and options</div>
        </div>
        <div class="col-auto">
            <a href="{{ url('admin/paymentmethod/create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Method
            </a>
        </div>
    </div>

    @if(session('message'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif



    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-0 pt-4 pb-2">
            <h2 class="h6 mb-0 fw-bold"><i class="fas fa-wallet me-2 text-primary"></i>Available Methods</h2>
        </div>
        <div class="card-body pt-0 p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase small text-muted">
                        <tr>
                            <th class="ps-4" style="width: 5%;">#</th>
                            <th style="width: 20%;">Payment Name</th>
                            <th style="width: 25%;">Logo / Image</th>
                            <th style="width: 20%;">Phone Number</th>
                            <th style="width: 15%;">Status</th>
                            <th class="text-end pe-4" style="width: 20%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pms as $index => $pm)
                            <tr>
                                <td class="ps-4 text-muted">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $pm->name }}</div>
                                </td>
                                <td>
                                    <div class="p-1 border rounded bg-light d-inline-block">
                                        <img src="{{ asset('adminimages/images/paymentmethodphoto/'.$pm->image) }}" alt="{{ $pm->name }}" class="d-block" style="height: 60px; object-fit: contain; max-width: 120px;" onerror="this.src='data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22120%22%20height%3D%2260%22%20viewBox%3D%220%200%20120%2060%22%3E%3Crect%20width%3D%22120%22%20height%3D%2260%22%20fill%3D%22%23e9ecef%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2250%25%22%20dominant-baseline%3D%22middle%22%20text-anchor%3D%22middle%22%20font-family%3D%22sans-serif%22%20font-size%3D%2212%22%20fill%3D%22%236c757d%22%3ENo%20Image%3C%2Ftext%3E%3C%2Fsvg%3E'">
                                    </div>
                                </td>
                                <td>
                                    @if($pm->phone_number)
                                        <div class="d-flex align-items-center gap-2">
                                            <span>{{ $pm->phone_number }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted small">Not set</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Active</span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="{{ url('admin/paymentmethod/'.$pm->id.'/edit') }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ url('admin/paymentmethod/'.$pm->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this payment method?');" title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <div class="mb-3">
                                        <i class="fas fa-folder-open fa-3x text-light"></i>
                                    </div>
                                    <p class="mb-0">No payment methods found.</p>
                                    <a href="{{ url('admin/paymentmethod/create') }}" class="btn btn-link btn-sm">Add your first payment method</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
