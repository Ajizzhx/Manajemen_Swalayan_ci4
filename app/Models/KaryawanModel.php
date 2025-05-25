<?php

namespace App\Models;

use CodeIgniter\Model;

class KaryawanModel extends Model
{
    protected $table            = 'karyawan';
    protected $primaryKey       = 'karyawan_id';
    protected $useAutoIncrement = false; 
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false; 
    protected $allowedFields    = ['karyawan_id', 'nama', 'email', 'password', 'role', 'is_deleted'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'is_deleted'; 

   
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        log_message('debug', '[KaryawanModel::hashPassword] Callback triggered. Incoming data: ' . json_encode($data));

        if (!isset($data['data']['password'])) {
            log_message('debug', '[KaryawanModel::hashPassword] "password" key is not set in data[data]. No password to hash or update.');
            return $data;
        }


        $plainPassword = $data['data']['password'];

        if ($plainPassword === '') {
           
            log_message('debug', '[KaryawanModel::hashPassword] "password" key is set but the value is an empty string. Unsetting from data to avoid saving an empty hash or an empty string directly.');
           
            unset($data['data']['password']);
            return $data;
        }

      
        $hashedPassword = hash('sha256', $plainPassword);
        log_message('debug', '[KaryawanModel::hashPassword] Hashing password. Plain: "' . $plainPassword . '", Hashed: "' . $hashedPassword . '"');
        $data['data']['password'] = $hashedPassword;
        return $data;
    }
 }