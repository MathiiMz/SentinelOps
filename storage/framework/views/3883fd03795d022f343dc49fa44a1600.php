<?php $__env->startSection('title', 'Incidente #'.$incident->id); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header" style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-start; gap: 1rem;">
    <div>
        <p style="color: #8b9bb0; font-size: 0.8rem; font-family: 'JetBrains Mono', monospace;">#<?php echo e($incident->id); ?></p>
        <h1><?php echo e($incident->title); ?></h1>
        <p style="margin-top: 0.5rem;">
            <span class="badge badge-<?php echo e($incident->severity); ?>"><?php echo e($incident->severity); ?></span>
            <span class="badge badge-<?php echo e($incident->status); ?>"><?php echo e($incident->status); ?></span>
        </p>
    </div>
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <a href="<?php echo e(route('incidents.export.pdf', $incident)); ?>" class="btn btn-primary">Exportar PDF</a>
        <?php if(auth()->user()->isAdmin() || auth()->user()->id === $incident->created_by): ?>
            <a href="<?php echo e(route('incidents.edit', $incident)); ?>" class="btn btn-ghost">Editar</a>
        <?php endif; ?>
        <?php if(auth()->user()->isAdmin()): ?>
            <form action="<?php echo e(route('incidents.destroy', $incident)); ?>" method="POST" onsubmit="return confirm('¿Eliminar este incidente?')">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button type="submit" class="btn btn-danger">Eliminar</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 320px; gap: 1.25rem; align-items: start;">
    <div>
        <div class="card" style="margin-bottom: 1.25rem;">
            <h2 style="font-size: 0.85rem; color: #8b9bb0; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem;">Descripción</h2>
            <p style="white-space: pre-wrap;"><?php echo e($incident->description); ?></p>
        </div>

        <div class="card">
            <h2 style="font-size: 0.85rem; color: #8b9bb0; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem;">
                Timeline de actividad (<?php echo e($timeline->count()); ?>)
            </h2>
            <?php $__empty_1 = true; $__currentLoopData = $timeline; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div style="padding: 0.85rem 0; border-bottom: 1px solid #2a3544;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <strong style="font-size: 0.85rem;"><?php echo e($item['actor']); ?></strong>
                            <span class="badge <?php echo e($item['type'] === 'comment' ? 'badge-medium' : 'badge-open'); ?>">
                                <?php echo e($item['type'] === 'comment' ? 'Comentario' : 'Evento'); ?>

                            </span>
                        </div>
                        <span style="color: #8b9bb0; font-size: 0.75rem;"><?php echo e($item['created_at']->format('d/m/Y H:i')); ?></span>
                    </div>
                    <p style="font-size: 0.9rem; white-space: pre-wrap;"><?php echo e($item['label']); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p style="color: #8b9bb0; font-size: 0.9rem;">Sin actividad aún.</p>
            <?php endif; ?>

            <?php if(!auth()->user()->isViewer()): ?>
            <form action="<?php echo e(route('incidents.comments.store', $incident)); ?>" method="POST" style="margin-top: 1.25rem;">
                <?php echo csrf_field(); ?>
                <div class="form-group">
                    <label for="content">Agregar comentario</label>
                    <textarea id="content" name="content" rows="3" required><?php echo e(old('content')); ?></textarea>
                    <?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="error-text"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <button type="submit" class="btn btn-primary">Publicar</button>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <div>
        <div class="card" style="margin-bottom: 1rem;">
            <h2 style="font-size: 0.85rem; color: #8b9bb0; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem;">Detalles</h2>
            <dl style="font-size: 0.875rem;">
                <dt style="color: #8b9bb0; margin-bottom: 0.2rem;">IP origen</dt>
                <dd style="font-family: 'JetBrains Mono', monospace; margin-bottom: 0.75rem;"><?php echo e($incident->source_ip); ?></dd>
                <dt style="color: #8b9bb0; margin-bottom: 0.2rem;">Host afectado</dt>
                <dd style="margin-bottom: 0.75rem;"><?php echo e($incident->affected_host); ?></dd>
                <dt style="color: #8b9bb0; margin-bottom: 0.2rem;">Creado por</dt>
                <dd style="margin-bottom: 0.75rem;"><?php echo e($incident->creator->name); ?></dd>
                <dt style="color: #8b9bb0; margin-bottom: 0.2rem;">Asignado a</dt>
                <dd style="margin-bottom: 0.75rem;"><?php echo e($incident->assignee?->name ?? 'Sin asignar'); ?></dd>
                <dt style="color: #8b9bb0; margin-bottom: 0.2rem;">Creado</dt>
                <dd><?php echo e($incident->created_at->format('d/m/Y H:i')); ?></dd>
            </dl>
        </div>

        <?php if(!auth()->user()->isViewer()): ?>
        <div class="card">
            <h2 style="font-size: 0.85rem; color: #8b9bb0; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem;">Cambiar estado</h2>
            <form action="<?php echo e(route('incidents.status', $incident)); ?>" method="POST">
                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                <select name="status" onchange="this.form.submit()">
                    <?php $__currentLoopData = ['open','investigating','resolved','closed']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($s); ?>" <?php if($incident->status === $s): echo 'selected'; endif; ?>><?php echo e($s); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

<a href="<?php echo e(route('incidents.index')); ?>" class="btn btn-ghost" style="margin-top: 1.25rem;">← Volver al listado</a>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/mathias/Escritorio/Archivos/Project SentinelOps/resources/views/incidents/show.blade.php ENDPATH**/ ?>