<?php
// app/Views/account.php

// Affiche les erreurs de validation si nécessaire
if (!empty($_SESSION['errors'])): ?>
  <ul class="mb-4 text-red-600">
    <?php foreach ($_SESSION['errors'] as $e): ?>
      <li><?= htmlspecialchars($e, ENT_QUOTES) ?></li>
    <?php endforeach;
    unset($_SESSION['errors']); ?>
  </ul>
<?php endif; ?>

<div class="bg-white p-6 rounded shadow mb-6">
  <h2 class="text-2xl font-bold mb-4">Mon profil</h2>
  <form action="/compte/mettre-a-jour"
        method="post"
        enctype="multipart/form-data"
        class="space-y-4">
    <label class="block">
      Nom complet
      <input type="text"
             name="fullname"
             required
             value="<?= htmlspecialchars($user['fullname'] ?? '', ENT_QUOTES) ?>"
             class="w-full border rounded p-2">
    </label>

    <div class="block">
      <span class="block font-medium mb-1">Avatar actuel</span>
      <?php if (!empty($user['avatar'])): ?>
        <img src="/<?= htmlspecialchars($user['avatar'], ENT_QUOTES) ?>"
             alt="Avatar"
             class="h-24 w-24 rounded-full mb-2 object-cover">
      <?php else: ?>
        <p class="text-gray-500">Aucun avatar défini.</p>
      <?php endif; ?>
    </div>

    <label class="block">
      Changer d’avatar
      <input type="file"
             name="avatar"
             accept="image/jpeg,image/png"
             class="mt-1">
    </label>

    <button type="submit"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
      Mettre à jour mon profil
    </button>
  </form>
</div>

<div class="bg-white p-6 rounded shadow">
  <h2 class="text-2xl font-bold mb-4">Historique des commandes</h2>
  <?php if (empty($invoices)): ?>
    <p>Vous n’avez encore passé aucune commande.</p>
  <?php else: ?>
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="bg-gray-100">
          <th class="p-2"># Commande</th>
          <th class="p-2">Date</th>
          <th class="p-2">Montant</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($invoices as $inv): ?>
          <tr class="border-t">
            <td class="p-2"><?= htmlspecialchars($inv['id'], ENT_QUOTES) ?></td>
            <td class="p-2"><?= htmlspecialchars($inv['created_at'], ENT_QUOTES) ?></td>
            <td class="p-2 font-bold">
              <?= number_format($inv['total_amount'], 2, ',', ' ') ?> €
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
