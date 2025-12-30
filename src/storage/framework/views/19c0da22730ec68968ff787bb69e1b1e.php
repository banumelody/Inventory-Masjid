<?php $__env->startSection('title', 'Peminjaman - Inventory Masjid'); ?>

<?php $__env->startSection('content'); ?>
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Daftar Peminjaman</h1>
    <?php if(auth()->user()->canManageLoans()): ?>
    <a href="<?php echo e(route('loans.create')); ?>" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold">
        + Pinjam Barang
    </a>
    <?php endif; ?>
</div>

<!-- Filter -->
<div class="bg-white rounded-lg shadow p-4 mb-6">
    <form action="<?php echo e(route('loans.index')); ?>" method="GET" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Cari Peminjam</label>
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Nama peminjam..."
                class="w-full border border-gray-300 rounded-lg px-4 py-2">
        </div>
        <div class="w-48">
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                <option value="">Semua Status</option>
                <option value="borrowed" <?php echo e(request('status') == 'borrowed' ? 'selected' : ''); ?>>Dipinjam</option>
                <option value="returned" <?php echo e(request('status') == 'returned' ? 'selected' : ''); ?>>Sudah Kembali</option>
                <option value="overdue" <?php echo e(request('status') == 'overdue' ? 'selected' : ''); ?>>Terlambat</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold">Filter</button>
        <a href="<?php echo e(route('loans.index')); ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-semibold">Reset</a>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peminjam</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl Pinjam</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jatuh Tempo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php $__empty_1 = true; $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900"><?php echo e($loan->item->name); ?></div>
                    <div class="text-sm text-gray-500"><?php echo e($loan->item->category->name); ?></div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900"><?php echo e($loan->borrower_name); ?></div>
                    <?php if($loan->borrower_phone): ?>
                    <div class="text-sm text-gray-500"><?php echo e($loan->borrower_phone); ?></div>
                    <?php endif; ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($loan->quantity); ?> <?php echo e($loan->item->unit); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($loan->borrowed_at->format('d/m/Y')); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <?php echo e($loan->due_at ? $loan->due_at->format('d/m/Y') : '-'); ?>

                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-<?php echo e($loan->status_color); ?>-100 text-<?php echo e($loan->status_color); ?>-800">
                        <?php echo e($loan->status); ?>

                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                    <?php if(!$loan->isReturned() && auth()->user()->canManageLoans()): ?>
                    <a href="<?php echo e(route('loans.return', $loan)); ?>" class="text-green-600 hover:text-green-900">Kembalikan</a>
                    <?php endif; ?>
                    <?php if(auth()->user()->isAdmin()): ?>
                    <form action="<?php echo e(route('loans.destroy', $loan)); ?>" method="POST" class="inline" onsubmit="return confirm('Yakin hapus data peminjaman ini?')">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-gray-500">Belum ada data peminjaman.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="mt-4">
    <?php echo e($loans->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/loans/index.blade.php ENDPATH**/ ?>