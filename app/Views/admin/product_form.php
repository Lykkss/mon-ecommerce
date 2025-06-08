<?php /* app/Views/admin/product_form.php */ ?>

<h2 class="text-2xl font-bold mb-4">
  <?= !empty($adminProductsEdit) ? 'Modifier un produit' : 'Créer un produit' ?>
</h2>

<form action="<?= !empty($adminProductsEdit)
                 ? '/admin/products/edit/'.$product['id']
                 : '/admin/products/create' ?>"
      method="post"
      enctype="multipart/form-data"
      class="bg-white p-6 rounded shadow space-y-4">

  <!-- Nom -->
  <label class="block">
    Nom
    <input type="text"
           name="name"
           required
           value="<?= htmlspecialchars($product['name'] ?? '', ENT_QUOTES) ?>"
           class="w-full border rounded p-2">
  </label>

  <!-- Description -->
  <label class="block">
    Description
    <textarea name="description"
              rows="4"
              class="w-full border rounded p-2"><?= htmlspecialchars($product['description'] ?? '', ENT_QUOTES) ?></textarea>
  </label>

  <!-- Prix -->
  <label class="block">
    Prix (€)
    <input type="number"
           name="price"
           step="0.01"
           required
           value="<?= htmlspecialchars($product['price'] ?? '', ENT_QUOTES) ?>"
           class="w-full border rounded p-2">
  </label>

  <!-- Stock -->
  <label class="block">
    Stock
    <input type="number"
           name="stock"
           min="0"
           required
           value="<?= htmlspecialchars($stock ?? 0, ENT_QUOTES) ?>"
           class="w-full border rounded p-2">
  </label>

  <!-- Image -->
  <label class="block">
    Image
    <input type="file"
           name="image"
           accept="image/jpeg,image/png"
           class="w-full border rounded p-2">
    <?php if (!empty($product['image'])): ?>
      <img src="/<?= htmlspecialchars($product['image'], ENT_QUOTES) ?>"
           alt="Aperçu de l'image"
           class="h-24 w-24 object-cover rounded mt-2">
    <?php endif; ?>
  </label>

  <!-- Sélecteur de catégorie -->
  <label class="block">
    Catégorie
    <select name="category_id" class="w-full border rounded p-2">
      <option value="">— Aucune —</option>
      <?php foreach($categories as $cat): ?>
        <option value="<?= $cat['id'] ?>"
          <?= (int)($product['category_id'] ?? 0) === (int)$cat['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($cat['name'], ENT_QUOTES) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </label>

  <button type="submit"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
    <?= !empty($adminProductsEdit) ? 'Enregistrer' : 'Créer' ?>
  </button>
</form>
