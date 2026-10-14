<?php $__env->startSection('title', 'Inventory Transactions'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i class="bi bi-arrow-left-right me-2 text-primary"></i>Inventory Transactions</h1>
        <p class="text-muted mb-0">Track all stock movements in real-time</p>
    </div>
    <div>
        <a href="<?php echo e(route('adjustments.create')); ?>" class="btn btn-outline-warning me-2">
            <i class="bi bi-sliders me-1"></i> Stock Adjustment
        </a>
        <a href="<?php echo e(route('transactions.create')); ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Record Transaction
        </a>
    </div>
</div>


<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Product</label>
                <select name="product_id" class="form-select">
                    <option value="">All Products</option>
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($p->id); ?>" <?php echo e(request('product_id') == $p->id ? 'selected' : ''); ?>>
                            <?php echo e($p->name); ?> (<?php echo e($p->sku); ?>)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Type</label>
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($value); ?>" <?php echo e(request('type') === $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">From Date</label>
                <input type="date" name="date_from" class="form-control" value="<?php echo e(request('date_from')); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">To Date</label>
                <input type="date" name="date_to" class="form-control" value="<?php echo e(request('date_to')); ?>">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary me-2"><i class="bi bi-funnel"></i> Filter</button>
                <a href="<?php echo e(route('transactions.index')); ?>" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>


<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if($transactions->isEmpty()): ?>
            <div class="p-5 text-center text-muted">
                <i class="bi bi-inbox fs-1"></i>
                <p class="mt-3 mb-0">No transactions found.</p>
                <a href="<?php echo e(route('transactions.create')); ?>" class="btn btn-primary mt-3">Record First Transaction</a>
            </div>
        <?php else: ?>
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
                        <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <span class="fw-medium"><?php echo e($t->created_at->format('M d, Y')); ?></span><br>
                                    <small class="text-muted"><?php echo e($t->created_at->format('h:i A')); ?></small>
                                </td>
                                <td>
                                    <a href="<?php echo e(route('products.show', $t->product_id)); ?>" class="text-decoration-none">
                                        <?php echo e($t->product?->name ?? '—'); ?>

                                    </a><br>
                                    <small class="text-muted"><?php echo e($t->product?->sku); ?></small>
                                </td>
                                <td>
                                    <span class="badge <?php echo e($t->type_badge_class); ?>"><?php echo e($t->type_label); ?></span>
                                </td>
                                <td class="text-center fw-bold <?php echo e($t->signed_quantity > 0 ? 'text-success' : 'text-danger'); ?>">
                                    <?php echo e($t->signed_quantity > 0 ? '+' : ''); ?><?php echo e($t->signed_quantity); ?>

                                </td>
                                <td><?php echo e($t->reference_number ?? '—'); ?></td>
                                <td><?php echo e($t->user?->name ?? '—'); ?></td>
                                <td class="text-center">
                                    <a href="<?php echo e(route('transactions.show', $t)); ?>" class="btn btn-sm btn-outline-info" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <form action="<?php echo e(route('transactions.destroy', $t)); ?>" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this transaction? Stock will be reversed.');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    <?php if($transactions->hasPages()): ?>
        <div class="card-footer bg-transparent"><?php echo e($transactions->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp1\htdocs\lab6_2411600101\resources\views/transactions/index.blade.php ENDPATH**/ ?>