@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12 mb-3">
            <h2 class="h4 mb-1">My Orders</h2>
            <p class="text-muted mb-0">View your past and current purchase requests.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('user.kpay.orders') }}" class="row g-3 align-items-end">
                <div class="col-sm-4">
                    <label for="game_type" class="form-label mb-1">Game</label>
                    <select name="game_type" id="game_type" class="form-select">
                        <option value="">All</option>
                        @foreach($games as $game)
                            <option value="{{ $game }}" {{ request('game_type') === $game ? 'selected' : '' }}>
                                {{ strtoupper($game) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-4">
                    <label for="status" class="form-label mb-1">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                    <a href="{{ route('user.kpay.orders') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($orders->isEmpty())
                <div class="p-4 text-center text-muted">
                    <i class="fas fa-receipt fa-2x mb-2"></i>
                    <p class="mb-0">You have no orders yet.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Game</th>
                                <th>Player ID</th>
                                <th>Product</th>
                                <th class="text-end">Amount (MMK)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td><span class="badge bg-secondary text-uppercase">{{ $order->game_type }}</span></td>
                                    <td>{{ $order->player_id }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $order->product_name ?: $order->product_id }}</div>
                                        <div class="small text-muted">{{ $order->product_id }}</div>
                                    </td>
                                    <td class="text-end">
                                        <strong>{{ number_format($order->amount, 0) }}</strong>
                                    </td>
                                    <td>
                                        @if($order->status === 'pending')
                                            <span class="badge bg-warning-subtle text-warning">Pending</span>
                                        @elseif($order->status === 'approved')
                                            <span class="badge bg-success-subtle text-success">Approved</span>
                                        @elseif($order->status === 'cancelled')
                                            <span class="badge bg-danger-subtle text-danger">Cancelled</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">{{ ucfirst($order->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-3">
                    {{ $orders->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
