<?php // app/Views/cart.php ?>

<h2 class="text-2xl font-bold mb-4">Mon Panier</h2>

<?php if (empty($items)): ?>
  <p>Votre panier est vide.</p>
<?php else: ?>
  <table class="w-full bg-white rounded-2xl shadow-md overflow-hidden">
    <thead class="bg-gray-100">
      <tr>
        <th class="p-2 text-left">Produit</th>
        <th class="p-2 text-center">Prix Unitaire</th>
        <th class="p-2 text-center">Quantité</th>
        <th class="p-2 text-right">Sous-total</th>
        <th class="p-2 text-center">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($items as $item): ?>
        <tr class="border-t">
          <td class="p-2">
            <?= htmlspecialchars($item['name']     ?? '—', ENT_QUOTES) ?>
          </td>
          <td class="p-2 text-center">
            <?= number_format((float)($item['price']    ?? 0), 2, ',', ' ') ?> €
          </td>
          <td class="p-2 text-center">
            <?= (int)    ($item['quantity'] ?? 0) ?>
          </td>
          <td class="p-2 text-right">
            <?= number_format((float)($item['subtotal'] ?? 0), 2, ',', ' ') ?> €
          </td>
          <td class="p-2 text-center space-x-2">
            <form action="/panier/supprimer" method="post" class="inline">
              <input type="hidden" name="product_id" value="<?= (int)($item['id'] ?? 0) ?>">
              <button class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded-xl">
                Supprimer
              </button>
            </form>
            <button onclick="location.href='/produit/<?= (int)($item['id'] ?? 0) ?>'"
                    class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-xl">
              Détails
            </button>
            <button onclick="location.href='/commande'"
                    class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded-xl">
              Commander
            </button>
          </td>
        </tr>
      <?php endforeach; ?>

      <tr class="bg-gray-50 font-bold">
        <td colspan="3" class="p-2 text-right">Total :</td>
        <td class="p-2 text-right">
          <?= number_format((float)($total ?? 0), 2, ',', ' ') ?> €
        </td>
        <td></td>
      </tr>
    </tbody>
  </table>
<?php endif; ?>
