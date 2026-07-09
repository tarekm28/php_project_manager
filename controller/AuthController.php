<?php

class AuthController extends Controller
{
    public function login()
    {
        $this->view('auth/login');
    }

    public function authenticate()
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        Response::redirect('/dashboard');
    }

    public function logout()
    {
        session_destroy();

        Response::redirect('/login');
    }
}