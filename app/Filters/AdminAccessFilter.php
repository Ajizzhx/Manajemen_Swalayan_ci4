<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class AdminAccessFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = Services::session();

        if (!$session->get('isLoggedIn')) {
            $session->set('redirect_url', current_url());
            return redirect()->to(base_url('login'))->with('error', 'Anda harus login untuk mengakses halaman ini.');
        }

        $role = $session->get('role');
        // Izinkan akses jika role adalah 'admin' ATAU 'pemilik'
        if ($role !== 'admin' && $role !== 'pemilik') {
            
            return redirect()->to(base_url('/'))->with('error', 'Anda tidak memiliki hak akses ke area ini.');
        }

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}