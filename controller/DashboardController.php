<?php
require_once __DIR__ . '/../core/auth.php';
require_once __DIR__ . '/../core/Controller.php';

class DashboardController extends Controller
{
    public function index()
    {
        Auth::requireLogin();

        if (Auth::isAdmin()) {
            $this->view('admin/dashboard');
        } else {
            $this->view('employee/dashboard');
        }
    }
}