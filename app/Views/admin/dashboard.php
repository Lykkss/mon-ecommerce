<?php /* Dashboard */ ?>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
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