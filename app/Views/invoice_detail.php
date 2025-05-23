<h2 class="text-2xl font-bold mb-4">Facture #<?= $invoice['id'] ?></h2>
<p>Émise le <?= htmlspecialchars($invoice['created_at'], ENT_QUOTES) ?></p>
<p>Adresse : <?= htmlspecialchars($invoice['billing_address'], ENT_QUOTES) ?>, 
   <?= htmlspecialchars($invoice['billing_city'], ENT_QUOTES) ?> 
   <?= htmlspecialchars($invoice['billing_zip'], ENT_QUOTES) ?></p>

<table class="w-full mt-4">
  <thead class="bg-gray-100">
    <tr>
      <th class="p-2">Produit</th>
      <th class="p-2 text-center">Qté</th>
      <th class="p-2 text-right">Prix unitaire</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($items as $it): ?>
      <tr class="border-t">
        <td class="p-2"><?= htmlspecialchars($it['product_name'], ENT_QUOTES) ?></td>
        <td class="p-2 text-center"><?= (int)$it['quantity'] ?></td>
        <td class="p-2 text-right"><?= number_format($it['unit_price'],2,',',' ')?> €</td>
      </tr>
    <?php endforeach; ?>
      <tr class="bg-gray-50">
        <td colspan="2" class="p-2 text-right font-bold">Total :</td>
        <td class="p-2 text-right font-bold">
          <?= number_format($invoice['total_amount'],2,',',' ')?> €
        </td>
      </tr>
  </tbody>
</table>

<a href="/compte/factures" class="mt-4 inline-block text-blue-600 hover:underline">← Retour aux factures</a>
