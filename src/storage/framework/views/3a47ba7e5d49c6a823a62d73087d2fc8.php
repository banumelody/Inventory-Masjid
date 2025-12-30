<?php $__env->startSection('title', 'Kirim Feedback - Inventory Masjid'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">💬 Kirim Feedback</h1>

    <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded mb-6">
        <p class="text-sm">
            Bantu kami meningkatkan aplikasi ini! Sampaikan masalah, saran, atau pertanyaan Anda.
        </p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="<?php echo e(route('feedbacks.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            
            <div class="mb-4">
                <label for="module" class="block text-sm font-medium text-gray-700 mb-2">Modul / Halaman *</label>
                <select name="module" id="module" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                    <option value="">Pilih Modul</option>
                    <option value="inventaris" <?php echo e(old('module') == 'inventaris' ? 'selected' : ''); ?>>Inventaris</option>
                    <option value="peminjaman" <?php echo e(old('module') == 'peminjaman' ? 'selected' : ''); ?>>Peminjaman</option>
                    <option value="mutasi" <?php echo e(old('module') == 'mutasi' ? 'selected' : ''); ?>>Mutasi Stok</option>
                    <option value="kategori" <?php echo e(old('module') == 'kategori' ? 'selected' : ''); ?>>Kategori</option>
                    <option value="lokasi" <?php echo e(old('module') == 'lokasi' ? 'selected' : ''); ?>>Lokasi</option>
                    <option value="laporan" <?php echo e(old('module') == 'laporan' ? 'selected' : ''); ?>>Laporan / Export</option>
                    <option value="pengguna" <?php echo e(old('module') == 'pengguna' ? 'selected' : ''); ?>>Pengguna</option>
                    <option value="backup" <?php echo e(old('module') == 'backup' ? 'selected' : ''); ?>>Backup</option>
                    <option value="umum" <?php echo e(old('module') == 'umum' ? 'selected' : ''); ?>>Umum / Lainnya</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Jenis Feedback *</label>
                <select name="type" id="type" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                    <option value="bug" <?php echo e(old('type') == 'bug' ? 'selected' : ''); ?>>🐛 Bug / Masalah</option>
                    <option value="suggestion" <?php echo e(old('type') == 'suggestion' ? 'selected' : ''); ?>>💡 Saran</option>
                    <option value="question" <?php echo e(old('type') == 'question' ? 'selected' : ''); ?>>❓ Pertanyaan</option>
                    <option value="other" <?php echo e(old('type') == 'other' ? 'selected' : ''); ?>>📝 Lainnya</option>
                </select>
            </div>

            <div class="mb-6">
                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Pesan *</label>
                <textarea name="message" id="message" rows="5" required
                    placeholder="Jelaskan feedback Anda di sini..."
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500"><?php echo e(old('message')); ?></textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="<?php echo e(route('dashboard')); ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold">Batal</a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold">Kirim Feedback</button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/feedbacks/create.blade.php ENDPATH**/ ?>