<?php $__env->startSection('title', 'Tambah Mutasi - Inventory Masjid'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Tambah Mutasi Stok</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="<?php echo e(route('stock-movements.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            
            <div class="mb-4">
                <label for="item_id" class="block text-sm font-medium text-gray-700 mb-2">Barang *</label>
                <select name="item_id" id="item_id" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                    <option value="">Pilih Barang</option>
                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($item->id); ?>" 
                            data-stock="<?php echo e($item->quantity); ?>"
                            data-unit="<?php echo e($item->unit); ?>"
                            <?php echo e((old('item_id') == $item->id || ($selectedItem && $selectedItem->id == $item->id)) ? 'selected' : ''); ?>>
                            <?php echo e($item->name); ?> (Stok: <?php echo e($item->quantity); ?> <?php echo e($item->unit); ?>)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Jenis Mutasi *</label>
                    <select name="type" id="type" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                        <option value="in" <?php echo e(old('type') == 'in' ? 'selected' : ''); ?>>Masuk (+)</option>
                        <option value="out" <?php echo e(old('type') == 'out' ? 'selected' : ''); ?>>Keluar (-)</option>
                    </select>
                </div>
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Jumlah *</label>
                    <input type="number" name="quantity" id="quantity" value="<?php echo e(old('quantity', 1)); ?>" min="1" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Alasan *</label>
                    <select name="reason" id="reason" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                        <option value="">Pilih Alasan</option>
                        <option value="Pembelian" <?php echo e(old('reason') == 'Pembelian' ? 'selected' : ''); ?>>Pembelian</option>
                        <option value="Donasi Masuk" <?php echo e(old('reason') == 'Donasi Masuk' ? 'selected' : ''); ?>>Donasi Masuk</option>
                        <option value="Penyesuaian Stok" <?php echo e(old('reason') == 'Penyesuaian Stok' ? 'selected' : ''); ?>>Penyesuaian Stok</option>
                        <option value="Barang Rusak" <?php echo e(old('reason') == 'Barang Rusak' ? 'selected' : ''); ?>>Barang Rusak</option>
                        <option value="Barang Hilang" <?php echo e(old('reason') == 'Barang Hilang' ? 'selected' : ''); ?>>Barang Hilang</option>
                        <option value="Donasi Keluar" <?php echo e(old('reason') == 'Donasi Keluar' ? 'selected' : ''); ?>>Donasi Keluar</option>
                        <option value="Lainnya" <?php echo e(old('reason') == 'Lainnya' ? 'selected' : ''); ?>>Lainnya</option>
                    </select>
                </div>
                <div>
                    <label for="moved_at" class="block text-sm font-medium text-gray-700 mb-2">Tanggal *</label>
                    <input type="date" name="moved_at" id="moved_at" value="<?php echo e(old('moved_at', date('Y-m-d'))); ?>" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                </div>
            </div>

            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                <textarea name="notes" id="notes" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500"><?php echo e(old('notes')); ?></textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="<?php echo e(route('stock-movements.index')); ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold">Batal</a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold">Simpan</button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/stock-movements/create.blade.php ENDPATH**/ ?>