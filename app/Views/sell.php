<?php
/**
 * sell.php
 *
 * Ce template sert à la fois à :
 * - Publier un nouveau produit (GET /sell → $sell = true)
 * - Modifier un produit existant (GET /edit/{id} → $edit = true, $prod + $stock définis)
 */

// On détecte si on est en mode édition
$isEdit = !empty($edit);

// Détermine l’URL du formulaire et les textes en fonction du mode
$formAction = $isEdit
    ? '/edit/' . (int)$prod['id']
    : '/sell';

$formTitle = $isEdit
    ? 'Modifier mon produit'
    : 'Vendre un Pokémon';

$btnText = $isEdit
    ? 'Enregistrer les modifications'
    : 'Publier';
?>

<h2 class="text-2xl font-bold mb-4"><?= $formTitle ?></h2>

<?php if (!empty($_SESSION['errors'])): ?>
  <ul class="text-red-600 mb-4">
    <?php foreach ($_SESSION['errors'] as $e): ?>
      <li><?= htmlspecialchars($e, ENT_QUOTES) ?></li>
    <?php endforeach; unset($_SESSION['errors']); ?>
  </ul>
<?php endif; ?>

<form action="<?= $formAction ?>"
      method="post"
      enctype="multipart/form-data"
      class="space-y-4 bg-white p-6 rounded shadow">

  <!-- Titre -->
  <label class="block">
    Titre
    <input type="text"
           name="title"
           required
           value="<?= htmlspecialchars($prod['title'] ?? '', ENT_QUOTES) ?>"
           class="w-full border rounded p-2">
  </label>

  <!-- Description -->
  <label class="block">
    Description
    <textarea name="description"
              class="w-full border rounded p-2"
              rows="4"><?= htmlspecialchars($prod['description'] ?? '', ENT_QUOTES) ?></textarea>
  </label>

  <!-- Prix -->
  <label class="block">
    Prix
    <input type="number"
           step="0.01"
           name="price"
           required
           value="<?= htmlspecialchars($prod['price'] ?? '', ENT_QUOTES) ?>"
           class="w-full border rounded p-2">
  </label>

  <!-- Stock -->
  <label class="block">
    Stock
    <input type="number"
           name="stock"
           required
           min="0"
           value="<?= htmlspecialchars($stock ?? '', ENT_QUOTES) ?>"
           class="w-full border rounded p-2">
  </label>

  <!-- Image -->
  <label class="block">
    Image
    <input type="file"
           name="image"
           accept="image/jpeg,image/png"
           class="w-full border rounded p-2">
  </label>

  <?php if ($isEdit && !empty($prod['image'])): ?>
    <div>
      <span class="text-sm text-gray-600">Image actuelle :</span>
      <img src="/<?= htmlspecialchars($prod['image'], ENT_QUOTES) ?>"
           alt="Aperçu"
           class="h-24 w-24 object-cover rounded mt-2">
    </div>
  <?php endif; ?>

  <button type="submit"
          class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded">
    <?= $btnText ?>
  </button>
</form>
