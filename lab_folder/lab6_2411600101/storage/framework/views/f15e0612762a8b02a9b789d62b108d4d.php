<?php $__env->startSection('title', 'Stock Adjustment'); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-sliders me-2"></i>Stock Adjustment</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Use this form when the physical count does not match the system quantity (e.g. damage, theft, count error).</p>

                <form action="<?php echo e(route('adjustments.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>

                    <div class="mb-3">
                        <label for="product_id" class="form-label">Product <span class="text-danger">*</span></label>
                        <select name="product_id" id="product_id" class="form-select <?php $__errorArgs = ['product_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">Select Product</option>
                            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($product->id); ?>" data-qty="<?php echo e($product->quantity); ?>"
                                    <?php echo e(old('product_id') == $product->id ? 'selected' : ''); ?>>
                                    <?php echo e($product->name); ?> (<?php echo e($product->sku); ?>)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['product_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Current System Quantity</label>
                        <input type="text" id="system_qty" class="form-control" readonly value="—">
                    </div>

                    <div class="mb-3">
                        <label for="physical_count" class="form-label">Physical Count <span class="text-danger">*</span></label>
                        <input type="number" name="physical_count" id="physical_count" min="0"
                               class="form-control <?php $__errorArgs = ['physical_count'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e(old('physical_count')); ?>" required>
                        <?php $__errorArgs = ['physical_count'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <div id="diffInfo" class="form-text"></div>
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                        <select name="reason" id="reason" class="form-select <?php $__errorArgs = ['reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">Select Reason</option>
                            <option value="Physical Count Discrepancy">Physical Count Discrepancy</option>
                            <option value="Damaged Goods">Damaged Goods</option>
                            <option value="Theft / Loss">Theft / Loss</option>
                            <option value="System Error Correction">System Error Correction</option>
                            <option value="Other">Other</option>
                        </select>
                        <?php $__errorArgs = ['reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea name="notes" id="notes" rows="2" class="form-control"><?php echo e(old('notes')); ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?php echo e(route('transactions.index')); ?>" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-lg me-1"></i> Apply Adjustment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp1\htdocs\lab6_2411600101\resources\views/transactions/adjustment.blade.php ENDPATH**/ ?>