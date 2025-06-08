<?php /* Dashboard */ ?>

<!-- Statistiques en haut -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
  <!-- Utilisateurs -->
  <div class="bg-white p-6 rounded-lg shadow-lg text-center transform hover:scale-105 transition">
    <h3 class="text-xl font-bold text-gray-700">Utilisateurs</h3>
    <p class="mt-4 text-4xl font-extrabold text-blue-600"><?= $totalUsers ?></p>
  </div>

  <!-- Produits -->
  <div class="bg-white p-6 rounded-lg shadow-lg text-center transform hover:scale-105 transition">
    <h3 class="text-xl font-bold text-gray-700">Produits</h3>
    <p class="mt-4 text-4xl font-extrabold text-green-600"><?= $totalProducts ?></p>
  </div>

  <!-- Chiffre d'affaires -->
  <div class="bg-white p-6 rounded-lg shadow-lg text-center transform hover:scale-105 transition">
    <h3 class="text-xl font-bold text-gray-700">Chiffre d'affaires</h3>
    <p class="mt-4 text-4xl font-extrabold text-yellow-600"><?= number_format($totalSales,2,',',' ') ?> €</p>
  </div>

  <!-- Commandes récentes -->
  <div class="bg-white p-6 rounded-lg shadow-lg transform hover:scale-105 transition lg:col-span-2">
    <h3 class="text-xl font-bold text-gray-700 mb-4">Commandes récentes</h3>
    <ul class="space-y-2 text-gray-600">
      <?php foreach($recentOrders as $inv): ?>
      <li class="flex justify-between items-center">
        <div>
          <a href="/compte/facture/<?= $inv['id'] ?>" class="hover:text-blue-600">
            <span class="font-medium"><?= htmlspecialchars($inv['id']) ?></span> —
            <span><?= htmlspecialchars($inv['created_at']) ?></span>
          </a>
        </div>
        <div class="font-semibold"><?= number_format($inv['total_amount'],2,',',' ') ?> €</div>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>

<!-- Liens d’administration -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
  <a href="/admin/users"
     class="bg-blue-600 hover:bg-blue-700 text-white p-6 rounded-lg shadow-lg text-center transform hover:scale-105 transition">
    <h3 class="text-lg font-semibold">Gérer les utilisateurs</h3>
    <p class="mt-2 text-sm">Voir, créer, modifier ou supprimer des utilisateurs</p>
  </a>

  <a href="/admin/products"
     class="bg-green-600 hover:bg-green-700 text-white p-6 rounded-lg shadow-lg text-center transform hover:scale-105 transition">
    <h3 class="text-lg font-semibold">Gérer les produits</h3>
    <p class="mt-2 text-sm">Voir, créer, modifier ou supprimer des produits</p>
  </a>

  <a href="/admin/stock"
     class="bg-yellow-600 hover:bg-yellow-700 text-white p-6 rounded-lg shadow-lg text-center transform hover:scale-105 transition">
    <h3 class="text-lg font-semibold">Gérer le stock</h3>
    <p class="mt-2 text-sm">Consulter et ajuster les quantités en stock</p>
  </a>
</div>

<!-- Modération -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
  <a href="/admin/comments"
     class="bg-purple-600 hover:bg-purple-700 text-white p-6 rounded-lg shadow-lg text-center transform hover:scale-105 transition">
    <h3 class="text-lg font-semibold">Gérer les commentaires</h3>
    <p class="mt-2 text-sm">Modérer, supprimer des commentaires</p>
  </a>

  <a href="/admin/favorites"
     class="bg-pink-600 hover:bg-pink-700 text-white p-6 rounded-lg shadow-lg text-center transform hover:scale-105 transition">
    <h3 class="text-lg font-semibold">Gérer les favoris</h3>
    <p class="mt-2 text-sm">Voir et retirer des favoris</p>
  </a>
</div>
