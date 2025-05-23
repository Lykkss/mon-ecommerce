<?php
namespace App\Controllers\Admin;

use App\Models\User;
use App\Core\Database;

class UserController
{
    /** Liste tous les utilisateurs */
    public function index(): void
    {
        $users = User::all();
        $adminUsers = true;
        require __DIR__ . '/../../Views/layout.php';
    }

    /** Formulaire de création d'un nouvel utilisateur */
    public function createForm(): void
    {
        $adminUsersCreate = true;
        require __DIR__ . '/../../Views/layout.php';
    }

    /** Traitement de la création d'un utilisateur */
    public function createSubmit(): void
    {
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $role     = $_POST['role'] ?? 'user';
        $password = $_POST['password'] ?? '';

        $errors = [];
        if ($username === '') {
            $errors[] = 'Le nom d\'utilisateur est requis.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email invalide.';
        }
        if (!in_array($role, ['user', 'admin'], true)) {
            $errors[] = 'Rôle invalide.';
        }
        if (strlen($password) < 6) {
            $errors[] = 'Le mot de passe doit faire au moins 6 caractères.';
        }

        if ($errors) {
            $_SESSION['errors'] = $errors;
            header('Location: /admin/users/edit/0');
            exit;
        }

        // Hash du mot de passe
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Création en base
        User::create([
            'username' => $username,
            'email'    => $email,
            'role'     => $role,
            'password' => $hash,
        ]);

        $_SESSION['success'] = 'Utilisateur créé.';
        header('Location: /admin/users');
    }

    /** Formulaire d'édition */
    public function editForm(int $id): void
    {
        $userToEdit = User::findById($id);
        if (!$userToEdit) {
            $_SESSION['errors'] = ["Utilisateur #{$id} introuvable."];
            header('Location: /admin/users');
            exit;
        }
        $adminUsersEdit = true;
        require __DIR__ . '/../../Views/layout.php';
    }

    /** Traitement de l'édition */
    public function editSubmit(int $id): void
    {
        $user = User::findById($id);
        if (!$user) {
            $_SESSION['errors'] = ["Utilisateur #{$id} introuvable."];
            header('Location: /admin/users');
            exit;
        }

        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $role     = $_POST['role'] ?? 'user';
        $password = $_POST['password'] ?? '';

        $errors = [];
        if ($username === '') {
            $errors[] = 'Le nom d\'utilisateur est requis.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email invalide.';
        }
        if (!in_array($role, ['user', 'admin'], true)) {
            $errors[] = 'Rôle invalide.';
        }
        if ($password !== '' && strlen($password) < 6) {
            $errors[] = 'Le mot de passe doit faire au moins 6 caractères.';
        }

        if ($errors) {
            $_SESSION['errors'] = $errors;
            header("Location: /admin/users/edit/{$id}");
            exit;
        }

        // Prépare les données à mettre à jour
        $data = [
            'username' => $username,
            'email'    => $email,
            'role'     => $role,
        ];
        if ($password !== '') {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        User::update($id, $data);

        $_SESSION['success'] = 'Utilisateur mis à jour.';
        header('Location: /admin/users');
    }

    /** Suppression */
    public function delete(int $id): void
    {
        User::delete($id);
        $_SESSION['success'] = 'Utilisateur supprimé.';
        header('Location: /admin/users');
    }
}
