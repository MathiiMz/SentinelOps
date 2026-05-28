<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the incidents created by this user.
     */
    public function incidents()
    {
        return $this->hasMany(Incident::class, 'created_by');
    }

    /**
     * Get the incidents assigned to this user.
     */
    public function assignedIncidents()
    {
        return $this->hasMany(Incident::class, 'assigned_to');
    }

    /**
     * Get the comments made by this user.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id');
    }

    /**
     * Get activity logs performed by this user.
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'actor_id');
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is SOC Analyst.
     */
    public function isAnalyst()
    {
        return $this->hasRole('analyst');
    }

    /**
     * Check if user is Viewer.
     */
    public function isViewer()
    {
        return $this->hasRole('viewer');
    }

    /**
     * Check if user is active.
     */
    public function isActive()
    {
        return $this->is_active === true;
    }
}
