<?php $__env->startSection('title_page', 'Tambahan Biaya Bulanan Santri'); ?>
<?php $__env->startSection('content'); ?>

    <div class="container mt-1">
        <div class="table-responsive">
            <table id="example1" class="table table-striped table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th scope="col" class="text-center">No.</th>
                        <th scope="col">NIS</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Kategori</th>
                        <th scope="col">Tambahan</th>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit_item_santri')): ?>
                            <th scope="col" class="text-center">Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $santris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="text-center"><?php echo e($loop->iteration); ?></td>
                            <td><?php echo e($s->nis); ?></td>
                            <td><?php echo e($s->nama_santri); ?></td>
                            <td><?php echo e($s->kategoriSantri->nama_kategori); ?></td>
                            <td>
                                <?php if($s->tambahanBulanans->isNotEmpty()): ?>
                                    <ul class="list-group">
                                        <?php $__currentLoopData = $s->tambahanBulanans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li class="list-group-item">
                                                <strong><?php echo e($item->nama_item); ?></strong>
                                                <span class="text-muted">(Rp
                                                    <?php echo e(number_format($item->nominal, 0, ',', '.')); ?>)</span>
                                                <br>
                                                <small>Jumlah: <?php echo e($item->pivot->jumlah); ?></small>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Tidak ada tambahan</span>
                                <?php endif; ?>
                            </td>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit_item_santri')): ?>
                                <td class="text-center">
                                    <a href="<?php echo e(route('tambahan_bulanan.item_santri.edit', $s)); ?>"
                                        class="btn btn-sm btn-primary">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\SIAKAD_LQ\resources\views/tambahan-bulanan/item-santri.blade.php ENDPATH**/ ?>