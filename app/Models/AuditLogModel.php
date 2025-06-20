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
    
    // Override insert method untuk menangani kasus di mana user_id tidak ada
    public function insert($data = null, bool $returnID = true)
    {
        // Nonaktifkan foreign key checks sementara
        $this->db->query('SET FOREIGN_KEY_CHECKS=0');
        
        // Jika user_id tidak ada atau kosong, set nilai NULL secara eksplisit
        if (is_array($data) && (empty($data['user_id']) || !isset($data['user_id']))) {
            $data['user_id'] = null;
        }
        
        $result = parent::insert($data, $returnID);
        
        // Aktifkan kembali foreign key checks
        $this->db->query('SET FOREIGN_KEY_CHECKS=1');
        
        return $result;
    }

   
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