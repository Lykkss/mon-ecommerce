<?php
namespace App\Controllers;

use App\Models\User;

class AuthController
{
    public function registerForm(): void
    {
        $register = true;
        require __DIR__ . '/../Views/layout.php';
    }

    public function registerSubmit(): void
    {
        // Récupération des champs POST
        $username  = trim($_POST['username']  ?? '');
        $email     = trim($_POST['email']     ?? '');
        $password  = $_POST['password']       ?? '';
        $password2 = $_POST['password2']      ?? '';
        $fullname  = trim($_POST['fullname']  ?? '');

        $errors = [];

        // Validations
        if ($username === '') {
            $errors[] = "Username requis";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email invalide";
        } elseif (User::findByEmail($email)) {
            $errors[] = "Email déjà utilisé";
        }
        if (strlen($password) < 6) {
            $errors[] = "Mot de passe trop court (minimum 6 caractères)";
        }
        if ($password !== $password2) {
            $errors[] = "Les mots de passe ne correspondent pas";
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: /register');
            exit;
        }

        // Création de l’utilisateur avec rôle 'user' par défaut
        $userId = User::create([
            'username' => $username,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'fullname' => $fullname,
            'role'     => 'user',
        ]);

        // On logue l’utilisateur
        $_SESSION['user_id']   = $userId;
        $_SESSION['user_role'] = 'user';

        header('Location: /');
        exit;
    }

    public function loginForm(): void
    {
        $login = true;
        require __DIR__ . '/../Views/layout.php';
    }

    public function loginSubmit(): void
    {
        $email = trim($_POST['email'] ?? '');
        $pass  = $_POST['password']    ?? '';
        $user  = User::findByEmail($email);

        if (!$user || !password_verify($pass, $user['password'])) {
            $_SESSION['errors'] = ["Email ou mot de passe incorrect"];
            header('Location: /login');
            exit;
        }

        // Stockage de l'ID et du rôle
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_role'] = $user['role'];

        // Redirection en fonction du rôle
        if ($user['role'] === 'admin') {
            header('Location: /admin');
        } else {
            header('Location: /');
        }
        exit;
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: /login');
        exit;
    }
}
