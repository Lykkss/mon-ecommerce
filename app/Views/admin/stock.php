<?php /* app/Views/admin/stock.php */ ?>

<h2 class="text-2xl font-bold mb-4">Gestion du stock</h2>

<?php if (!empty($_SESSION['success'])): ?>
  <div class="bg-green-100 p-4 rounded mb-4">
    <?= htmlspecialchars($_SESSION['success'], ENT_QUOTES) ?>
    <?php unset($_SESSION['success']); ?>
  </div>
<?php endif; ?>

<?php if (!empty($_SESSION['errors'])): ?>
  <div class="bg-red-100 p-4 rounded mb-4">
    <ul class="list-disc pl-5 space-y-1">
      <?php foreach ($_SESSION['errors'] as $e): ?>
        <li><?= htmlspecialchars($e, ENT_QUOTES) ?></li>
      <?php endforeach; ?>
    </ul>
    <?php unset($_SESSION['errors']); ?>
  </div>
<?php endif; ?>

<table class="w-full bg-white rounded shadow">
  <thead class="bg-gray-100">
    <tr>
      <th class="p-2 text-left">ID</th>
      <th class="p-2 text-left">Produit</th>
      <th class="p-2 text-left">Stock actuel</th>
      <th class="p-2 text-left">Nouvelle quantité</th>
      <th class="p-2 text-left">Statut</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($products as $p): 
      $pid = $p['id'];
      $currentQty = $stocks[$pid];
    ?>
      <tr class="border-t">
        <td class="p-2"><?= $pid ?></td>
        <td class="p-2">
          <?= htmlspecialchars(
                $p['name']   ?? 
                $p['title']  ?? 
                '<Sans Nom>',
                ENT_QUOTES
             ) ?>
        </td>
        <td class="p-2"><?= $currentQty ?></td>
        <td class="p-2">
          <form action="/admin/stock/update" method="post" class="flex items-center space-x-2">
            <input type="hidden" name="article_id" value="<?= $pid ?>">
            <input 
              type="number" 
              name="quantity" 
              min="0" 
              value="<?= $currentQty ?>" 
              class="w-20 border rounded p-1" 
              required
            >
            <button type="submit" class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
              OK
            </button>
          </form>
        </td>
        <td class="p-2">
          <?php if ($currentQty <= 0): ?>
            <span class="text-red-600 font-bold">Stock insuffisant</span>
          <?php else: ?>
            <span class="text-green-600 font-semibold">Stock suffisant</span>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
