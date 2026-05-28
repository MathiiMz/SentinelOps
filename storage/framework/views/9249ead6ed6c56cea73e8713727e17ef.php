<?php $__env->startSection('title', 'Editar incidente'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1>Editar incidente #<?php echo e($incident->id); ?></h1>
    <p><?php echo e($incident->title); ?></p>
</div>

<div class="card" style="max-width: 640px;">
    <form action="<?php echo e(route('incidents.update', $incident)); ?>" method="POST">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <?php echo $__env->make('incidents._form', ['incident' => $incident, 'edit' => true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <div style="display: flex; gap: 0.75rem; margin-top: 0.5rem;">
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
            <a href="<?php echo e(route('incidents.show', $incident)); ?>" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/mathias/Escritorio/Archivos/Project SentinelOps/resources/views/incidents/edit.blade.php ENDPATH**/ ?>