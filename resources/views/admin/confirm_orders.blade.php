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

<!-- Approval Progress Modal -->
<div class="modal fade" id="approvalProgressModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Processing Order</h5>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <p id="approvalStatusText" class="fw-bold">Initializing...</p>
                    <div class="progress" style="height: 25px;">
                        <div id="approvalProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%">0%</div>
                    </div>
                </div>
                <ul id="approvalLog" class="list-group list-group-flush small" style="max-height: 200px; overflow-y: auto;">
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="approvalCloseBtn" onclick="location.reload()" disabled>Done</button>
            </div>
        </div>
    </div>
</div>

<script>
    let approvalModal;

    function startApprovalProcess(orderId, quantity) {
        if (!confirm('Approve and send this order to the game?')) return;

        if (!approvalModal) {
            approvalModal = new bootstrap.Modal(document.getElementById('approvalProgressModal'));
        }

        const modalEl = document.getElementById('approvalProgressModal');
        const statusText = document.getElementById('approvalStatusText');
        const progressBar = document.getElementById('approvalProgressBar');
        const logList = document.getElementById('approvalLog');
        const closeBtn = document.getElementById('approvalCloseBtn');

        // Reset UI
        statusText.textContent = `Preparing to send ${quantity} items...`;
        statusText.className = 'fw-bold'; 
        progressBar.style.width = '0%';
        progressBar.textContent = '0%';
        progressBar.className = 'progress-bar progress-bar-striped progress-bar-animated'; 
        logList.innerHTML = '';
        closeBtn.disabled = true;
        closeBtn.textContent = 'Done';
        
        approvalModal.show();

        processItems(orderId, quantity, 0);
    }

    function processItems(orderId, totalQuantity, currentIndex) {
        const statusText = document.getElementById('approvalStatusText');
        const progressBar = document.getElementById('approvalProgressBar');
        
        // Check if we need to process more items
        if (currentIndex < totalQuantity) {
            const itemNum = currentIndex + 1;
            statusText.textContent = `Sending item ${itemNum} of ${totalQuantity}...`;
            addLog(`Item ${itemNum}: Sending request...`);

            // Get CSRF token from meta tag (preferred) or fallback to form input
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                              document.querySelector('input[name="_token"]')?.value;
            
            const url = `{{ url('admin/confirm-orders') }}/${orderId}/approve-item`;

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(response => {
                if (!response.ok) {
                    // Try to parse JSON error message if possible
                    return response.text().then(text => {
                        try {
                            const json = JSON.parse(text);
                            throw new Error(json.message || response.statusText);
                        } catch (e) {
                            throw new Error(`Server Error (${response.status}): ${text.substring(0, 100)}...`);
                        }
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    addLog(`Item ${itemNum}: Success`, 'text-success');
                    const percent = Math.round((itemNum / totalQuantity) * 90); 
                    progressBar.style.width = `${percent}%`;
                    progressBar.textContent = `${percent}%`;
                    
                    // Process next item
                    setTimeout(() => processItems(orderId, totalQuantity, currentIndex + 1), 3000); 
                } else {
                    statusText.textContent = `Error on item ${itemNum}`;
                    statusText.classList.add('text-danger');
                    addLog(`Item ${itemNum}: Failed - ${data.message}`, 'text-danger');
                    document.getElementById('approvalCloseBtn').disabled = false;
                }
            })
            .catch(error => {
                statusText.textContent = `Network Error on item ${itemNum}`;
                statusText.classList.add('text-danger');
                addLog(`Item ${itemNum}: Network Error - ${error.message}`, 'text-danger');
                document.getElementById('approvalCloseBtn').disabled = false;
            });
        } else {
            // All items done, finalize
            finalizeOrder(orderId);
        }
    }

    function finalizeOrder(orderId) {
        const statusText = document.getElementById('approvalStatusText');
        const progressBar = document.getElementById('approvalProgressBar');

        statusText.textContent = 'Finalizing order...';
        addLog('Finalizing order status...', 'text-primary');

        // Get CSRF token from meta tag (preferred) or fallback to form input
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                          document.querySelector('input[name="_token"]')?.value;

        const url = `{{ url('admin/confirm-orders') }}/${orderId}/finalize`;

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    try {
                        const json = JSON.parse(text);
                        throw new Error(json.message || response.statusText);
                    } catch (e) {
                        throw new Error(`Server Error (${response.status}): ${text.substring(0, 100)}...`);
                    }
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                statusText.textContent = 'Order Completed Successfully!';
                statusText.classList.add('text-success');
                addLog('Order finalized.', 'text-success');
                progressBar.style.width = '100%';
                progressBar.textContent = '100%';
                progressBar.classList.remove('progress-bar-animated');
                progressBar.classList.add('bg-success');
                
                const closeBtn = document.getElementById('approvalCloseBtn');
                closeBtn.textContent = "Done (Reloading...)";
                closeBtn.disabled = false;
                
                setTimeout(() => location.reload(), 1500);
            } else {
                statusText.textContent = 'Error finalizing order';
                statusText.classList.add('text-danger');
                addLog(`Finalize Failed - ${data.message}`, 'text-danger');
                document.getElementById('approvalCloseBtn').disabled = false;
            }
        })
        .catch(error => {
            statusText.textContent = 'Network Error during finalize';
            statusText.classList.add('text-danger');
            addLog(`Finalize Network Error - ${error.message}`, 'text-danger');
            document.getElementById('approvalCloseBtn').disabled = false;
        });
    }

    function addLog(message, className = '') {
        const logList = document.getElementById('approvalLog');
        const li = document.createElement('li');
        li.className = `list-group-item py-1 ${className}`;
        li.textContent = message;
        logList.prepend(li);
    }

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
