<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1>Dashboard SOC</h1>
    <p>Resumen operativo de incidentes de seguridad</p>
</div>

<div class="grid-stats">
    <div class="card stat-card">
        <h3><?php echo e($stats['total']); ?></h3>
        <p>Total incidentes</p>
    </div>
    <div class="card stat-card">
        <h3><?php echo e($stats['open']); ?></h3>
        <p>Abiertos</p>
    </div>
    <div class="card stat-card">
        <h3><?php echo e($stats['investigating']); ?></h3>
        <p>En investigación</p>
    </div>
    <div class="card stat-card">
        <h3 style="color: #fca5a5;"><?php echo e($stats['critical']); ?></h3>
        <p>Críticos</p>
    </div>
    <?php if($usersCount !== null): ?>
    <div class="card stat-card">
        <h3><?php echo e($usersCount); ?></h3>
        <p>Usuarios registrados</p>
    </div>
    <?php endif; ?>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.75rem;">
    <?php $__currentLoopData = ['critical', 'high', 'medium', 'low']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="card stat-card">
            <h3><?php echo e($severityBreakdown[$level] ?? 0); ?></h3>
            <p>Incidentes <?php echo e(ucfirst($level)); ?></p>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<div style="display: grid; grid-template-columns: 1fr minmax(280px, 420px) 1fr; gap: 0.75rem; margin-bottom: 1rem; align-items: start;">
    <div class="card">
        <h2 style="font-size: 1rem; margin-bottom: 0.75rem;">Incidentes por estado</h2>
        <?php $maxStatus = max(1, (int) collect($statusBreakdown)->max()); ?>
        <?php $__currentLoopData = ['open', 'investigating', 'resolved', 'closed']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $value = (int) ($statusBreakdown[$status] ?? 0);
                $width = ($value / $maxStatus) * 100;
            ?>
            <div style="margin-bottom: 0.75rem;">
                <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 0.25rem;">
                    <span style="text-transform: uppercase; color: #8b9bb0;"><?php echo e($status); ?></span>
                    <strong><?php echo e($value); ?></strong>
                </div>
                <div style="height: 8px; background: #1a2330; border-radius: 999px;">
                    <div style="height: 8px; width: <?php echo e($width); ?>%; background: #3b82f6; border-radius: 999px;"></div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div class="card" style="padding: 0.9rem;">
        <div style="display: flex; justify-content: space-between; gap: 0.75rem; align-items: baseline; margin-bottom: 0.5rem;">
            <h2 style="font-size: 0.95rem;">Heatmap semanal</h2>
            <p style="color: #8b9bb0; font-size: 0.75rem;">por criticidad</p>
        </div>

        <div style="overflow: hidden;">
            <div style="display: grid; grid-template-columns: repeat(<?php echo e($weeklyHeatmap->count()); ?>, 10px); column-gap: 2px; align-items: start; justify-content: start;">
                <?php $__currentLoopData = $weeklyHeatmap; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="display: grid; row-gap: 2px;">
                        <?php $__currentLoopData = $week['days']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $bg = match($day['max_score']) {
                                    1 => '#22c55e', // low
                                    2 => '#facc15', // medium
                                    3 => '#f97316', // high
                                    4 => '#ef4444', // critical or many high/critical
                                    default => '#1a2330',
                                };
                            ?>
                            <div
                                title="<?php echo e($day['label']); ?> | Incidentes: <?php echo e($day['count']); ?>"
                                style="height: 10px; width: 10px; border-radius: 2px; background: <?php echo e($bg); ?>;"
                            ></div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; align-items: center; gap: 0.25rem; margin-top: 0.5rem; color: #8b9bb0; font-size: 0.7rem;">
            <span>Baja</span>
            <span style="height: 8px; width: 8px; border-radius: 2px; background: #22c55e; display: inline-block;"></span>
            <span style="height: 8px; width: 8px; border-radius: 2px; background: #facc15; display: inline-block;"></span>
            <span style="height: 8px; width: 8px; border-radius: 2px; background: #f97316; display: inline-block;"></span>
            <span style="height: 8px; width: 8px; border-radius: 2px; background: #ef4444; display: inline-block;"></span>
            <span>Alta</span>
        </div>
    </div>

    <div class="card">
        <h2 style="font-size: 1rem; margin-bottom: 0.75rem;">Actividad por usuario</h2>
        <?php $maxUser = max(1, (int) collect($activityByUser)->pluck('total')->max()); ?>
        <?php $__empty_1 = true; $__currentLoopData = $activityByUser; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php $width = ($row['total'] / $maxUser) * 100; ?>
            <div style="margin-bottom: 0.75rem;">
                <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 0.25rem;">
                    <span><?php echo e($row['name']); ?></span>
                    <strong><?php echo e($row['total']); ?></strong>
                </div>
                <div style="height: 8px; background: #1a2330; border-radius: 999px;">
                    <div style="height: 8px; width: <?php echo e($width); ?>%; background: #22c55e; border-radius: 999px;"></div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p style="color: #8b9bb0;">Sin datos de actividad.</p>
        <?php endif; ?>
    </div>
</div>

<div class="toolbar">
    <h2 style="font-size: 1rem; font-weight: 600;">Incidentes recientes</h2>
  <?php if(auth()->user()->isAdmin() || auth()->user()->isAnalyst()): ?>
    <a href="<?php echo e(route('incidents.create')); ?>" class="btn btn-primary">+ Nuevo incidente</a>
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
                <th>Asignado</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $recentIncidents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $incident): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td style="font-family: 'JetBrains Mono', monospace; color: #8b9bb0;">#<?php echo e($incident->id); ?></td>
                <td><a href="<?php echo e(route('incidents.show', $incident)); ?>"><?php echo e(Str::limit($incident->title, 50)); ?></a></td>
                <td><span class="badge badge-<?php echo e($incident->severity); ?>"><?php echo e($incident->severity); ?></span></td>
                <td><span class="badge badge-<?php echo e($incident->status); ?>"><?php echo e($incident->status); ?></span></td>
                <td><?php echo e($incident->assignee?->name ?? '—'); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="5" style="text-align: center; color: #8b9bb0; padding: 2rem;">No hay incidentes</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="card" style="margin-top: 1rem;">
    <h2 style="font-size: 1rem; margin-bottom: 0.75rem;">Actividad reciente</h2>
    <?php $__empty_1 = true; $__currentLoopData = $recentActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div style="padding: 0.65rem 0; border-bottom: 1px solid #2a3544;">
            <div style="display: flex; justify-content: space-between; gap: 0.5rem;">
                <p style="font-size: 0.9rem;"><?php echo e($activity->message); ?></p>
                <span style="color: #8b9bb0; font-size: 0.75rem; white-space: nowrap;">
                    <?php echo e($activity->created_at->format('d/m H:i')); ?>

                </span>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p style="color: #8b9bb0;">Sin actividad registrada.</p>
    <?php endif; ?>
</div>

<div style="margin-top: 1rem;">
    <a href="<?php echo e(route('incidents.index')); ?>" class="btn btn-ghost">Ver todos los incidentes →</a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/mathias/Escritorio/Archivos/Project SentinelOps/resources/views/dashboard.blade.php ENDPATH**/ ?>