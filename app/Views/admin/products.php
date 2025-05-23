<?php /* Liste des produits */ ?>
<div class="flex justify-between items-center mb-4">
  <h2 class="text-2xl font-bold">Tous les produits</h2>
  <a href="/admin/products/create" class="px-4 py-2 bg-green-600 text-white rounded">Créer un produit</a>
</div>
<table class="w-full bg-white rounded shadow">
  <thead class="bg-gray-100">
    <tr>
      <th class="p-2">ID</th>
      <th class="p-2">Titre</th>
      <th class="p-2">Prix</th>
      <th class="p-2">Stock</th>
      <th class="p-2">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($adminProducts as $p): ?>
    <tr class="border-t">
      <td class="p-2"><?= $p['id'] ?></td>
      <td class="p-2"><?= htmlspecialchars($p['title'],ENT_QUOTES) ?></td>
      <td class="p-2"><?= number_format($p['price'],2,',',' ') ?> €</td>
      <td class="p-2"><?= $p['stock'] ?? '—' ?></td>
      <td class="p-2 space-x-2">
        <a href="/admin/products/edit/<?= $p['id'] ?>" class="text-blue-600">Éditer</a>
        <form action="/admin/products/delete/<?= $p['id'] ?>" method="post" class="inline">
          <button onclick="return confirm('Confirmer ?')" class="text-red-600">Supprimer</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>