<?php $__env->startSection('title', 'Nuevo incidente'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1>Nuevo incidente</h1>
    <p>Registrar una nueva alerta de seguridad</p>
</div>

<div class="card" style="max-width: 640px;">
    <form action="<?php echo e(route('incidents.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo $__env->make('incidents._form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <div style="display: flex; gap: 0.75rem; margin-top: 0.5rem;">
            <button type="submit" class="btn btn-primary">Crear incidente</button>
            <a href="<?php echo e(route('incidents.index')); ?>" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/mathias/Escritorio/Archivos/Project SentinelOps/resources/views/incidents/create.blade.php ENDPATH**/ ?>