<h2 class="text-2xl font-bold mb-4">Valider ma commande</h2>

<?php if (empty($items)): ?>
  <p>Votre panier est vide. <a href="/" class="text-blue-600 hover:underline">Voir nos produits</a>.</p>
<?php else: ?>
  <table class="mb-6 w-full bg-white rounded shadow">
    <thead class="bg-gray-100">
      <tr>
        <th class="p-2">Produit</th>
        <th class="p-2">Qté</th>
        <th class="p-2">Sous-total</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($items as $i): ?>
      <tr class="border-t">
        <td class="p-2"><?= htmlspecialchars($i['name']) ?></td>
        <td class="p-2"><?= $i['quantity'] ?></td>
        <td class="p-2"><?= number_format($i['subtotal'],2,',',' ') ?> €</td>
      </tr>
      <?php endforeach; ?>
      <tr class="font-bold bg-gray-50">
        <td colspan="2" class="p-2 text-right">Total :</td>
        <td class="p-2"><?= number_format(array_reduce($items, fn($s,$i)=>$s+$i['subtotal'],0),2,',',' ') ?> €</td>
      </tr>
    </tbody>
  </table>

  <form action="/commande/valider" method="post" class="bg-white p-6 rounded shadow">
    <label class="block mb-2">
      Votre e-mail :
      <input type="email" name="email" required
             class="w-full border rounded p-2 mt-1">
    </label>
    <button type="submit"
            class="mt-4 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl">
      Confirmer la commande
    </button>
  </form>
<?php endif; ?>
