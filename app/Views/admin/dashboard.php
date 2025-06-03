<?php /* Dashboard */ ?>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
  <div class="bg-white p-6 rounded shadow text-center">
    <h3 class="text-xl font-bold">Utilisateurs</h3>
    <p class="mt-4 text-3xl"><?= $totalUsers ?></p>
  </div>
  <div class="bg-white p-6 rounded shadow text-center">
    <h3 class="text-xl font-bold">Produits</h3>
    <p class="mt-4 text-3xl"><?= $totalProducts ?></p>
  </div>
  <div class="bg-white p-6 rounded shadow text-center">
    <h3 class="text-xl font-bold">Chiffre d'affaires</h3>
    <p class="mt-4 text-3xl"><?= number_format($totalSales,2,',',' ') ?> €</p>
  </div>
  <div class="bg-white p-6 rounded shadow col-span-full">
    <h3 class="text-xl font-bold mb-4">Commandes récentes</h3>
    <ul class="space-y-2">
      <?php foreach($recentOrders as $inv): ?>
      <li>
        <a href="/compte/facture/<?= $inv['id'] ?>" class="hover:underline">
          <?= htmlspecialchars($inv['id']) ?> — <?= htmlspecialchars($inv['created_at']) ?> — 
          <?= number_format($inv['total_amount'],2,',',' ') ?> €
        </a>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
  <a href="/admin/users" class="bg-blue-600 hover:bg-blue-700 text-white p-6 rounded shadow text-center">
    <h3 class="text-lg font-semibold">Gérer les utilisateurs</h3>
    <p class="mt-2">Voir, créer, modifier ou supprimer des utilisateurs</p>
  </a>
  <a href="/admin/products" class="bg-green-600 hover:bg-green-700 text-white p-6 rounded shadow text-center">
    <h3 class="text-lg font-semibold">Gérer les produits</h3>
    <p class="mt-2">Voir, créer, modifier ou supprimer des produits</p>
  </a>
  <a href="/admin/stock" class="bg-yellow-600 hover:bg-yellow-700 text-white p-6 rounded shadow text-center">
    <h3 class="text-lg font-semibold">Gérer le stock</h3>
    <p class="mt-2">Consulter et ajuster les quantités en stock</p>
  </a>
</div>