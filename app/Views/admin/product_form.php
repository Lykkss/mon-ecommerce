<?php /* Formulaire création / édition */ ?>
<h2 class="text-2xl font-bold mb-4">
  <?= !empty($adminProductsEdit) ? 'Modifier un produit' : 'Créer un produit' ?>
</h2>
<form action="<?= !empty($adminProductsEdit) ? '/admin/products/edit/'.$product['id'] : '/admin/products/create' ?>" method="post" enctype="multipart/form-data" class="bg-white p-6 rounded shadow space-y-4">
  <label class="block">
    Nom
    <input type="text" name="name" required value="<?= htmlspecialchars($product['name'] ?? '',ENT_QUOTES) ?>" class="w-full border rounded p-2">
  </label>
  <label class="block">
    Description
    <textarea name="description" rows="4" class="w-full border rounded p-2"><?= htmlspecialchars($product['description'] ?? '',ENT_QUOTES) ?></textarea>
  </label>
  <label class="block">
    Prix
    <input type="number" step="0.01" name="price" required value="<?= htmlspecialchars($product['price'] ?? '',ENT_QUOTES) ?>" class="w-full border rounded p-2">
  </label>
  <label class="block">
    Stock
    <input type="number" name="stock" required value="<?= htmlspecialchars($stock ?? '',ENT_QUOTES) ?>" class="w-full border rounded p-2">
  </label>
  <label class="block">
    Image
    <input type="file" name="image" accept="image/*" class="w-full border rounded p-2">
    <?php if(!empty($product['image'])): ?>
      <img src="/<?= htmlspecialchars($product['image'],ENT_QUOTES) ?>" alt="" class="h-24 mt-2 object-cover">
    <?php endif; ?>
  </label>
  <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
    <?= !empty($adminProductsEdit) ? 'Enregistrer' : 'Créer' ?>
  </button>
</form>
