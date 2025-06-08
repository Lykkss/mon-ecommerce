<?php // app/Views/account.php

/** @var array $user */
/** @var float $balance */
/** @var array[] $invoices */
/** @var array[] $myProducts */
// Cache-busting pour l’avatar
$ts = $_SESSION['avatar_ts'] ?? null;
?>

<!-- Popup JS pour les messages de succès -->
<?php if (!empty($_SESSION['success'])): ?>
  <!-- Inline -->
  <p class="mb-4 text-green-600">
    <?= htmlspecialchars($_SESSION['success'], ENT_QUOTES) ?>
  </p>
  <!-- Popup -->
  <script>
    alert("<?= addslashes($_SESSION['success']) ?>");
  </script>
  <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<!-- Erreurs -->
<?php if (!empty($_SESSION['errors'])): ?>
  <ul class="mb-4 text-red-600">
    <?php foreach ($_SESSION['errors'] as $e): ?>
      <li><?= htmlspecialchars($e, ENT_QUOTES) ?></li>
    <?php endforeach; unset($_SESSION['errors']); ?>
  </ul>
<?php endif; ?>

<!-- MON PROFIL -->
<div class="bg-white p-6 rounded shadow mb-6">
  <h2 class="text-2xl font-bold mb-4">Mon profil</h2>
  <form id="profile-form"
        action="/compte/mettre-a-jour"
        method="post"
        enctype="multipart/form-data"
        class="space-y-4">

    <label class="block">
      Nom complet
      <input type="text" name="fullname" required
             value="<?= htmlspecialchars($user['fullname'] ?? '', ENT_QUOTES) ?>"
             class="w-full border rounded p-2">
    </label>

    <div>
      <span class="block font-medium mb-1">Avatar actuel</span>
      <img id="avatar-preview"
           src="/<?= ltrim(htmlspecialchars($user['avatar'] ?? 'assets/avatars/default.png', ENT_QUOTES), '/') ?><?= $ts ? '?t='.$ts : '' ?>"
           alt="Avatar"
           class="h-24 w-24 rounded-full mb-2 object-cover">
    </div>

    <label class="block">
      Changer d’avatar
      <input id="avatar-input" type="file" name="avatar"
             accept="image/jpeg,image/png"
             class="mt-1">
    </label>

    <button type="submit"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
      Mettre à jour mon profil
    </button>
  </form>
</div>

<!-- MON SOLDE -->
<div class="bg-white p-6 rounded shadow mb-6">
  <h2 class="text-2xl font-bold mb-4">Mon solde</h2>
  <p class="mb-4 text-lg">
    Votre solde est de 
    <span class="font-semibold"><?= number_format($balance, 2, ',', ' ') ?> €</span>
  </p>
  <form action="/compte/deposer" method="post" class="flex space-x-2">
    <input type="number" name="amount" step="0.01" min="0.01" required
           placeholder="Montant à déposer"
           class="border rounded p-2 w-1/3">
    <button type="submit"
            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded">
      Déposer
    </button>
  </form>
</div>

<!-- HISTORIQUE DES COMMANDES -->
<div class="bg-white p-6 rounded shadow mb-6">
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
          <th class="p-2">Statut</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($invoices as $inv): ?>
          <tr class="border-t">
            <td class="p-2">
              <a href="/compte/facture/<?= htmlspecialchars($inv['id'], ENT_QUOTES) ?>"
                 class="text-blue-600 hover:underline">
                <?= htmlspecialchars($inv['id'], ENT_QUOTES) ?>
              </a>
            </td>
            <td class="p-2"><?= htmlspecialchars($inv['created_at'], ENT_QUOTES) ?></td>
            <td class="p-2 font-bold"><?= number_format($inv['total_amount'], 2, ',', ' ') ?> €</td>
            <td class="p-2">
              <?php if (empty($inv['paid_at'])): ?>
                <form action="/compte/payer" method="post" class="inline">
                  <input type="hidden" name="invoice_id" value="<?= htmlspecialchars($inv['id'], ENT_QUOTES) ?>">
                  <button type="submit"
                          class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm">
                    Payer
                  </button>
                </form>
              <?php else: ?>
                <span class="text-gray-600">Payée le <?= htmlspecialchars($inv['paid_at'], ENT_QUOTES) ?></span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<!-- MES PRODUITS À VENDRE -->
<div class="bg-white p-6 rounded shadow mb-6">
  <div class="flex justify-between items-center mb-4">
    <h2 class="text-2xl font-bold">Mes produits à vendre</h2>
    <a href="/sell" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded">
      Publier un nouveau produit
    </a>
  </div>

  <?php if (empty($myProducts)): ?>
    <p>Vous n’avez encore publié aucun produit à la vente.</p>
  <?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php foreach ($myProducts as $prod): ?>
        <div class="bg-gray-50 p-4 rounded shadow">
          <?php if (!empty($prod['image'])): ?>
            <img src="/<?= htmlspecialchars($prod['image'], ENT_QUOTES) ?>"
                 alt="<?= htmlspecialchars($prod['title'], ENT_QUOTES) ?>"
                 class="w-full h-32 object-cover rounded mb-2">
          <?php else: ?>
            <div class="w-full h-32 bg-gray-200 rounded mb-2 flex items-center justify-center text-gray-500 text-sm">
              Aucune image
            </div>
          <?php endif; ?>

          <h3 class="font-semibold text-lg"><?= htmlspecialchars($prod['title'], ENT_QUOTES) ?></h3>
          <p class="text-sm text-gray-600 mb-1"><?= number_format($prod['price'], 2, ',', ' ') ?> €</p>

          <div class="flex space-x-2">
            <a href="/edit/<?= $prod['id'] ?>"
               class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm">
              Éditer
            </a>
            <form action="/delete/<?= $prod['id'] ?>" method="post"
                  onsubmit="return confirm('Voulez-vous vraiment supprimer ce produit ?');">
              <button type="submit"
                      class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-sm">
                Supprimer
              </button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<script>
  // Preview avatar
  const input = document.getElementById('avatar-input');
  const preview = document.getElementById('avatar-preview');
  if (input && preview) {
    input.addEventListener('change', function() {
      if (this.files && this.files[0]) {
        preview.src = URL.createObjectURL(this.files[0]);
      }
    });
  }
</script>
