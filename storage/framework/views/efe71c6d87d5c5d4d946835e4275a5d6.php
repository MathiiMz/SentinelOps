<?php $__env->startSection('title', 'Incidentes'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1>Incidentes</h1>
    <p>Gestión y seguimiento de alertas de seguridad</p>
</div>

<div class="toolbar">
    <form class="filters" method="GET" action="<?php echo e(route('incidents.index')); ?>">
        <input type="search" name="q" placeholder="Buscar..." value="<?php echo e(request('q')); ?>">
        <input type="search" name="host" placeholder="Hostname..." value="<?php echo e(request('host')); ?>">
        <select name="status">
            <option value="">Todos los estados</option>
            <?php $__currentLoopData = ['open','investigating','resolved','closed']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($s); ?>" <?php if(request('status') === $s): echo 'selected'; endif; ?>><?php echo e($s); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <select name="severity">
            <option value="">Todas las severidades</option>
            <?php $__currentLoopData = ['critical','high','medium','low']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($s); ?>" <?php if(request('severity') === $s): echo 'selected'; endif; ?>><?php echo e($s); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <select name="assigned_to">
            <option value="">Asignado a (todos)</option>
            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($user->id); ?>" <?php if((string) request('assigned_to') === (string) $user->id): echo 'selected'; endif; ?>>
                    <?php echo e($user->name); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <select name="created_by">
            <option value="">Creado por (todos)</option>
            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($user->id); ?>" <?php if((string) request('created_by') === (string) $user->id): echo 'selected'; endif; ?>>
                    <?php echo e($user->name); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <input type="date" name="created_from" value="<?php echo e(request('created_from')); ?>">
        <input type="date" name="created_to" value="<?php echo e(request('created_to')); ?>">
        <button type="submit" class="btn btn-ghost">Filtrar</button>
        <?php if(request()->hasAny(['q','host','status','severity','assigned_to','created_by','created_from','created_to'])): ?>
            <a href="<?php echo e(route('incidents.index')); ?>" class="btn btn-ghost">Limpiar</a>
        <?php endif; ?>
    </form>
    <?php if(auth()->user()->isAdmin() || auth()->user()->isAnalyst()): ?>
        <a href="<?php echo e(route('incidents.create')); ?>" class="btn btn-primary">+ Nuevo</a>
    <?php endif; ?>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Severidad</th>
                <th>Estado</th>
                <th>Host</th>
                <th>Asignado</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $incidents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $incident): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td style="font-family: 'JetBrains Mono', monospace; color: #8b9bb0;">#<?php echo e($incident->id); ?></td>
                <td><a href="<?php echo e(route('incidents.show', $incident)); ?>"><?php echo e(Str::limit($incident->title, 45)); ?></a></td>
                <td><span class="badge badge-<?php echo e($incident->severity); ?>"><?php echo e($incident->severity); ?></span></td>
                <td><span class="badge badge-<?php echo e($incident->status); ?>"><?php echo e($incident->status); ?></span></td>
                <td style="font-size: 0.8rem;"><?php echo e($incident->affected_host); ?></td>
                <td><?php echo e($incident->assignee?->name ?? '—'); ?></td>
                <td style="color: #8b9bb0; font-size: 0.8rem;"><?php echo e($incident->created_at->format('d/m/Y H:i')); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="7" style="text-align: center; color: #8b9bb0; padding: 2rem;">No se encontraron incidentes</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if($incidents->hasPages()): ?>
<div class="pagination">
    <?php if($incidents->onFirstPage()): ?>
        <span>← Anterior</span>
    <?php else: ?>
        <a href="<?php echo e($incidents->previousPageUrl()); ?>">← Anterior</a>
    <?php endif; ?>
    <span class="active"><?php echo e($incidents->currentPage()); ?> / <?php echo e($incidents->lastPage()); ?></span>
    <?php if($incidents->hasMorePages()): ?>
        <a href="<?php echo e($incidents->nextPageUrl()); ?>">Siguiente →</a>
    <?php else: ?>
        <span>Siguiente →</span>
    <?php endif; ?>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/mathias/Escritorio/Archivos/Project SentinelOps/resources/views/incidents/index.blade.php ENDPATH**/ ?>