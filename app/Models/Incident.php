<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'severity',
        'status',
        'source_ip',
        'affected_host',
        'assigned_to',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who created the incident.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user this incident is assigned to.
     */
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the comments associated with this incident.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'incident_id');
    }

    /**
     * Get the activity log associated with this incident.
     */
    public function activities()
    {
        return $this->hasMany(ActivityLog::class, 'incident_id');
    }

    /**
     * Get valid severity levels.
     */
    public static function getSeverityLevels()
    {
        return ['critical', 'high', 'medium', 'low'];
    }

    /**
     * Get valid statuses.
     */
    public static function getStatuses()
    {
        return ['open', 'investigating', 'resolved', 'closed'];
    }

    /**
     * Scope to get only open incidents.
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope to get only investigating incidents.
     */
    public function scopeInvestigating($query)
    {
        return $query->where('status', 'investigating');
    }

    /**
     * Scope to get only critical incidents.
     */
    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    /**
     * Scope to get incidents assigned to a specific user.
     */
    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Check if incident is open.
     */
    public function isOpen()
    {
        return $this->status === 'open';
    }

    /**
     * Check if incident is critical.
     */
    public function isCritical()
    {
        return $this->severity === 'critical';
    }
}
