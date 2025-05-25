<?php

if (!function_exists('generate_sequential_id')) {
    
    function generate_sequential_id(string $prefix, \CodeIgniter\Model $model, string $columnName, int $length = 5): string
    {
        $safePrefix = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $prefix));

       
        $builder = $model->builder();
        $builder->selectMax($columnName)
                ->like($columnName, $safePrefix, 'after')
                ->orderBy($columnName, 'DESC'); 

        
        $lastIdObj = $model->withDeleted(true)->selectMax($columnName)->like($columnName, $safePrefix, 'after')->orderBy($columnName, 'DESC')->get(1)->getFirstRow();

        log_message('debug', '[custom_helper::generate_sequential_id] Query result for max ID: ' . json_encode($lastIdObj));
        

        $nextNumber = 1;
       
        if (is_object($lastIdObj) && !empty($lastIdObj->{$columnName})) {
            
            $numericPart = (int) substr($lastIdObj->{$columnName}, strlen($safePrefix));
            $nextNumber = $numericPart + 1;
        }
        

        return $safePrefix . str_pad($nextNumber, $length, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('generate_daily_sequential_id')) {
    
    function generate_daily_sequential_id(string $prefix, \CodeIgniter\Model $model, string $columnName, int $length = 5): string
    {
        $safePrefix = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $prefix));
        $datePart = date('Ymd');
        $fullPrefix = $safePrefix . '-' . $datePart . '-';

        
        $builder = $model->builder();
        $builder->selectMax($columnName)
                ->like($columnName, $fullPrefix, 'after') 
                ->orderBy($columnName, 'DESC'); 


        
        $lastIdObjDaily = $model->withDeleted(true)->selectMax($columnName)->like($columnName, $fullPrefix, 'after')->orderBy($columnName, 'DESC')->get(1)->getFirstRow();

        log_message('debug', '[custom_helper::generate_daily_sequential_id] Query result for max daily ID: ' . json_encode($lastIdObjDaily));
        

        $nextNumber = 1;
       
        if (is_object($lastIdObjDaily) && !empty($lastIdObjDaily->{$columnName})) {
        
            $numericPartString = substr($lastIdObjDaily->{$columnName}, strlen($fullPrefix));
            if (is_numeric($numericPartString)) {
                $nextNumber = (int)$numericPartString + 1;
            }
        }
      

        return $fullPrefix . str_pad($nextNumber, $length, '0', STR_PAD_LEFT);
    }
}

