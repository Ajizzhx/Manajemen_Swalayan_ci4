<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class OwnerOnlyFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = Services::session();

        if (!$session->get('isLoggedIn')) {
            $session->set('redirect_url', current_url());
            return redirect()->to(base_url('login'))->with('error', 'Anda harus login untuk mengakses halaman ini.');
        }

        // Izinkan akses HANYA jika role adalah 'pemilik'
        if ($session->get('role') !== 'pemilik') {
            // Jika bukan pemilik, redirect atau tampilkan error
            // Anda bisa mengarahkan ke dashboard admin (jika admin mencoba) atau halaman utama
            return redirect()->to(base_url('admin/dashboard'))->with('error', 'Hanya Pemilik yang dapat mengakses halaman ini.');
        }

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}