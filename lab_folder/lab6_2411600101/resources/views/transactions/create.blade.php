@extends('layouts.app')

@section('title', 'Record Transaction')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Record Inventory Transaction</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('transactions.store') }}" method="POST" id="transactionForm">
                    @csrf

                    <div class="mb-3">
                        <label for="product_id" class="form-label">Product <span class="text-danger">*</span></label>
                        <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}"
                                        data-qty="{{ $product->quantity }}"
                                        data-reorder="{{ $product->reorder_level }}"
                                        {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} ({{ $product->sku }}) — Qty: {{ $product->quantity }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div id="stockInfo" class="form-text mt-1"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Transaction Type <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            @foreach($types as $value => $label)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="type" id="type_{{ $value }}"
                                               value="{{ $value }}" {{ old('type', 'stock_in') === $value ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="type_{{ $value }}">{{ $label }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('type') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" id="quantity" min="1"
                               class="form-control @error('quantity') is-invalid @enderror"
                               value="{{ old('quantity') }}" required>
                        @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="reference_number" class="form-label">Reference Number</label>
                        <input type="text" name="reference_number" id="reference_number"
                               class="form-control @error('reference_number') is-invalid @enderror"
                               value="{{ old('reference_number') }}" placeholder="e.g. PO-2026-001, SO-1234">
                        @error('reference_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Record Transaction
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('product_id').addEventListener('change', function () {
    const opt = this.options[this.selectedIndex];
    const info = document.getElementById('stockInfo');
    if (!this.value) {
        info.innerHTML = '';
        return;
    }
    const qty = opt.dataset.qty;
    const reorder = opt.dataset.reorder;
    let status = 'In Stock';
    let cls = 'text-success';
    if (parseInt(qty) <= 0) { status = 'Out of Stock'; cls = 'text-danger'; }
    else if (parseInt(qty) <= parseInt(reorder)) { status = 'Low Stock'; cls = 'text-warning'; }

    info.innerHTML = `Current stock: <strong>${qty}</strong> | Reorder level: ${reorder} | Status: <span class="${cls}">${status}</span>`;
});
</script>
@endpush
