<?php /* app/Views/admin/favorites.php */ ?>

<h2 class="text-2xl font-bold mb-4">Tous les favoris</h2>

<?php if (!empty($_SESSION['success'])): ?>
  <div class="bg-green-100 border border-green-300 p-4 rounded mb-6 text-green-800">
    <?= htmlspecialchars($_SESSION['success'], ENT_QUOTES) ?>
    <?php unset($_SESSION['success']); ?>
  </div>
<?php endif; ?>

<table class="w-full bg-white rounded shadow overflow-hidden">
  <thead class="bg-gray-100">
    <tr class="text-left text-gray-700">
      <th class="p-3">Utilisateur</th>
      <th class="p-3">Produit</th>
      <th class="p-3">Ajouté le</th>
      <th class="p-3">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($adminFavorites as $f): ?>
      <tr class="border-t hover:bg-gray-50 transition">
        <!-- Utilisateur -->
        <td class="p-3">
          <a href="/admin/users/edit/<?= (int)$f['user_id'] ?>"
             class="text-blue-600 hover:underline">
            <?= htmlspecialchars($f['username'], ENT_QUOTES) ?>
          </a>
        </td>

        <!-- Produit avec miniature -->
        <td class="p-3 flex items-center space-x-3">
          <?php if (!empty($f['image'])): ?>
            <img src="/<?= htmlspecialchars($f['image'], ENT_QUOTES) ?>"
                 alt="<?= htmlspecialchars($f['product_name'], ENT_QUOTES) ?>"
                 class="w-12 h-12 object-cover rounded">
          <?php else: ?>
          <a href="/produit/<?= (int)$f['product_id'] ?>"
             class="text-blue-600 hover:underline">
            <?= htmlspecialchars($f['product_name'], ENT_QUOTES) ?>
          </a>
          <?php endif; ?>
        </td>

        <!-- Date -->
        <td class="p-3">
          <?= date('d/m/Y H:i', strtotime($f['created_at'])) ?>
        </td>

        <!-- Bouton Retirer -->
        <td class="p-3">
          <form action="/admin/favorites/delete/<?= (int)$f['user_id'] ?>-<?= (int)$f['product_id'] ?>"
                method="post"
                onsubmit="return confirm('Retirer ce favori ?');"
                class="inline">
            <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">
              Retirer
            </button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>

    <?php if (empty($adminFavorites)): ?>
      <tr>
        <td colspan="4" class="p-4 text-center text-gray-500">
          Aucun favori enregistré.
        </td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>
