<?php /* Formulaire création / édition utilisateur */ ?>
<h2 class="text-2xl font-bold mb-4">
  <?= !empty($adminUsersEdit) ? 'Modifier un utilisateur' : 'Créer un utilisateur' ?>
</h2>
<form action="<?= !empty($adminUsersEdit) ? '/admin/users/edit/'.$user['id'] : '/admin/users/edit/0' ?>" method="post" class="bg-white p-6 rounded shadow space-y-4">
  <label class="block">
    Username
    <input type="text" name="username" required value="<?= htmlspecialchars($user['username'] ?? '',ENT_QUOTES) ?>" class="w-full border rounded p-2">
  </label>
  <label class="block">
    Email
    <input type="email" name="email" required value="<?= htmlspecialchars($user['email'] ?? '',ENT_QUOTES) ?>" class="w-full border rounded p-2">
  </label>
  <label class="block">
    Rôle
    <select name="role" class="w-full border rounded p-2">
      <option value="user" <?= (isset($user['role'])&&$user['role']==='user')?'selected':'' ?>>User</option>
      <option value="admin" <?= (isset($user['role'])&&$user['role']==='admin')?'selected':'' ?>>Admin</option>
    </select>
  </label>
  <label class="block">
    Mot de passe
    <input type="password" name="password" <?= empty($adminUsersEdit)?'required':'' ?> class="w-full border rounded p-2" placeholder="Laisser vide pour ne pas changer">
  </label>
  <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
    <?= !empty($adminUsersEdit) ? 'Enregistrer' : 'Créer' ?>
  </button>
</form>
