<?php $__env->startSection('title', 'Pengaturan Aplikasi - ' . \App\Models\Setting::appName()); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">⚙️ Pengaturan Aplikasi</h1>
    <p class="text-gray-500 mt-1">Kustomisasi aplikasi sesuai kebutuhan organisasi Anda</p>
</div>

<form action="<?php echo e(route('settings.update')); ?>" method="POST" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    <div class="space-y-6">
        <?php $__currentLoopData = $settings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupKey => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h2 class="text-lg font-semibold text-gray-800"><?php echo e($group['info']['title']); ?></h2>
                <p class="text-sm text-gray-500"><?php echo e($group['info']['description']); ?></p>
            </div>
            
            <div class="p-6 space-y-6">
                <?php $__currentLoopData = $group['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="<?php echo e($setting->key); ?>" class="block text-sm font-medium text-gray-700">
                            <?php echo e($setting->label); ?>

                        </label>
                        <?php if($setting->description): ?>
                        <p class="text-xs text-gray-400 mt-1"><?php echo e($setting->description); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="md:col-span-2">
                        <?php if($setting->type === 'text'): ?>
                            <input type="text" 
                                   name="<?php echo e($setting->key); ?>" 
                                   id="<?php echo e($setting->key); ?>"
                                   value="<?php echo e(old($setting->key, $setting->value)); ?>"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        
                        <?php elseif($setting->type === 'textarea'): ?>
                            <textarea name="<?php echo e($setting->key); ?>" 
                                      id="<?php echo e($setting->key); ?>"
                                      rows="3"
                                      class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500"><?php echo e(old($setting->key, $setting->value)); ?></textarea>
                        
                        <?php elseif($setting->type === 'boolean'): ?>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" 
                                       name="<?php echo e($setting->key); ?>" 
                                       id="<?php echo e($setting->key); ?>"
                                       value="1"
                                       <?php echo e($setting->value ? 'checked' : ''); ?>

                                       class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500">
                                <span class="text-sm text-gray-700">Aktif</span>
                            </label>
                        
                        <?php elseif($setting->type === 'image'): ?>
                            <div class="space-y-3">
                                <?php if($setting->value): ?>
                                <div class="flex items-center gap-4">
                                    <img src="<?php echo e(asset('storage/' . $setting->value)); ?>" 
                                         alt="<?php echo e($setting->label); ?>"
                                         class="h-16 w-16 object-contain bg-gray-100 rounded-lg border">
                                    <label class="flex items-center gap-2 text-sm text-red-600 cursor-pointer">
                                        <input type="checkbox" name="remove_<?php echo e($setting->key); ?>" value="1" class="rounded">
                                        Hapus gambar
                                    </label>
                                </div>
                                <?php endif; ?>
                                <input type="file" 
                                       name="<?php echo e($setting->key); ?>" 
                                       id="<?php echo e($setting->key); ?>"
                                       accept="image/*"
                                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <!-- Submit Button -->
    <div class="mt-6 flex justify-end">
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg font-semibold">
            💾 Simpan Pengaturan
        </button>
    </div>
</form>

<!-- Preview Section -->
<div class="mt-8 bg-blue-50 rounded-lg p-6">
    <h3 class="font-semibold text-blue-800 mb-3">💡 Tips:</h3>
    <ul class="text-sm text-blue-700 space-y-1">
        <li>• <strong>Nama Aplikasi</strong> akan muncul di header, judul tab, dan laporan</li>
        <li>• <strong>Logo</strong> akan muncul di sidebar dan halaman login</li>
        <li>• <strong>Informasi Organisasi</strong> akan muncul di halaman About dan laporan cetak</li>
        <li>• Perubahan langsung terlihat setelah menyimpan</li>
    </ul>
</div>

<!-- Version Info -->
<div class="mt-6 bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 bg-gray-50 border-b">
        <h2 class="text-lg font-semibold text-gray-800">ℹ️ Informasi Aplikasi</h2>
        <p class="text-sm text-gray-500">Detail versi dan informasi sistem</p>
    </div>
    
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Current Version -->
            <div class="bg-green-50 rounded-lg p-4">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <span class="text-2xl">🚀</span>
                    </div>
                    <div>
                        <p class="text-sm text-green-600 font-medium">Versi Aplikasi</p>
                        <p class="text-2xl font-bold text-green-700">v<?php echo e(config('version.version')); ?></p>
                    </div>
                </div>
                <div class="text-sm text-green-700 space-y-1">
                    <p><strong>Codename:</strong> <?php echo e(config('version.name')); ?></p>
                    <p><strong>Tanggal Rilis:</strong> <?php echo e(\Carbon\Carbon::parse(config('version.release_date'))->format('d F Y')); ?></p>
                </div>
            </div>
            
            <!-- System Info -->
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                        <span class="text-2xl">⚙️</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 font-medium">Informasi Sistem</p>
                        <p class="text-lg font-bold text-gray-700">Laravel <?php echo e(app()->version()); ?></p>
                    </div>
                </div>
                <div class="text-sm text-gray-600 space-y-1">
                    <p><strong>PHP:</strong> <?php echo e(phpversion()); ?></p>
                    <p><strong>Environment:</strong> <?php echo e(ucfirst(app()->environment())); ?></p>
                </div>
            </div>
        </div>
        
        <!-- Version History -->
        <div class="mt-6">
            <h3 class="font-semibold text-gray-700 mb-3">📋 Riwayat Versi</h3>
            <div class="space-y-3">
                <?php $__currentLoopData = config('version.history'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ver => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="border rounded-lg p-4 <?php echo e($loop->first ? 'border-green-300 bg-green-50' : 'border-gray-200'); ?>">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="font-bold <?php echo e($loop->first ? 'text-green-700' : 'text-gray-700'); ?>">v<?php echo e($ver); ?></span>
                            <span class="text-sm px-2 py-0.5 rounded <?php echo e($loop->first ? 'bg-green-200 text-green-800' : 'bg-gray-200 text-gray-600'); ?>">
                                <?php echo e($info['name']); ?>

                            </span>
                            <?php if($loop->first): ?>
                            <span class="text-xs px-2 py-0.5 bg-green-600 text-white rounded">CURRENT</span>
                            <?php endif; ?>
                        </div>
                        <span class="text-sm text-gray-500"><?php echo e(\Carbon\Carbon::parse($info['date'])->format('d M Y')); ?></span>
                    </div>
                    <ul class="text-sm <?php echo e($loop->first ? 'text-green-700' : 'text-gray-600'); ?> space-y-1">
                        <?php $__currentLoopData = $info['highlights']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $highlight): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>• <?php echo e($highlight); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/settings/index.blade.php ENDPATH**/ ?>