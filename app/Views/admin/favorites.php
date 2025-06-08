<?php /* app/Views/admin/favorites.php */ ?>

<h2 class="text-2xl font-bold mb-4">Tous les favoris</h2>

<?php if (!empty($_SESSION['success'])): ?>
  <div class="bg-green-100 p-4 rounded mb-4">
    <?= htmlspecialchars($_SESSION['success'], ENT_QUOTES) ?>
    <?php unset($_SESSION['success']); ?>
  </div>
<?php endif; ?>

<table class="w-full bg-white rounded shadow">
  <thead class="bg-gray-100">
    <tr>
      <th class="p-2">Utilisateur</th>
      <th class="p-2">Produit</th>
      <th class="p-2">Ajouté le</th>
      <th class="p-2">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($adminFavorites as $f): ?>
    <tr class="border-t">
      <td class="p-2"><?= htmlspecialchars($f['username'], ENT_QUOTES) ?></td>
      <td class="p-2"><?= htmlspecialchars($f['product_name'], ENT_QUOTES) ?></td>
      <td class="p-2"><?= htmlspecialchars($f['created_at'], ENT_QUOTES) ?></td>
      <td class="p-2">
        <form action="/admin/favorites/delete/<?= $f['user_id'] ?>-<?= $f['product_id'] ?>"
              method="post"
              onsubmit="return confirm('Retirer ce favori ?');">
          <button class="px-2 py-1 bg-red-600 text-white rounded">Retirer</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
