@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="bi bi-speedometer2 me-2"></i>Fitness Inventory Dashboard
    </h2>
    <a href="{{ route('products.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add Product
    </a>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="opacity-75">Total Products</h6>
                        <h2 class="mb-0">{{ $totalProducts }}</h2>
                    </div>
                    <i class="bi bi-box-seam fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card bg-warning text-dark h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Low Stock</h6>
                        <h2 class="mb-0">{{ $lowStockCount }}</h2>
                    </div>
                    <i class="bi bi-exclamation-triangle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card bg-danger text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="opacity-75">Out of Stock</h6>
                        <h2 class="mb-0">{{ $outOfStockCount }}</h2>
                    </div>
                    <i class="bi bi-x-circle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="opacity-75">Inventory Value</h6>
                        <h2 class="mb-0">₱{{ number_format($totalValue, 0) }}</h2>
                    </div>
                    <i class="bi bi-currency-dollar fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong><i class="bi bi-clock-history me-2"></i>Recent Products</strong>
                <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <ul class="list-group list-group-flush">
                @forelse($recentProducts as $product)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $product->name }}</strong><br>
                            <small class="text-muted">{{ $product->sku }} · {{ $product->category }}</small>
                        </div>
                        <span class="badge bg-secondary">{{ $product->quantity }} pcs</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">No products yet.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card h-100 border-warning">
            <div class="card-header bg-warning-subtle">
                <strong class="text-warning-emphasis">
                    <i class="bi bi-exclamation-triangle me-2"></i>Low Stock Alerts
                </strong>
            </div>
            <ul class="list-group list-group-flush">
                @forelse($lowStockItems as $product)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $product->name }}</strong><br>
                            <small class="text-muted">Reorder at {{ $product->reorder_level }}</small>
                        </div>
                        <span class="badge {{ $product->isOutOfStock() ? 'bg-danger' : 'bg-warning text-dark' }}">
                            {{ $product->quantity }} left
                        </span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">All products are well stocked</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection