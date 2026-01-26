@forelse($orders as $order)
    <tr>
        <td>{{ $order->id }}</td>
        <td>{{ $order->created_at?->format('Y-m-d H:i') }}</td>
        <td><span class="badge bg-secondary text-uppercase">{{ $order->game_type }}</span></td>
        <td>{{ $order->player_id }}</td>
        <td>
            <div class="fw-semibold">{{ $order->product_name ?: $order->product_id }}</div>
            <div class="small text-muted">{{ $order->product_id }}</div>
        </td>
        <td>
            <strong>{{ number_format($order->amount, 0) }}</strong>
        </td>
        <td>{{ $order->kpay_phone }}</td>
        <td>
            <div class="d-flex align-items-center gap-2">
                <img src="{{ asset('adminimages/topups/' . $order->transaction_image) }}"
                        alt="KPay Screenshot"
                        class="rounded border flex-shrink-0"
                        style="height: 40px; width: auto; max-width: 60px; object-fit: cover; cursor: pointer;"
                        onclick="window.open('{{ asset('adminimages/topups/' . $order->transaction_image) }}', '_blank')"
                        onerror="this.src='https://via.placeholder.com/60x40?text=No+Img'">
                <a href="{{ asset('adminimages/topups/' . $order->transaction_image) }}"
                    target="_blank"
                    class="btn btn-sm btn-outline-primary flex-shrink-0">
                    View
                </a>
            </div>
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
        <td>
            @if($order->status === 'pending')
                <div class="d-flex gap-2">
                    <form method="POST" action="{{ route('admin.kpay.orders.approve', $order) }}">
                        @csrf
                        <button class="btn btn-success btn-sm"
                            onclick="return confirm('Approve and send this order to the game?');">
                            Approve & Deliver
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.kpay.orders.reject', $order) }}">
                        @csrf
                        <button class="btn btn-danger btn-sm"
                            onclick="return confirm('Reject this KPay order?');">
                            Reject
                        </button>
                    </form>
                </div>
            @else
                <span class="text-muted">No Action</span>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="10" class="text-center text-muted">
            No KPay orders found.
        </td>
    </tr>
@endforelse
