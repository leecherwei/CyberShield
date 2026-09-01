<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Audit Log
 * 
 * Compliance/traceability record. Created by AuditLogObserver when 
 * a new Project is posted. This is a record of the action taken by the 
 * Security Manager to review the project.
 * 
 */

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_logs';

    public $timestamps = false; //uses explicit created_at only

    protected $fillable = [
        'organisation_id',
        'action',
        'entity_type',
        'entity_id',
        'details',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    /**
     * Polymorphic-style helper - resolves the related entity
     * if later want to eager-load it.
     * Not a true Eloquent polymorphic relationship, but a simple 
     * helper to resolve the related entity.
     */
    public function relatedEntity()
    {
        $modelClass = 'App\\Models\\' . $this->entity_type;

        if(!class_exists($modelClass)) {
            return null;
        }


        return $modelClass::find($this->entity_id);
    }











    /**
     * The project that this audit log is for
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * The user that performed the action
     */
    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}