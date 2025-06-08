<?php
/**
 * sell.php
 *
 * Template pour publier ou modifier un produit
 */

// Détermine le mode (création vs édition)
$isEdit = !empty($edit);

if ($isEdit) {
    $product += [
        'id'          => 0,
        'title'       => '',
        'description' => '',
        'price'       => '',
        'image'       => null,
    ];
    $stock = $stock ?? 0; 
} else {
    $product = [
        'id'          => 0,
        'title'       => '',
        'description' => '',
        'price'       => '',
        'image'       => null,
    ];
    $stock = 0;
}

// Cache-buster pour l'image
$ts = time();

// URL du formulaire et textes selon le mode
$formAction = $isEdit
    ? '/edit/' . (int)$product['id']
    : '/sell';

$formTitle = $isEdit
    ? 'Modifier mon produit'
    : 'Vendre un Pokémon';

$btnText = $isEdit
    ? 'Enregistrer les modifications'
    : 'Publier';
?>

<h2 class="text-2xl font-bold mb-4"><?= htmlspecialchars($formTitle, ENT_QUOTES) ?></h2>

<?php if (!empty($_SESSION['errors'])): ?>
  <ul class="text-red-600 mb-4">
    <?php foreach ($_SESSION['errors'] as $e): ?>
      <li><?= htmlspecialchars($e, ENT_QUOTES) ?></li>
    <?php endforeach; unset($_SESSION['errors']); ?>
  </ul>
<?php endif; ?>

<form action="<?= htmlspecialchars($formAction, ENT_QUOTES) ?>"
      method="post"
      enctype="multipart/form-data"
      class="space-y-4 bg-white p-6 rounded shadow">

  <!-- Titre -->
  <label class="block">
    Titre
    <input type="text"
           name="title"
           required
           value="<?= htmlspecialchars($product['title'] ?? '', ENT_QUOTES) ?>"
           class="w-full border rounded p-2">
  </label>

  <!-- Description -->
  <label class="block">
    Description
    <textarea name="description"
              class="w-full border rounded p-2"
              rows="4"><?= htmlspecialchars($product['description'] ?? '', ENT_QUOTES) ?></textarea>
  </label>

  <!-- Prix -->
  <label class="block">
    Prix
    <input type="number"
           step="0.01"
           name="price"
           required
           value="<?= htmlspecialchars($product['price'] ?? '', ENT_QUOTES) ?>"
           class="w-full border rounded p-2">
  </label>

  <!-- Stock -->
  <label class="block">
    Stock
    <input type="number"
           name="stock"
           required
           min="0"
           value="<?= htmlspecialchars($stock, ENT_QUOTES) ?>"
           class="w-full border rounded p-2">
  </label>

  <!-- Image -->
  <label class="block">
    Image
    <input type="file"
           name="image"
           accept="image/jpeg,image/png"
           id="image-input"
           class="w-full border rounded p-2">
  </label>

  <?php if ($isEdit && !empty($product['image'])): ?>
    <div>
      <span class="text-sm text-gray-600">Image actuelle :</span>
      <img id="image-preview"
           src="/<?= htmlspecialchars($product['image'], ENT_QUOTES) ?>?t=<?= $ts ?>"
           alt="Aperçu"
           class="h-24 w-24 object-cover rounded mt-2">
    </div>
  <?php endif; ?>

  <button type="submit"
          class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded">
    <?= htmlspecialchars($btnText, ENT_QUOTES) ?>
  </button>
</form>

<script>
  const imgInput   = document.getElementById('image-input');
  const imgPreview = document.getElementById('image-preview');
  if (imgInput && imgPreview) {
    imgInput.addEventListener('change', () => {
      const file = imgInput.files[0];
      if (file) {
        imgPreview.src = URL.createObjectURL(file);
      }
    });
  }
</script>
