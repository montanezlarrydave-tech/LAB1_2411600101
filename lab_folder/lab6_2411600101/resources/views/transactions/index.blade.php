@extends('layouts.app')

@section('title', 'Inventory Transactions')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i class="bi bi-arrow-left-right me-2 text-primary"></i>Inventory Transactions</h1>
        <p class="text-muted mb-0">Track all stock movements in real-time</p>
    </div>
    <div>
        <a href="{{ route('adjustments.create') }}" class="btn btn-outline-warning me-2">
            <i class="bi bi-sliders me-1"></i> Stock Adjustment
        </a>
        <a href="{{ route('transactions.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Record Transaction
        </a>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Product</label>
                <select name="product_id" class="form-select">
                    <option value="">All Products</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->name }} ({{ $p->sku }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Type</label>
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    @foreach($types as $value => $label)
                        <option value="{{ $value }}" {{ request('type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">From Date</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">To Date</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary me-2"><i class="bi bi-funnel"></i> Filter</button>
                <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Transactions Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($transactions->isEmpty())
            <div class="p-5 text-center text-muted">
                <i class="bi bi-inbox fs-1"></i>
                <p class="mt-3 mb-0">No transactions found.</p>
                <a href="{{ route('transactions.create') }}" class="btn btn-primary mt-3">Record First Transaction</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date / Time</th>
                            <th>Product</th>
                            <th>Type</th>
                            <th class="text-center">Qty</th>
                            <th>Reference</th>
                            <th>User</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $t)
                            <tr>
                                <td>
                                    <span class="fw-medium">{{ $t->created_at->format('M d, Y') }}</span><br>
                                    <small class="text-muted">{{ $t->created_at->format('h:i A') }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('products.show', $t->product_id) }}" class="text-decoration-none">
                                        {{ $t->product?->name ?? '—' }}
                                    </a><br>
                                    <small class="text-muted">{{ $t->product?->sku }}</small>
                                </td>
                                <td>
                                    <span class="badge {{ $t->type_badge_class }}">{{ $t->type_label }}</span>
                                </td>
                                <td class="text-center fw-bold {{ $t->signed_quantity > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $t->signed_quantity > 0 ? '+' : '' }}{{ $t->signed_quantity }}
                                </td>
                                <td>{{ $t->reference_number ?? '—' }}</td>
                                <td>{{ $t->user?->name ?? '—' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('transactions.show', $t) }}" class="btn btn-sm btn-outline-info" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <form action="{{ route('transactions.destroy', $t) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this transaction? Stock will be reversed.');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @if($transactions->hasPages())
        <div class="card-footer bg-transparent">{{ $transactions->links() }}</div>
    @endif
</div>
@endsection
