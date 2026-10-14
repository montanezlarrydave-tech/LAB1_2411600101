@extends('layouts.app')

@section('title', 'Stock Adjustment')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-sliders me-2"></i>Stock Adjustment</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Use this form when the physical count does not match the system quantity (e.g. damage, theft, count error).</p>

                <form action="{{ route('adjustments.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="product_id" class="form-label">Product <span class="text-danger">*</span></label>
                        <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-qty="{{ $product->quantity }}"
                                    {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} ({{ $product->sku }})
                                </option>
                            @endforeach
                        </select>
                        @error('product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Current System Quantity</label>
                        <input type="text" id="system_qty" class="form-control" readonly value="—">
                    </div>

                    <div class="mb-3">
                        <label for="physical_count" class="form-label">Physical Count <span class="text-danger">*</span></label>
                        <input type="number" name="physical_count" id="physical_count" min="0"
                               class="form-control @error('physical_count') is-invalid @enderror"
                               value="{{ old('physical_count') }}" required>
                        @error('physical_count') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div id="diffInfo" class="form-text"></div>
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                        <select name="reason" id="reason" class="form-select @error('reason') is-invalid @enderror" required>
                            <option value="">Select Reason</option>
                            <option value="Physical Count Discrepancy">Physical Count Discrepancy</option>
                            <option value="Damaged Goods">Damaged Goods</option>
                            <option value="Theft / Loss">Theft / Loss</option>
                            <option value="System Error Correction">System Error Correction</option>
                            <option value="Other">Other</option>
                        </select>
                        @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea name="notes" id="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-lg me-1"></i> Apply Adjustment
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
const productSelect = document.getElementById('product_id');
const systemQty = document.getElementById('system_qty');
const physicalCount = document.getElementById('physical_count');
const diffInfo = document.getElementById('diffInfo');

productSelect.addEventListener('change', function () {
    const opt = this.options[this.selectedIndex];
    systemQty.value = opt.dataset.qty ?? '—';
    updateDiff();
});

physicalCount.addEventListener('input', updateDiff);

function updateDiff() {
    const sys = parseInt(systemQty.value) || 0;
    const phys = parseInt(physicalCount.value);
    if (isNaN(phys)) { diffInfo.innerHTML = ''; return; }
    const diff = phys - sys;
    if (diff === 0) {
        diffInfo.innerHTML = '<span class="text-muted">No change needed.</span>';
    } else if (diff > 0) {
        diffInfo.innerHTML = `<span class="text-success">Will increase stock by +${diff}</span>`;
    } else {
        diffInfo.innerHTML = `<span class="text-danger">Will decrease stock by ${diff}</span>`;
    }
}
</script>
@endpush
