<?php /* app/Views/admin/dashboard.php */ ?>

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

  <!-- Catégories (nouveau) -->
  <div class="bg-white p-6 rounded-lg shadow-lg text-center transform hover:scale-105 transition">
    <h3 class="text-xl font-bold text-gray-700">Catégories</h3>
    <p class="mt-4 text-4xl font-extrabold text-purple-600"><?= $totalCategories ?></p>
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

  <a href="/admin/categories"
     class="bg-purple-600 hover:bg-purple-700 text-white p-6 rounded-lg shadow-lg text-center transform hover:scale-105 transition">
    <h3 class="text-lg font-semibold">Gérer les catégories</h3>
    <p class="mt-2 text-sm">Ajouter ou supprimer des catégories</p>
  </a>
</div>

<!-- Modération -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
  <a href="/admin/comments"
     class="bg-indigo-600 hover:bg-indigo-700 text-white p-6 rounded-lg shadow-lg text-center transform hover:scale-105 transition">
    <h3 class="text-lg font-semibold">Gérer les commentaires</h3>
    <p class="mt-2 text-sm">Modérer, supprimer des commentaires</p>
  </a>

  <a href="/admin/favorites"
     class="bg-pink-600 hover:bg-pink-700 text-white p-6 rounded-lg shadow-lg text-center transform hover:scale-105 transition">
    <h3 class="text-lg font-semibold">Gérer les favoris</h3>
    <p class="mt-2 text-sm">Voir et retirer des favoris</p>
  </a>
</div>
