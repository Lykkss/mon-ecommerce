<h2 class="text-2xl font-bold mb-4">Vendre un Pokémon</h2>

<?php if (!empty($_SESSION['errors'])): ?>
  <ul class="text-red-600 mb-4">
    <?php foreach ($_SESSION['errors'] as $e): ?>
      <li><?= htmlspecialchars($e, ENT_QUOTES) ?></li>
    <?php endforeach; unset($_SESSION['errors']); ?>
  </ul>
<?php endif; ?>

<form action="/sell"
      method="post"
      enctype="multipart/form-data"
      class="space-y-4 bg-white p-6 rounded shadow">

  <label class="block">
    Titre
    <input type="text"
           name="title"
           required
           class="w-full border p-2">
  </label>

  <label class="block">
    Description
    <textarea name="description"
              class="w-full border p-2"
              rows="4"></textarea>
  </label>

  <label class="block">
    Prix
    <input type="number"
           step="0.01"
           name="price"
           required
           class="border p-2">
  </label>

  <label class="block">
    Stock
    <input type="number"
           name="stock"
           required
           class="border p-2">
  </label>

  <label class="block">
    Image
    <input type="file"
           name="image"
           accept="image/jpeg,image/png"
           class="border p-2">
  </label>

  <button type="submit"
          class="px-4 py-2 bg-green-600 text-white rounded">
    Publier
  </button>
</form>
