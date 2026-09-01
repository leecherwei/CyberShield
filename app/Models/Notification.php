<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 


/**
 * Notification Created bu NewProject Observer when a new Project
 * is posted. This is a notification to the Security Manager to review the project
 * and ensure it meets the platform's standards.
 */

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'organisation_id',
        'type',
        'project_id',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * The organisation that this notification is for
     */
    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function markAsRead(): void
    {
        $this->update(['is_read' => true]);
        //$this->is_read = true;
        //$this->save();
    }
}