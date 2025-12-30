<?php $__env->startSection('title', 'Dashboard - Inventory Masjid'); ?>

<?php $__env->startSection('content'); ?>
<h1 class="text-2xl font-bold text-gray-800 mb-6">📊 Dashboard</h1>

<!-- Stats Overview -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-3xl font-bold text-green-600"><?php echo e($stats['total_items']); ?></div>
        <div class="text-sm text-gray-500">Total Jenis Barang</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-3xl font-bold text-blue-600"><?php echo e(number_format($stats['total_quantity'])); ?></div>
        <div class="text-sm text-gray-500">Total Stok</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-3xl font-bold text-yellow-600"><?php echo e($stats['active_loans']); ?></div>
        <div class="text-sm text-gray-500">Sedang Dipinjam</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-3xl font-bold <?php echo e($stats['overdue_loans'] > 0 ? 'text-red-600' : 'text-gray-400'); ?>"><?php echo e($stats['overdue_loans']); ?></div>
        <div class="text-sm text-gray-500">Terlambat Kembali</div>
    </div>
</div>

<!-- Kondisi Barang -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex items-center">
            <span class="text-2xl mr-3">✅</span>
            <div>
                <div class="text-2xl font-bold text-green-700"><?php echo e($stats['items_good']); ?></div>
                <div class="text-sm text-green-600">Kondisi Baik</div>
            </div>
        </div>
    </div>
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex items-center">
            <span class="text-2xl mr-3">⚠️</span>
            <div>
                <div class="text-2xl font-bold text-yellow-700"><?php echo e($stats['items_need_repair']); ?></div>
                <div class="text-sm text-yellow-600">Perlu Perbaikan</div>
            </div>
        </div>
    </div>
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-center">
            <span class="text-2xl mr-3">❌</span>
            <div>
                <div class="text-2xl font-bold text-red-700"><?php echo e($stats['items_broken']); ?></div>
                <div class="text-sm text-red-600">Rusak</div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Overdue Loans Warning -->
    <?php if($overdueLoans->count() > 0): ?>
    <div class="bg-red-50 border border-red-300 rounded-lg p-4">
        <h2 class="text-lg font-semibold text-red-800 mb-3">🚨 Peminjaman Terlambat</h2>
        <div class="space-y-2">
            <?php $__currentLoopData = $overdueLoans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white rounded p-3 flex justify-between items-center">
                <div>
                    <div class="font-medium"><?php echo e($loan->item->name); ?></div>
                    <div class="text-sm text-gray-500"><?php echo e($loan->borrower_name); ?> - sejak <?php echo e($loan->borrowed_at->format('d/m/Y')); ?></div>
                </div>
                <div class="text-red-600 text-sm font-medium">
                    <?php echo e($loan->due_at->diffForHumans()); ?>

                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <a href="<?php echo e(route('loans.index', ['status' => 'overdue'])); ?>" class="block text-center text-red-600 hover:text-red-800 mt-3 text-sm">
            Lihat Semua →
        </a>
    </div>
    <?php endif; ?>

    <!-- Recent Items -->
    <div class="bg-white rounded-lg shadow p-4">
        <h2 class="text-lg font-semibold mb-3">📦 Barang Terbaru</h2>
        <div class="space-y-2">
            <?php $__empty_1 = true; $__currentLoopData = $recentItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="flex justify-between items-center py-2 border-b last:border-0">
                <div>
                    <a href="<?php echo e(route('items.show', $item)); ?>" class="font-medium text-blue-600 hover:text-blue-800"><?php echo e($item->name); ?></a>
                    <div class="text-sm text-gray-500"><?php echo e($item->category->name); ?></div>
                </div>
                <div class="text-sm text-gray-500"><?php echo e($item->quantity); ?> <?php echo e($item->unit); ?></div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-gray-500 text-sm">Belum ada barang.</p>
            <?php endif; ?>
        </div>
        <a href="<?php echo e(route('items.index')); ?>" class="block text-center text-blue-600 hover:text-blue-800 mt-3 text-sm">
            Lihat Semua →
        </a>
    </div>

    <!-- Recent Movements -->
    <div class="bg-white rounded-lg shadow p-4">
        <h2 class="text-lg font-semibold mb-3">📊 Mutasi Terbaru</h2>
        <div class="space-y-2">
            <?php $__empty_1 = true; $__currentLoopData = $recentMovements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="flex justify-between items-center py-2 border-b last:border-0">
                <div>
                    <div class="font-medium"><?php echo e($movement->item->name); ?></div>
                    <div class="text-sm text-gray-500"><?php echo e($movement->reason); ?></div>
                </div>
                <div class="text-sm font-medium <?php echo e($movement->type === 'in' ? 'text-green-600' : 'text-red-600'); ?>">
                    <?php echo e($movement->type === 'in' ? '+' : '-'); ?><?php echo e($movement->quantity); ?>

                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-gray-500 text-sm">Belum ada mutasi.</p>
            <?php endif; ?>
        </div>
        <a href="<?php echo e(route('stock-movements.index')); ?>" class="block text-center text-blue-600 hover:text-blue-800 mt-3 text-sm">
            Lihat Semua →
        </a>
    </div>

    <!-- Items by Category -->
    <div class="bg-white rounded-lg shadow p-4">
        <h2 class="text-lg font-semibold mb-3">📁 Per Kategori</h2>
        <div class="space-y-2">
            <?php $__currentLoopData = $itemsByCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex justify-between items-center py-2 border-b last:border-0">
                <div class="font-medium"><?php echo e($category->name); ?></div>
                <div class="text-sm text-gray-500"><?php echo e($category->items_count); ?> barang</div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <!-- Items by Location -->
    <div class="bg-white rounded-lg shadow p-4">
        <h2 class="text-lg font-semibold mb-3">📍 Per Lokasi</h2>
        <div class="space-y-2">
            <?php $__currentLoopData = $itemsByLocation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex justify-between items-center py-2 border-b last:border-0">
                <div class="font-medium"><?php echo e($location->name); ?></div>
                <div class="text-sm text-gray-500"><?php echo e($location->items_count); ?> barang</div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-lg shadow p-4">
    <h2 class="text-lg font-semibold mb-3">⚡ Aksi Cepat</h2>
    <div class="flex flex-wrap gap-3">
        <?php if(auth()->user()->canEditItems()): ?>
        <a href="<?php echo e(route('items.create')); ?>" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">+ Tambah Barang</a>
        <?php endif; ?>
        <?php if(auth()->user()->canManageLoans()): ?>
        <a href="<?php echo e(route('loans.create')); ?>" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">📤 Pinjamkan</a>
        <?php endif; ?>
        <?php if(auth()->user()->canManageStock()): ?>
        <a href="<?php echo e(route('stock-movements.create')); ?>" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg">📊 Mutasi Stok</a>
        <?php endif; ?>
        <a href="<?php echo e(route('export.index')); ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">📥 Export</a>
        <a href="<?php echo e(route('feedbacks.create')); ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">💬 Feedback</a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/dashboard/index.blade.php ENDPATH**/ ?>