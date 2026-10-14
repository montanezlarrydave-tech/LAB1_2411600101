<?php $__env->startSection('title', $product->name); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-box me-2"></i>Product Details</h5>
                <div>
                    <a href="<?php echo e(route('products.edit', $product)); ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <a href="<?php echo e(route('products.index')); ?>" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">SKU</div>
                    <div class="col-sm-8"><code><?php echo e($product->sku); ?></code></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Product Name</div>
                    <div class="col-sm-8 fw-semibold"><?php echo e($product->name); ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Category</div>
                    <div class="col-sm-8">
                        <span class="badge bg-info text-dark"><?php echo e($product->category); ?></span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Description</div>
                    <div class="col-sm-8"><?php echo e($product->description ?: '—'); ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Quantity in Stock</div>
                    <div class="col-sm-8">
                        <span class="fw-bold fs-5"><?php echo e($product->quantity); ?></span>
                        <?php if($product->isOutOfStock()): ?>
                            <span class="badge bg-danger ms-2">Out of Stock</span>
                        <?php elseif($product->isLowStock()): ?>
                            <span class="badge bg-warning text-dark ms-2">Low Stock</span>
                        <?php else: ?>
                            <span class="badge bg-success ms-2">In Stock</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Reorder Level</div>
                    <div class="col-sm-8"><?php echo e($product->reorder_level); ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Unit Price</div>
                    <div class="col-sm-8">₱<?php echo e(number_format($product->unit_price, 2)); ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Total Value</div>
                    <div class="col-sm-8 fw-bold text-primary">₱<?php echo e(number_format($product->total_value, 2)); ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Supplier</div>
                    <div class="col-sm-8"><?php echo e($product->supplier ?: '—'); ?></div>
                </div>
                <div class="row">
                    <div class="col-sm-4 text-muted">Last Updated</div>
                    <div class="col-sm-8"><?php echo e($product->updated_at->format('M d, Y h:i A')); ?></div>
                </div>
            </div>
            <div class="card-footer bg-white">
                <form action="<?php echo e(route('products.destroy', $product)); ?>" method="POST"
                      onsubmit="return confirm('Delete this product permanently?')">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-trash me-1"></i> Delete Product
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp1\htdocs\lab6_2411600101\resources\views/products/show.blade.php ENDPATH**/ ?>