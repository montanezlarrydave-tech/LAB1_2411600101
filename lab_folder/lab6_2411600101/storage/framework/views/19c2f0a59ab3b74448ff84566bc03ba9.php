<?php $__env->startSection('title', 'Transaction Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Transaction #<?php echo e($transaction->id); ?></h5>
                <span class="badge <?php echo e($transaction->type_badge_class); ?> fs-6"><?php echo e($transaction->type_label); ?></span>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="text-uppercase text-muted small">Product</h6>
                        <p class="mb-1 fw-bold">
                            <a href="<?php echo e(route('products.show', $transaction->product_id)); ?>">
                                <?php echo e($transaction->product?->name); ?>

                            </a>
                        </p>
                        <p class="text-muted small mb-0">SKU: <?php echo e($transaction->product?->sku); ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-uppercase text-muted small">Quantity Change</h6>
                        <p class="fs-4 fw-bold mb-0 <?php echo e($transaction->signed_quantity > 0 ? 'text-success' : 'text-danger'); ?>">
                            <?php echo e($transaction->signed_quantity > 0 ? '+' : ''); ?><?php echo e($transaction->signed_quantity); ?>

                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-uppercase text-muted small">Reference Number</h6>
                        <p><?php echo e($transaction->reference_number ?? '—'); ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-uppercase text-muted small">Performed By</h6>
                        <p><?php echo e($transaction->user?->name ?? '—'); ?></p>
                    </div>
                    <div class="col-12">
                        <h6 class="text-uppercase text-muted small">Notes</h6>
                        <p><?php echo e($transaction->notes ?? 'No notes.'); ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-uppercase text-muted small">Date & Time</h6>
                        <p><?php echo e($transaction->created_at->format('F d, Y h:i A')); ?></p>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent d-flex justify-content-between">
                <a href="<?php echo e(route('transactions.index')); ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to List
                </a>
                <form action="<?php echo e(route('transactions.destroy', $transaction)); ?>" method="POST"
                      onsubmit="return confirm('Delete this transaction? Stock will be reversed.');">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i> Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp1\htdocs\lab6_2411600101\resources\views/transactions/show.blade.php ENDPATH**/ ?>