<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: Arial, sans-serif; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ddd; padding: 8px; }
    th { background: #f2f2f2; }
  </style>
</head>
<body>
  <h1>Facture #<?= $invoice['id'] ?></h1>
  <p>Date : <?= $invoice['created_at'] ?></p>
  <p>Client : <?= htmlspecialchars($_SESSION['user_id'], ENT_QUOTES) ?></p>
  <p>Adresse : <?= htmlspecialchars($invoice['billing_address'], ENT_QUOTES) ?>, 
     <?= htmlspecialchars($invoice['billing_city'], ENT_QUOTES) ?> <?= htmlspecialchars($invoice['billing_zip'], ENT_QUOTES) ?></p>
  <table class="mt-4">
    <thead>
      <tr>
        <th>Produit</th><th>Qté</th><th>PU</th><th>Total</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($items as $it): ?>
      <tr>
        <td><?= htmlspecialchars($it['product_name'], ENT_QUOTES) ?></td>
        <td><?= $it['quantity'] ?></td>
        <td><?= number_format($it['unit_price'],2,',',' ')?> €</td>
        <td><?= number_format($it['unit_price']*$it['quantity'],2,',',' ')?> €</td>
      </tr>
      <?php endforeach; ?>
      <tr>
        <td colspan="3"><strong>Total</strong></td>
        <td><strong><?= number_format($invoice['total_amount'],2,',',' ')?> €</strong></td>
      </tr>
    </tbody>
  </table>
</body>
</html>
