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

        
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(site_url('login'))->with('error', 'Anda harus login terlebih dahulu.');
        }

        
        
        $allowedRoles = (array) $arguments;
        $userRole = $session->get('role');

        if (empty($userRole) || !in_array($userRole, $allowedRoles)) {
           
                if ($request->isAJAX()) {
                    $response = service('response');
                    return $response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Akses ditolak atau sesi berakhir.', 'csrf_hash' => csrf_hash()]);
                } else {
                    session()->setFlashdata('error', 'Anda tidak memiliki hak akses ke halaman ini.');
                    
                    return redirect()->to(site_url($userRole === 'admin' ? 'admin/dashboard' : ($userRole === 'kasir' ? 'kasir/dashboard' : 'login')));
                }
        }
        
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        
    }
}   