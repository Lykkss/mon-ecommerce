<?php /* app/Views/admin/users.php */ ?>

<div class="flex justify-between items-center mb-4">
  <h2 class="text-2xl font-bold">Tous les utilisateurs</h2>
  <a href="/admin/users/create"
     class="px-4 py-2 bg-green-600 text-white rounded">
    Créer un utilisateur
  </a>
</div>

<table class="w-full bg-white rounded shadow">
  <thead class="bg-gray-100">
    <tr>
      <th class="p-2">ID</th>
      <th class="p-2">Username</th>
      <th class="p-2">Email</th>
      <th class="p-2">Rôle</th>
      <th class="p-2">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($users as $u): ?>
    <tr class="border-t">
      <td class="p-2"><?= $u['id'] ?></td>
      <td class="p-2"><?= htmlspecialchars($u['username'], ENT_QUOTES) ?></td>
      <td class="p-2"><?= htmlspecialchars($u['email'], ENT_QUOTES) ?></td>
      <td class="p-2"><?= htmlspecialchars($u['role'], ENT_QUOTES) ?></td>
      <td class="p-2 space-x-2">
        <a href="/admin/users/edit/<?= $u['id'] ?>"
           class="text-blue-600">Éditer</a>
        <form action="/admin/users/delete/<?= $u['id'] ?>"
              method="post" class="inline"
              onsubmit="return confirm('Supprimer cet utilisateur ?');">
          <button class="text-red-600">Supprimer</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
