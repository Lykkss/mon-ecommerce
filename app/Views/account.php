<?php
// Erreurs
if (!empty($_SESSION['errors'])): ?>
  <ul class="mb-4 text-red-600">
    <?php foreach ($_SESSION['errors'] as $e): ?>
      <li><?= htmlspecialchars($e, ENT_QUOTES) ?></li>
    <?php endforeach; ?>
  </ul>
<?php unset($_SESSION['errors']); endif; ?>

<?php
// Message de succès
if (!empty($_SESSION['success'])): ?>
  <p class="mb-4 text-green-600">
    <?= htmlspecialchars($_SESSION['success'], ENT_QUOTES) ?>
  </p>
<?php unset($_SESSION['success']); endif; ?>

<div class="bg-white p-6 rounded shadow mb-6">
  <h2 class="text-2xl font-bold mb-4">Mon profil</h2>
  <form id="profile-form"
        action="/compte/mettre-a-jour"
        method="post"
        enctype="multipart/form-data"
        class="space-y-4">

    <!-- Nom complet -->
    <label class="block">
      Nom complet
      <input
        type="text"
        name="fullname"
        required
        value="<?= htmlspecialchars($user['fullname'] ?? '', ENT_QUOTES) ?>"
        class="w-full border rounded p-2"
      >
    </label>

    <!-- Prévisualisation avatar -->
    <div class="block">
      <span class="block font-medium mb-1">Avatar actuel</span>
      <img
        id="avatar-preview"
        src="<?= htmlspecialchars(
               '/' . ltrim($user['avatar'] ?? 'assets/avatars/default.png','/'),
               ENT_QUOTES
             ) ?>"
        alt="Avatar"
        class="h-24 w-24 rounded-full mb-2 object-cover"
      >
    </div>

    <!-- Input fichier -->
    <label class="block">
      Changer d’avatar
      <input
        id="avatar-input"
        type="file"
        name="avatar"
        accept="image/jpeg,image/png"
        class="mt-1"
      >
    </label>

    <!-- Submit -->
    <button
      type="submit"
      class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded"
    >
      Mettre à jour mon profil
    </button>
  </form>
</div>

<!-- Historique des commandes -->
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
            <td class="p-2">
              <a href="/compte/facture/<?= htmlspecialchars($inv['id'], ENT_QUOTES) ?>"
                 class="text-blue-600 hover:underline">
                <?= htmlspecialchars($inv['id'], ENT_QUOTES) ?>
              </a>
            </td>
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

<!-- Preview instantanée -->
<script>
  const input   = document.getElementById('avatar-input');
  const preview = document.getElementById('avatar-preview');

  input.addEventListener('change', function() {
    if (this.files && this.files[0]) {
      preview.src = URL.createObjectURL(this.files[0]);
    }
  });
</script>
