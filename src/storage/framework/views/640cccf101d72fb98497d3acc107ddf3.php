<?php $__env->startSection('title', 'Kembalikan Barang - Inventory Masjid'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Kembalikan Barang</h1>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Detail Peminjaman</h2>
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-500">Barang</dt>
                <dd class="font-medium"><?php echo e($loan->item->name); ?></dd>
            </div>
            <div>
                <dt class="text-gray-500">Peminjam</dt>
                <dd class="font-medium"><?php echo e($loan->borrower_name); ?></dd>
            </div>
            <div>
                <dt class="text-gray-500">Jumlah</dt>
                <dd class="font-medium"><?php echo e($loan->quantity); ?> <?php echo e($loan->item->unit); ?></dd>
            </div>
            <div>
                <dt class="text-gray-500">Tanggal Pinjam</dt>
                <dd class="font-medium"><?php echo e($loan->borrowed_at->format('d/m/Y')); ?></dd>
            </div>
            <?php if($loan->due_at): ?>
            <div>
                <dt class="text-gray-500">Jatuh Tempo</dt>
                <dd class="font-medium <?php echo e($loan->isOverdue() ? 'text-red-600' : ''); ?>">
                    <?php echo e($loan->due_at->format('d/m/Y')); ?>

                    <?php if($loan->isOverdue()): ?>
                        (Terlambat!)
                    <?php endif; ?>
                </dd>
            </div>
            <?php endif; ?>
        </dl>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Form Pengembalian</h2>
        <form action="<?php echo e(route('loans.return.store', $loan)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="returned_at" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Kembali *</label>
                    <input type="date" name="returned_at" id="returned_at" value="<?php echo e(old('returned_at', date('Y-m-d'))); ?>" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label for="returned_condition" class="block text-sm font-medium text-gray-700 mb-2">Kondisi Saat Kembali *</label>
                    <select name="returned_condition" id="returned_condition" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                        <option value="baik" <?php echo e(old('returned_condition') == 'baik' ? 'selected' : ''); ?>>Baik</option>
                        <option value="perlu_perbaikan" <?php echo e(old('returned_condition') == 'perlu_perbaikan' ? 'selected' : ''); ?>>Perlu Perbaikan</option>
                        <option value="rusak" <?php echo e(old('returned_condition') == 'rusak' ? 'selected' : ''); ?>>Rusak</option>
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan Pengembalian</label>
                <textarea name="notes" id="notes" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500"><?php echo e(old('notes')); ?></textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="<?php echo e(route('loans.index')); ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold">Batal</a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold">Konfirmasi Pengembalian</button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/loans/return.blade.php ENDPATH**/ ?>