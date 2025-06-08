<?php /* app/Views/admin/comments.php */ ?>

<h2 class="text-2xl font-bold mb-4">Tous les commentaires</h2>

<?php if (!empty($_SESSION['success'])): ?>
  <div class="bg-green-100 p-4 rounded mb-4">
    <?= htmlspecialchars($_SESSION['success'], ENT_QUOTES) ?>
    <?php unset($_SESSION['success']); ?>
  </div>
<?php endif; ?>

<table class="w-full bg-white rounded shadow">
  <thead class="bg-gray-100">
    <tr>
      <th class="p-2">ID</th>
      <th class="p-2">Utilisateur</th>
      <th class="p-2">Produit</th>
      <th class="p-2">Commentaire</th>
      <th class="p-2">Date</th>
      <th class="p-2">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($adminComments as $c): ?>
    <tr class="border-t">
      <td class="p-2"><?= $c['id'] ?></td>
      <td class="p-2"><?= htmlspecialchars($c['username'], ENT_QUOTES) ?></td>
      <td class="p-2"><?= htmlspecialchars($c['product_name'], ENT_QUOTES) ?></td>
      <td class="p-2"><?= nl2br(htmlspecialchars($c['content'], ENT_QUOTES)) ?></td>
      <td class="p-2"><?= htmlspecialchars($c['created_at'], ENT_QUOTES) ?></td>
      <td class="p-2">
        <form action="/admin/comments/delete/<?= $c['id'] ?>" method="post"
              onsubmit="return confirm('Supprimer ce commentaire ?');">
          <button class="px-2 py-1 bg-red-600 text-white rounded">Supprimer</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
