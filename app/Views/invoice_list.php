<h2 class="text-2xl font-bold">Mes factures</h2>
<table class="w-full mt-4">
  <thead>
    <tr class="bg-gray-100">
      <th class="p-2">#</th>
      <th class="p-2">Date</th>
      <th class="p-2">Montant</th>
      <th class="p-2">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($invoices as $inv): ?>
      <tr class="border-t">
        <td class="p-2"><?= htmlspecialchars($inv['id'], ENT_QUOTES) ?></td>
        <td class="p-2"><?= htmlspecialchars($inv['created_at'], ENT_QUOTES) ?></td>
        <td class="p-2 font-bold"><?= number_format($inv['total_amount'],2,',',' ') ?> €</td>
        <td class="p-2">
          <a href="/compte/facture/<?= $inv['id'] ?>" class="mr-2">Voir</a>
          <a href="/compte/facture/<?= $inv['id'] ?>/pdf" target="_blank">PDF</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
