<?php $__env->startSection('title', 'Usuarios'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1>Gestión de usuarios</h1>
    <p>Administración de cuentas y roles del SOC</p>
</div>

<div class="toolbar">
    <form class="filters" method="GET" action="<?php echo e(route('admin.users.index')); ?>">
        <input type="search" name="q" placeholder="Buscar nombre o email..." value="<?php echo e(request('q')); ?>">
        <select name="role">
            <option value="">Todos los roles</option>
            <?php $__currentLoopData = ['admin','analyst','viewer']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($r); ?>" <?php if(request('role') === $r): echo 'selected'; endif; ?>><?php echo e($r); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <select name="is_active">
            <option value="">Activo / inactivo</option>
            <option value="1" <?php if(request('is_active') === '1'): echo 'selected'; endif; ?>>Activos</option>
            <option value="0" <?php if(request('is_active') === '0'): echo 'selected'; endif; ?>>Inactivos</option>
        </select>
        <button type="submit" class="btn btn-ghost">Filtrar</button>
        <?php if(request()->hasAny(['q','role','is_active'])): ?>
            <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-ghost">Limpiar</a>
        <?php endif; ?>
    </form>
    <a href="<?php echo e(route('admin.users.create')); ?>" class="btn btn-primary">+ Nuevo usuario</a>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Registro</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td style="font-family: 'JetBrains Mono', monospace; color: #8b9bb0;">#<?php echo e($user->id); ?></td>
                <td><?php echo e($user->name); ?></td>
                <td style="font-size: 0.85rem;"><?php echo e($user->email); ?></td>
                <td><span class="role-badge"><?php echo e($user->role); ?></span></td>
                <td>
                    <?php if($user->is_active): ?>
                        <span class="badge badge-resolved">activo</span>
                    <?php else: ?>
                        <span class="badge badge-closed">inactivo</span>
                    <?php endif; ?>
                </td>
                <td style="color: #8b9bb0; font-size: 0.8rem;"><?php echo e($user->created_at->format('d/m/Y')); ?></td>
                <td style="white-space: nowrap;">
                    <a href="<?php echo e(route('admin.users.edit', $user)); ?>" class="btn btn-ghost" style="padding: 0.35rem 0.65rem;">Editar</a>
                    <?php if($user->id !== auth()->id()): ?>
                    <form action="<?php echo e(route('admin.users.destroy', $user)); ?>" method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar a <?php echo e($user->name); ?>?')">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-danger" style="padding: 0.35rem 0.65rem;">Eliminar</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="7" style="text-align: center; color: #8b9bb0; padding: 2rem;">No hay usuarios</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if($users->hasPages()): ?>
<div class="pagination">
    <?php if($users->onFirstPage()): ?>
        <span>← Anterior</span>
    <?php else: ?>
        <a href="<?php echo e($users->previousPageUrl()); ?>">← Anterior</a>
    <?php endif; ?>
    <span class="active"><?php echo e($users->currentPage()); ?> / <?php echo e($users->lastPage()); ?></span>
    <?php if($users->hasMorePages()): ?>
        <a href="<?php echo e($users->nextPageUrl()); ?>">Siguiente →</a>
    <?php else: ?>
        <span>Siguiente →</span>
    <?php endif; ?>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/mathias/Escritorio/Archivos/Project SentinelOps/resources/views/users/index.blade.php ENDPATH**/ ?>