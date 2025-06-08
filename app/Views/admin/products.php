<?php
// app/Views/admin/products.php

$adminProducts = $adminProducts ?? [];
$publicDir     = rtrim($_SERVER['DOCUMENT_ROOT'], '/');
?>

<div class="flex justify-between items-center mb-4">
  <h2 class="text-2xl font-bold">Tous les produits</h2>
  <a href="/admin/products/create" class="px-4 py-2 bg-green-600 text-white rounded">
    Créer un produit
  </a>
</div>

<table class="w-full bg-white rounded shadow">
  <thead class="bg-gray-100">
    <tr>
      <th class="p-2">ID</th>
      <th class="p-2">Image</th>
      <th class="p-2">Nom</th>
      <th class="p-2">Prix</th>
      <th class="p-2">Stock</th>
      <th class="p-2">Auteur</th>                      <!-- ← ajouté -->
      <th class="p-2">Statut</th>
      <th class="p-2">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($adminProducts as $p): 
        $relPath  = ltrim($p['image'] ?? '', '/');
        $fullPath = $publicDir . '/' . $relPath;
    ?>
      <tr class="border-t">
        <td class="p-2"><?= htmlspecialchars($p['id'], ENT_QUOTES) ?></td>
        <td class="p-2">
          <?php if ($relPath && file_exists($fullPath)): ?>
            <img src="/<?= htmlspecialchars($relPath, ENT_QUOTES) ?>"
                 alt="<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>"
                 class="w-16 h-16 object-cover rounded">
          <?php else: ?>
            <span class="text-gray-400 italic">Pas d’image</span>
          <?php endif; ?>
        </td>
        <td class="p-2"><?= htmlspecialchars($p['name'], ENT_QUOTES) ?></td>
        <td class="p-2"><?= number_format($p['price'], 2, ',', ' ') ?> €</td>
        <td class="p-2"><?= htmlspecialchars($p['stock'], ENT_QUOTES) ?></td>
        <td class="p-2">
          <?= htmlspecialchars($p['author_name'] ?? 'Inconnu', ENT_QUOTES) ?> <!-- ← affiche l’auteur -->
        </td>
        <td class="p-2">
          <?php if ((int)$p['stock'] > 0): ?>
            <span class="text-green-600 font-semibold">Stock suffisant</span>
          <?php else: ?>
            <span class="text-red-600 font-bold">Rupture</span>
          <?php endif; ?>
        </td>
        <td class="p-2 space-x-2">
          <a href="/admin/products/edit/<?= $p['id'] ?>" class="text-blue-600">Éditer</a>
          <form action="/admin/products/delete/<?= $p['id'] ?>" method="post" class="inline">
            <button onclick="return confirm('Confirmer ?')" class="text-red-600">Supprimer</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
