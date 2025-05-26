<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table            = 'audit_logs'; 
    protected $primaryKey       = 'id'; 
    protected $useAutoIncrement = true;
    protected $returnType       = 'object'; 
    protected $useSoftDeletes   = false;

   
    protected $allowedFields    = [
        'user_id',
        'action',
        'description',
        'ip_address',
        'user_agent',
        'created_at' 
    ];

    // Dates
    protected $useTimestamps = true; 
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at'; 
    protected $updatedField  = ''; 

    
}