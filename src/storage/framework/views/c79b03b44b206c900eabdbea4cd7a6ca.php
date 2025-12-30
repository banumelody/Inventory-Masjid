<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Inventory Masjid'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-green-700 text-white shadow-lg print:hidden">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <a href="<?php echo e(route('items.index')); ?>" class="text-xl font-bold">🕌 Inventory Masjid</a>
                <?php if(auth()->guard()->check()): ?>
                <div class="flex items-center space-x-6">
                    <a href="<?php echo e(route('items.index')); ?>" class="hover:text-green-200 <?php echo e(request()->routeIs('items.*') ? 'text-green-200 font-semibold' : ''); ?>">Inventaris</a>
                    <a href="<?php echo e(route('loans.index')); ?>" class="hover:text-green-200 <?php echo e(request()->routeIs('loans.*') ? 'text-green-200 font-semibold' : ''); ?>">Peminjaman</a>
                    <a href="<?php echo e(route('stock-movements.index')); ?>" class="hover:text-green-200 <?php echo e(request()->routeIs('stock-movements.*') ? 'text-green-200 font-semibold' : ''); ?>">Mutasi</a>
                    <a href="<?php echo e(route('categories.index')); ?>" class="hover:text-green-200 <?php echo e(request()->routeIs('categories.*') ? 'text-green-200 font-semibold' : ''); ?>">Kategori</a>
                    <a href="<?php echo e(route('locations.index')); ?>" class="hover:text-green-200 <?php echo e(request()->routeIs('locations.*') ? 'text-green-200 font-semibold' : ''); ?>">Lokasi</a>
                    <a href="<?php echo e(route('reports.index')); ?>" class="hover:text-green-200 <?php echo e(request()->routeIs('reports.*') || request()->routeIs('export.*') ? 'text-green-200 font-semibold' : ''); ?>">Laporan</a>
                    <?php if(auth()->user()->isAdmin()): ?>
                    <a href="<?php echo e(route('users.index')); ?>" class="hover:text-green-200 <?php echo e(request()->routeIs('users.*') ? 'text-green-200 font-semibold' : ''); ?>">Pengguna</a>
                    <a href="<?php echo e(route('backups.index')); ?>" class="hover:text-green-200 <?php echo e(request()->routeIs('backups.*') ? 'text-green-200 font-semibold' : ''); ?>">Backup</a>
                    <?php endif; ?>
                    <div class="border-l border-green-500 pl-4 flex items-center space-x-3">
                        <span class="text-sm"><?php echo e(auth()->user()->name); ?></span>
                        <form action="<?php echo e(route('logout')); ?>" method="POST" class="inline">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="text-sm hover:text-green-200">Logout</button>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-8">
        <?php if(session('success')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </main>
</body>
</html>
<?php /**PATH /var/www/resources/views/layouts/app.blade.php ENDPATH**/ ?>