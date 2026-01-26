@extends('admin.layout')

@section('page_title', 'Bank & Profit Report')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-university me-2"></i> Bank & Profit Report
        </h1>
        
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cleanupModal">
                <i class="fas fa-trash-alt me-1"></i> Cleanup
            </button>

            <form action="{{ route('admin.bank.index') }}" method="GET" class="d-flex align-items-center gap-2">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-calendar"></i>
                    </span>
                    <input type="date" name="date" class="form-control border-start-0 ps-0" 
                           value="{{ $isAllTime ? '' : $date }}" 
                           {{ $isAllTime ? 'disabled' : '' }}
                           onchange="this.form.submit()">
                </div>
                <div class="form-check form-switch mb-0 d-flex align-items-center bg-white p-2 rounded border">
                    <input class="form-check-input mt-0 me-2" type="checkbox" name="all_time" value="1" id="allTimeCheck" {{ $isAllTime ? 'checked' : '' }} onchange="this.form.submit()">
                    <label class="form-check-label mb-0 text-nowrap" for="allTimeCheck">All Time</label>
                </div>
            </form>
        </div>
    </div>



    {{-- Summary Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="card border-start-lg border-start-primary h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase fw-bold text-xs text-primary mb-1">Total Sales</div>
                            <div class="h3 mb-0 fw-bold text-gray-800">{{ number_format($totalSales) }} Ks</div>
                        </div>
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 text-primary">
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card border-start-lg border-start-danger h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase fw-bold text-xs text-danger mb-1">Total Cost (API)</div>
                            <div class="h3 mb-0 fw-bold text-gray-800">{{ number_format($totalCost) }} Ks</div>
                        </div>
                        <div class="rounded-circle bg-danger bg-opacity-10 p-3 text-danger">
                            <i class="fas fa-file-invoice-dollar fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card border-start-lg border-start-success h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase fw-bold text-xs text-success mb-1">Total Profit</div>
                            <div class="h3 mb-0 fw-bold text-gray-800">{{ number_format($totalProfit) }} Ks</div>
                        </div>
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 text-success">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Transaction History ({{ $isAllTime ? 'All Time' : \Carbon\Carbon::parse($date)->format('F j, Y') }})</h6>
        </div>
        <div class="card-body">
            @if($orders->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-receipt fa-3x text-gray-300 mb-3"></i>
                    <p class="text-muted">No transactions found for this period.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Game</th>
                                <th>User</th>
                                <th>Product</th>
                                <th class="text-end">Selling Price</th>
                                <th class="text-end">Cost Price</th>
                                <th class="text-end">Profit</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <span class="badge bg-secondary text-uppercase">{{ $order->game }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $order->user->name ?? 'Unknown' }}</div>
                                        <div class="small text-muted">{{ $order->player_id }}</div>
                                    </td>
                                    <td>
                                        {{ $order->product_name }}<br>
                                        <small class="text-muted">{{ $order->product_id }}</small>
                                    </td>
                                    <td class="text-end fw-bold">{{ number_format($order->selling_price) }} Ks</td>
                                    <td class="text-end text-danger">{{ number_format($order->cost_price) }} Ks</td>
                                    <td class="text-end text-success fw-bold">{{ number_format($order->profit) }} Ks</td>
                                    <td class="text-center">
                                        <span class="badge bg-success">{{ ucfirst($order->status) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="4" class="text-end">Totals:</td>
                                <td class="text-end">{{ number_format($totalSales) }} Ks</td>
                                <td class="text-end text-danger">{{ number_format($totalCost) }} Ks</td>
                                <td class="text-end text-success">{{ number_format($totalProfit) }} Ks</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
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
                <h5 class="modal-title">Cleanup Old Orders</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.bank.delete_old') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Delete orders older than (days):</label>
                        <input type="number" name="days" class="form-control" value="90" min="1" required>
                        <div class="form-text text-danger">
                            <i class="fas fa-exclamation-triangle me-1"></i> Warning: This action cannot be undone. 
                            This will remove financial records.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Orders</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.border-start-lg {
    border-left: 4px solid;
}
.border-start-primary { border-left-color: #4e73df !important; }
.border-start-success { border-left-color: #1cc88a !important; }
.border-start-danger { border-left-color: #e74a3b !important; }
.text-xs { font-size: .7rem; }
</style>
@endsection
