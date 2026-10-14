@extends('layouts.app')

@section('title', 'Transaction Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Transaction #{{ $transaction->id }}</h5>
                <span class="badge {{ $transaction->type_badge_class }} fs-6">{{ $transaction->type_label }}</span>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="text-uppercase text-muted small">Product</h6>
                        <p class="mb-1 fw-bold">
                            <a href="{{ route('products.show', $transaction->product_id) }}">
                                {{ $transaction->product?->name }}
                            </a>
                        </p>
                        <p class="text-muted small mb-0">SKU: {{ $transaction->product?->sku }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-uppercase text-muted small">Quantity Change</h6>
                        <p class="fs-4 fw-bold mb-0 {{ $transaction->signed_quantity > 0 ? 'text-success' : 'text-danger' }}">
                            {{ $transaction->signed_quantity > 0 ? '+' : '' }}{{ $transaction->signed_quantity }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-uppercase text-muted small">Reference Number</h6>
                        <p>{{ $transaction->reference_number ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-uppercase text-muted small">Performed By</h6>
                        <p>{{ $transaction->user?->name ?? '—' }}</p>
                    </div>
                    <div class="col-12">
                        <h6 class="text-uppercase text-muted small">Notes</h6>
                        <p>{{ $transaction->notes ?? 'No notes.' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-uppercase text-muted small">Date & Time</h6>
                        <p>{{ $transaction->created_at->format('F d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent d-flex justify-content-between">
                <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to List
                </a>
                <form action="{{ route('transactions.destroy', $transaction) }}" method="POST"
                      onsubmit="return confirm('Delete this transaction? Stock will be reversed.');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i> Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
