<?php $__env->startSection('title', 'Tambah Barang - Inventory Masjid'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Tambah Barang</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="<?php echo e(route('items.store')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Barang *</label>
                <input type="text" name="name" id="name" value="<?php echo e(old('name')); ?>" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
                    <select name="category_id" id="category_id" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                        <option value="">Pilih Kategori</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($category->id); ?>" <?php echo e(old('category_id') == $category->id ? 'selected' : ''); ?>>
                                <?php echo e($category->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label for="location_id" class="block text-sm font-medium text-gray-700 mb-2">Lokasi *</label>
                    <select name="location_id" id="location_id" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                        <option value="">Pilih Lokasi</option>
                        <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($location->id); ?>" <?php echo e(old('location_id') == $location->id ? 'selected' : ''); ?>>
                                <?php echo e($location->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Jumlah *</label>
                    <input type="number" name="quantity" id="quantity" value="<?php echo e(old('quantity', 1)); ?>" min="0" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label for="unit" class="block text-sm font-medium text-gray-700 mb-2">Satuan *</label>
                    <select name="unit" id="unit" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                        <option value="pcs" <?php echo e(old('unit') == 'pcs' ? 'selected' : ''); ?>>pcs</option>
                        <option value="unit" <?php echo e(old('unit') == 'unit' ? 'selected' : ''); ?>>unit</option>
                        <option value="set" <?php echo e(old('unit') == 'set' ? 'selected' : ''); ?>>set</option>
                        <option value="buah" <?php echo e(old('unit') == 'buah' ? 'selected' : ''); ?>>buah</option>
                        <option value="lembar" <?php echo e(old('unit') == 'lembar' ? 'selected' : ''); ?>>lembar</option>
                        <option value="roll" <?php echo e(old('unit') == 'roll' ? 'selected' : ''); ?>>roll</option>
                    </select>
                </div>
                <div>
                    <label for="condition" class="block text-sm font-medium text-gray-700 mb-2">Kondisi *</label>
                    <select name="condition" id="condition" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                        <option value="baik" <?php echo e(old('condition') == 'baik' ? 'selected' : ''); ?>>Baik</option>
                        <option value="perlu_perbaikan" <?php echo e(old('condition') == 'perlu_perbaikan' ? 'selected' : ''); ?>>Perlu Perbaikan</option>
                        <option value="rusak" <?php echo e(old('condition') == 'rusak' ? 'selected' : ''); ?>>Rusak</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">Foto Barang</label>
                <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/jpg"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                <p class="text-sm text-gray-500 mt-1">Format: JPG, PNG. Maks 5MB. Akan di-resize otomatis.</p>
            </div>

            <div class="mb-6">
                <label for="note" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                <textarea name="note" id="note" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500"><?php echo e(old('note')); ?></textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="<?php echo e(route('items.index')); ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold">
                    Batal
                </a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/items/create.blade.php ENDPATH**/ ?>