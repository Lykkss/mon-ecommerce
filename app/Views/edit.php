<h2 class="text-2xl font-bold mb-4">Modifier un Pokémon</h2>

<form action="/edit/<?= $prod['id'] ?>"
      method="post"
      enctype="multipart/form-data"
      class="space-y-4 bg-white p-6 rounded shadow">

  <label class="block">
    Titre
    <input type="text"
           name="title"
           value="<?= htmlspecialchars($prod['title'], ENT_QUOTES) ?>"
           required
           class="w-full border p-2">
  </label>

  <label class="block">
    Description
    <textarea name="description"
              class="w-full border p-2"
              rows="4"><?= htmlspecialchars($prod['description'], ENT_QUOTES) ?></textarea>
  </label>

  <label class="block">
    Prix
    <input type="number"
           step="0.01"
           name="price"
           value="<?= htmlspecialchars($prod['price'], ENT_QUOTES) ?>"
           required
           class="border p-2">
  </label>

  <label class="block">
    Stock
    <input type="number"
           name="stock"
           value="<?= htmlspecialchars($stock, ENT_QUOTES) ?>"
           required
           class="border p-2">
  </label>

  <div class="block">
    <span class="font-medium">Image actuelle</span><br>
    <?php if (!empty($prod['image'])): ?>
      <img src="/<?= htmlspecialchars($prod['image'], ENT_QUOTES) ?>"
           alt="<?= htmlspecialchars($prod['title'], ENT_QUOTES) ?>"
           class="h-24 w-auto object-cover mb-2">
    <?php else: ?>
      <p class="text-gray-500">Pas d’image</p>
    <?php endif; ?>

    <input type="file"
           name="image"
           accept="image/jpeg,image/png"
           class="mt-1 border p-2">
  </div>

  <div class="flex space-x-2 items-center">
    <button type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded">
      Enregistrer
    </button>

    <form action="/delete/<?= $prod['id'] ?>" method="post">
      <button type="submit"
              class="px-4 py-2 bg-red-600 text-white rounded">
        Supprimer
      </button>
    </form>
  </div>
</form>
