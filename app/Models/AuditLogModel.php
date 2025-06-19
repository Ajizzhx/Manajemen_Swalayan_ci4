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
    
    /**
     * Log activity safely with error handling for foreign key constraint
     */
    public function logActivity($userId, $action, $description = '')
    {
        try {
            // Check first if user exists
            $karyawanModel = new \App\Models\KaryawanModel();
            $userExists = $karyawanModel->find($userId);
            
            if (!$userExists) {
                // Skip logging if the user doesn't exist to avoid foreign key error
                return false;
            }
            
            return $this->insert([
                'user_id' => $userId,
                'action' => $action,
                'description' => $description,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            ]);
        } catch (\Exception $e) {
            // Log the error but don't let it break the application
            log_message('error', 'Failed to log activity: ' . $e->getMessage());
            return false;
        }
    }
    }