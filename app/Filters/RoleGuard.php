<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleGuard implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // Pertama, pastikan pengguna sudah login
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(site_url('login'))->with('error', 'Anda harus login terlebih dahulu.');
        }

        // Dapatkan role yang diizinkan dari argumen filter
        // $arguments akan berisi ['kasir'] jika filter dipanggil sebagai 'roleGuard:kasir'
        $allowedRoles = (array) $arguments;
        $userRole = $session->get('role');

        if (empty($userRole) || !in_array($userRole, $allowedRoles)) {
            // Jika role tidak sesuai, redirect atau tampilkan error
            // Anda bisa redirect ke halaman sebelumnya dengan pesan error, atau ke dashboard default pengguna jika ada
                if ($request->isAJAX()) {
                    $response = service('response');
                    return $response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Akses ditolak atau sesi berakhir.', 'csrf_hash' => csrf_hash()]);
                } else {
                    session()->setFlashdata('error', 'Anda tidak memiliki hak akses ke halaman ini.');
                    // Redirect ke halaman login atau halaman utama yang sesuai dengan role pengguna jika ada
                    return redirect()->to(site_url($userRole === 'admin' ? 'admin/dashboard' : ($userRole === 'kasir' ? 'kasir/dashboard' : 'login')));
                }
        }
        // Jika semua kondisi terpenuhi, lanjutkan ke controller
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada aksi setelah controller
    }
}   