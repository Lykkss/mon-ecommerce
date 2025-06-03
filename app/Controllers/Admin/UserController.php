<?php
namespace App\Controllers\Admin;

use App\Models\User;

class UserController
{
    // 1) Liste tous les utilisateurs
    public function index(): void
    {
        $users      = User::all();
        $adminUsers = $users;
        require __DIR__ . '/../../Views/layout.php';
    }

    // 2) Formulaire de création
    public function createForm(): void
    {
        $adminUsersCreate = true;
        require __DIR__ . '/../../Views/layout.php';
    }

    // 3) Traitement de la création
    public function createSubmit(): void
    {
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $role     = $_POST['role'] ?? 'user';
        $password = $_POST['password'] ?? '';

        $errors = [];
        if ($username === '') {
            $errors[] = "Le nom d'utilisateur est requis.";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email invalide.";
        }
        if (!in_array($role, ['user','admin'], true)) {
            $errors[] = "Rôle invalide.";
        }
        if (strlen($password) < 6) {
            $errors[] = "Le mot de passe doit faire au moins 6 caractères.";
        }

        if ($errors) {
            $_SESSION['errors'] = $errors;
            header('Location: /admin/users/create');
            exit;
        }

        // On hache une UNIQUE fois avant de créer
        $hash = password_hash($password, PASSWORD_DEFAULT);

        User::create([
            'username' => $username,
            'email'    => $email,
            'role'     => $role,
            'password' => $hash,       // Mot de passe déjà haché
            'fullname' => $_POST['fullname'] ?? null,
            // 'avatar' peut être géré séparément si besoin
        ]);

        $_SESSION['success'] = 'Utilisateur créé.';
        header('Location: /admin/users');
        exit;
    }

    // 4) Formulaire d'édition
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

    // 5) Traitement de l'édition
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
            $errors[] = "Le nom d'utilisateur est requis.";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email invalide.";
        }
        if (!in_array($role, ['user','admin'], true)) {
            $errors[] = "Rôle invalide.";
        }
        if ($password !== '' && strlen($password) < 6) {
            $errors[] = "Le mot de passe doit faire au moins 6 caractères.";
        }

        if ($errors) {
            $_SESSION['errors'] = $errors;
            header("Location: /admin/users/edit/{$id}");
            exit;
        }

        $data = [
            'username' => $username,
            'email'    => $email,
            'role'     => $role,
        ];
        if ($password !== '') {
            // On transmet le mot de passe en clair, le modèle l’hachera
            $data['password'] = $password;
        }
        if (array_key_exists('fullname', $_POST)) {
            $data['fullname'] = trim($_POST['fullname']);
        }

        User::update($id, $data);
        $_SESSION['success'] = 'Utilisateur mis à jour.';
        header('Location: /admin/users');
        exit;
    }

    // 6) Suppression
    public function delete(int $id): void
    {
        User::delete($id);
        $_SESSION['success'] = 'Utilisateur supprimé.';
        header('Location: /admin/users');
        exit;
    }
}
