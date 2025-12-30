<?php $__env->startSection('title', 'Mutasi Stok - Inventory Masjid'); ?>

<?php $__env->startSection('content'); ?>
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Riwayat Mutasi Stok</h1>
    <?php if(auth()->user()->canManageStock()): ?>
    <a href="<?php echo e(route('stock-movements.create')); ?>" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold">
        + Tambah Mutasi
    </a>
    <?php endif; ?>
</div>

<!-- Filter -->
<div class="bg-white rounded-lg shadow p-4 mb-6">
    <form action="<?php echo e(route('stock-movements.index')); ?>" method="GET" class="flex flex-wrap gap-4 items-end">
        <div class="w-64">
            <label class="block text-sm font-medium text-gray-700 mb-1">Barang</label>
            <select name="item_id" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                <option value="">Semua Barang</option>
                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($item->id); ?>" <?php echo e(request('item_id') == $item->id ? 'selected' : ''); ?>>
                        <?php echo e($item->name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="w-40">
            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis</label>
            <select name="type" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                <option value="">Semua</option>
                <option value="in" <?php echo e(request('type') == 'in' ? 'selected' : ''); ?>>Masuk</option>
                <option value="out" <?php echo e(request('type') == 'out' ? 'selected' : ''); ?>>Keluar</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold">Filter</button>
        <a href="<?php echo e(route('stock-movements.index')); ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-semibold">Reset</a>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alasan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php $__empty_1 = true; $__currentLoopData = $movements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($movement->moved_at->format('d/m/Y')); ?></td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900"><?php echo e($movement->item->name); ?></div>
                    <div class="text-sm text-gray-500"><?php echo e($movement->item->category->name); ?></div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-<?php echo e($movement->type_color); ?>-100 text-<?php echo e($movement->type_color); ?>-800">
                        <?php echo e($movement->type_label); ?>

                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <span class="font-medium text-<?php echo e($movement->type_color); ?>-600">
                        <?php echo e($movement->type === 'in' ? '+' : '-'); ?><?php echo e($movement->quantity); ?>

                    </span>
                    <?php echo e($movement->item->unit); ?>

                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($movement->reason); ?></td>
                <td class="px-6 py-4 text-sm text-gray-500"><?php echo e($movement->notes ?? '-'); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada data mutasi stok.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="mt-4">
    <?php echo e($movements->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/stock-movements/index.blade.php ENDPATH**/ ?>