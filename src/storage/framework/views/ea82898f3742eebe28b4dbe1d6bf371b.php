<?php $__env->startSection('title', 'Export Data - Inventory Masjid'); ?>

<?php $__env->startSection('content'); ?>
<h1 class="text-2xl font-bold text-gray-800 mb-6">Export Data Inventaris</h1>

<div class="bg-white rounded-lg shadow p-6">
    <form id="exportForm" class="space-y-6">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter Kategori</label>
                <select name="category_id" class="w-full border border-gray-300 rounded-lg px-4 py-3">
                    <option value="">Semua Kategori</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($category->id); ?>"><?php echo e($category->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter Lokasi</label>
                <select name="location_id" class="w-full border border-gray-300 rounded-lg px-4 py-3">
                    <option value="">Semua Lokasi</option>
                    <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($location->id); ?>"><?php echo e($location->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>

        <div class="border-t pt-6">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Pilih Format Export</h3>
            <div class="flex space-x-4">
                <a href="#" onclick="exportData('excel')" class="flex-1 bg-green-600 hover:bg-green-700 text-white py-4 rounded-lg font-semibold text-center">
                    📊 Export Excel (CSV)
                </a>
                <a href="#" onclick="exportData('pdf')" class="flex-1 bg-red-600 hover:bg-red-700 text-white py-4 rounded-lg font-semibold text-center">
                    📄 Export PDF
                </a>
            </div>
        </div>
    </form>
</div>

<script>
function exportData(format) {
    const form = document.getElementById('exportForm');
    const formData = new FormData(form);
    const params = new URLSearchParams();
    
    for (const [key, value] of formData.entries()) {
        if (value) params.append(key, value);
    }
    
    const url = format === 'excel' 
        ? '<?php echo e(route("export.excel")); ?>' 
        : '<?php echo e(route("export.pdf")); ?>';
    
    window.location.href = url + '?' + params.toString();
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/exports/index.blade.php ENDPATH**/ ?>