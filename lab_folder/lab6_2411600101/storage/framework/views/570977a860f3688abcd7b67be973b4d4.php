<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="bi bi-speedometer2 me-2"></i>Fitness Inventory Dashboard
    </h2>
    <a href="<?php echo e(route('products.create')); ?>" class="btn btn-primary">
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
                        <h2 class="mb-0"><?php echo e($totalProducts); ?></h2>
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
                        <h2 class="mb-0"><?php echo e($lowStockCount); ?></h2>
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
                        <h2 class="mb-0"><?php echo e($outOfStockCount); ?></h2>
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
                        <h2 class="mb-0">₱<?php echo e(number_format($totalValue, 0)); ?></h2>
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
                <a href="<?php echo e(route('products.index')); ?>" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <ul class="list-group list-group-flush">
                <?php $__empty_1 = true; $__currentLoopData = $recentProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?php echo e($product->name); ?></strong><br>
                            <small class="text-muted"><?php echo e($product->sku); ?> · <?php echo e($product->category); ?></small>
                        </div>
                        <span class="badge bg-secondary"><?php echo e($product->quantity); ?> pcs</span>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li class="list-group-item text-muted">No products yet.</li>
                <?php endif; ?>
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
                <?php $__empty_1 = true; $__currentLoopData = $lowStockItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?php echo e($product->name); ?></strong><br>
                            <small class="text-muted">Reorder at <?php echo e($product->reorder_level); ?></small>
                        </div>
                        <span class="badge <?php echo e($product->isOutOfStock() ? 'bg-danger' : 'bg-warning text-dark'); ?>">
                            <?php echo e($product->quantity); ?> left
                        </span>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li class="list-group-item text-muted">All products are well stocked</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp1\htdocs\lab_folder\lab5_2411600101\resources\views/dashboard.blade.php ENDPATH**/ ?>