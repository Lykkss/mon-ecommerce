<?php /* app/Views/admin/user_form.php */ ?>

<h2 class="text-2xl font-bold mb-4">
  <?= !empty($adminUsersEdit)
        ? 'Modifier un utilisateur'
        : 'Créer un utilisateur' ?>
</h2>

<form action="<?= !empty($adminUsersEdit)
                 ? '/admin/users/edit/'.$userToEdit['id']
                 : '/admin/users/create' ?>"
      method="post"
      enctype="multipart/form-data"
      class="bg-white p-6 rounded shadow space-y-4">

  <label class="block">
    Username
    <input type="text"
           name="username"
           required
           value="<?= htmlspecialchars($userToEdit['username'] ?? '', ENT_QUOTES) ?>"
           class="w-full border rounded p-2">
  </label>

  <label class="block">
    Email
    <input type="email"
           name="email"
           required
           value="<?= htmlspecialchars($userToEdit['email'] ?? '', ENT_QUOTES) ?>"
           class="w-full border rounded p-2">
  </label>

  <label class="block">
    Rôle
    <select name="role"
            class="w-full border rounded p-2">
      <option value="user"
        <?= (isset($userToEdit['role']) && $userToEdit['role'] === 'user') ? 'selected' : '' ?>>
        User
      </option>
      <option value="admin"
        <?= (isset($userToEdit['role']) && $userToEdit['role'] === 'admin') ? 'selected' : '' ?>>
        Admin
      </option>
    </select>
  </label>

  <label class="block">
    Nom complet
    <input type="text"
           name="fullname"
           value="<?= htmlspecialchars($userToEdit['fullname'] ?? '', ENT_QUOTES) ?>"
           class="w-full border rounded p-2">
  </label>

  <?php if (!empty($userToEdit['avatar'])): ?>
    <div class="block">
      <span class="font-medium">Avatar actuel</span>
      <img src="/<?= htmlspecialchars($userToEdit['avatar'], ENT_QUOTES) ?>"
           alt="Avatar"
           class="h-24 w-24 rounded-full object-cover mt-2 mb-4">
    </div>
  <?php endif; ?>

  <label class="block">
    Avatar
    <input type="file"
           name="avatar"
           accept="image/jpeg,image/png"
           class="mt-1">
  </label>

  <label class="block">
    Mot de passe
    <input type="password"
           name="password"
           <?= empty($adminUsersEdit) ? 'required' : '' ?>
           class="w-full border rounded p-2"
           placeholder="<?= !empty($adminUsersEdit) ? 'Laisser vide pour ne pas changer' : '' ?>">
  </label>

  <button type="submit"
          class="px-4 py-2 bg-blue-600 text-white rounded">
    <?= !empty($adminUsersEdit) ? 'Enregistrer' : 'Créer' ?>
  </button>
</form>
