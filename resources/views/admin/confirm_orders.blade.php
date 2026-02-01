@extends('admin.layout')

@section('page_title', 'Orders')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-2 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h1 class="h4 mb-1 fw-bold">Orders</h1>
                <span id="new-orders-badge" class="badge bg-danger rounded-pill" style="display: none;">
                    <span id="new-orders-count">0</span> Pending
                </span>
            </div>
            <div class="text-muted">Review payment screenshots and approve game delivery</div>
        </div>
        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cleanupModal">
            <i class="fas fa-trash-alt me-2"></i>Cleanup Old Orders
        </button>
    </div>


    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.dashboard') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="game_type" class="form-label">Game</label>
                    <select name="game_type" id="game_type" class="form-select">
                        <option value="">All Games</option>
                        @foreach($games as $game)
                            <option value="{{ $game }}" {{ request('game_type') === $game ? 'selected' : '' }}>
                                {{ strtoupper($game) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="payment_method" class="form-label">Payment Method</label>
                    <select name="payment_method" id="payment_method" class="form-select">
                        <option value="">All Methods</option>
                        @foreach($paymentMethods as $pm)
                            @php $value = strtolower($pm->name); @endphp
                            <option value="{{ $value }}" {{ request('payment_method') === $value ? 'selected' : '' }}>
                                {{ $pm->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12 d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Created</th>
                            <th>Game</th>
                            <th>Player ID</th>
                            <th>Product</th>
                            <th>Amount (MMK)</th>
                            <th>Phone Number</th>
                            <th>Screenshot</th>
                            <th>Status</th>
                            <th style="width: 220px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @include('admin.partials.confirm_orders_table')
                    </tbody>
                </table>
            </div>

            <div class="p-3">
                {{ $orders->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Cleanup Modal -->
<div class="modal fade" id="cleanupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cleanup Old Orders</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.confirm.orders.delete_old') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Delete orders older than (days):</label>
                        <input type="number" name="days" class="form-control" value="90" min="1" required>
                        <div class="form-text text-danger">
                            Warning: This action cannot be undone. All orders older than the specified number of days will be permanently deleted.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete these orders? This cannot be undone.')">Delete Orders</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initial fetch to set the badge count
        fetchOrders();

        // Auto refresh every 10 seconds
        setInterval(fetchOrders, 10000);

        function fetchOrders() {
            // Construct URL with current query parameters to maintain filters
            const currentUrl = new URL(window.location.href);
            const fetchUrl = new URL("{{ route('admin.confirm.orders.fetch') }}");
            
            // Copy search params from current URL to fetch URL
            currentUrl.searchParams.forEach((value, key) => {
                fetchUrl.searchParams.append(key, value);
            });

            fetch(fetchUrl)
                .then(response => response.json())
                .then(data => {
                    // Update table body
                    const tbody = document.querySelector('tbody');
                    if (tbody) {
                        tbody.innerHTML = data.html;
                    }
                    
                    // Update count badge
                    const badge = document.getElementById('new-orders-badge');
                    const countSpan = document.getElementById('new-orders-count');
                    
                    if (badge && countSpan) {
                        if (data.pending_count > 0) {
                            countSpan.textContent = data.pending_count;
                            badge.style.display = 'inline-block';
                        } else {
                            badge.style.display = 'none';
                        }
                    }
                })
                .catch(error => console.error('Error fetching orders:', error));
        }
    });
</script>
@endsection
