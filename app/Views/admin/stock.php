<h2 class="text-2xl font-bold mb-4">Gestion du stock</h2>

<?php if(!empty($_SESSION['success'])): ?>
  <div class="bg-green-100 p-4 rounded mb-4"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<?php if(!empty($_SESSION['errors'])): ?>
  <div class="bg-red-100 p-4 rounded mb-4">
    <ul>
      <?php foreach($_SESSION['errors'] as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
    </ul>
    <?php unset($_SESSION['errors']); ?>
  </div>
<?php endif; ?>

<table class="w-full bg-white rounded shadow">
  <thead class="bg-gray-100">
    <tr>
      <th class="p-2">ID</th>
      <th class="p-2">Produit</th>
      <th class="p-2">Stock</th>
      <th class="p-2">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($products as $p):
      $qty = $stocks[$p['id']];
    ?>
    <tr class="border-t">
      <td class="p-2"><?= $p['id'] ?></td>
      <td class="p-2"><?= htmlspecialchars($p['title'] ?? $p['name'], ENT_QUOTES) ?></td>
      <td class="p-2">
        <form action="/admin/stock/update" method="post" class="flex items-center space-x-2">
          <input type="hidden" name="article_id" value="<?= $p['id'] ?>">
          <input 
            type="number" 
            name="quantity" 
            min="0" 
            value="<?= $qty ?>" 
            class="w-20 border rounded p-1"
          >
          <button 
            type="submit" 
            class="px-2 py-1 bg-blue-600 text-white rounded"
          >OK</button>
        </form>
      </td>
      <td class="p-2">
        <?php if ($qty <= 0): ?>
          <span class="text-red-600 font-bold">Stock insuffisant</span>
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
