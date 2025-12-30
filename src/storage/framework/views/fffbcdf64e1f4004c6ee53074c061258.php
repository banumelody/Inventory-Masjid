<?php $__env->startSection('title', 'Pinjam Barang - Inventory Masjid'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Pinjam Barang</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="<?php echo e(route('loans.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            
            <div class="mb-4">
                <label for="item_id" class="block text-sm font-medium text-gray-700 mb-2">Barang *</label>
                <select name="item_id" id="item_id" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                    <option value="">Pilih Barang</option>
                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($item->id); ?>" 
                            data-available="<?php echo e($item->available_quantity); ?>"
                            data-unit="<?php echo e($item->unit); ?>"
                            <?php echo e((old('item_id') == $item->id || ($selectedItem && $selectedItem->id == $item->id)) ? 'selected' : ''); ?>>
                            <?php echo e($item->name); ?> (Tersedia: <?php echo e($item->available_quantity); ?> <?php echo e($item->unit); ?>)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="borrower_name" class="block text-sm font-medium text-gray-700 mb-2">Nama Peminjam *</label>
                    <input type="text" name="borrower_name" id="borrower_name" value="<?php echo e(old('borrower_name')); ?>" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label for="borrower_phone" class="block text-sm font-medium text-gray-700 mb-2">No. HP</label>
                    <input type="text" name="borrower_phone" id="borrower_phone" value="<?php echo e(old('borrower_phone')); ?>"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Jumlah *</label>
                    <input type="number" name="quantity" id="quantity" value="<?php echo e(old('quantity', 1)); ?>" min="1" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label for="borrowed_at" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pinjam *</label>
                    <input type="date" name="borrowed_at" id="borrowed_at" value="<?php echo e(old('borrowed_at', date('Y-m-d'))); ?>" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label for="due_at" class="block text-sm font-medium text-gray-700 mb-2">Jatuh Tempo</label>
                    <input type="date" name="due_at" id="due_at" value="<?php echo e(old('due_at')); ?>"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                </div>
            </div>

            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                <textarea name="notes" id="notes" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500"><?php echo e(old('notes')); ?></textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="<?php echo e(route('loans.index')); ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold">Batal</a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold">Simpan</button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/loans/create.blade.php ENDPATH**/ ?>