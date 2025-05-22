<h2 class="text-2xl font-bold mb-4">Mon Panier</h2>
<?php if (empty($items)): ?>
  <p>Votre panier est vide.</p>
<?php else: ?>
  <table class="w-full bg-white rounded-2xl shadow-md overflow-hidden">
    <thead class="bg-gray-100">
      <tr>
        <th class="p-2">Produit</th>
        <th class="p-2">Prix unitaire</th>
        <th class="p-2">Quantité</th>
        <th class="p-2">Sous-total</th>
        <th class="p-2">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($items as $item): ?>
      <tr class="border-t">
        <td class="p-2"><?= htmlspecialchars($item['name']) ?></td>
        <td class="p-2"><?= number_format($item['price'],2,',',' ') ?> €</td>
        <td class="p-2"><?= $item['quantity'] ?></td>
        <td class="p-2"><?= number_format($item['subtotal'],2,',',' ') ?> €</td>
        <td class="p-2">
          <form action="/panier/supprimer" method="post">
            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
            <button type="submit"
                    class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded-xl">
              Supprimer
            </button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      <tr class="font-bold">
        <td colspan="3" class="p-2 text-right">Total :</td>
        <td class="p-2"><?= number_format($total,2,',',' ') ?> €</td>
        <td></td>
      </tr>
    </tbody>
  </table>
<?php endif; ?>
