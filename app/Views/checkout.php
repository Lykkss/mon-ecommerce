<?php
/**
 * checkout.php
 *
 * Affiché depuis layout.php quand $checkout = true.
 */
if (empty($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

// Reconstruction du panier
$items   = [];
$total   = 0.0;
foreach ($_SESSION['cart'] ?? [] as $id => $qty) {
    if ($p = App\Models\Product::find((int)$id)) {
        $sub    = $qty * (float)$p['price'];
        $items[] = [
            'product'  => $p,
            'quantity' => $qty,
            'subtotal' => $sub,
        ];
        $total  += $sub;
    }
}

// Récupération du solde pour l’affichage
$user    = App\Models\User::findById((int)$_SESSION['user_id']);
$balance = $user['balance'] ?? 0.0;
?>

<h2 class="text-2xl font-bold mb-4">Validation de la commande</h2>

<?php if (!empty($_SESSION['errors'])): ?>
  <ul class="mb-4 text-red-600">
    <?php foreach ($_SESSION['errors'] as $e): ?>
      <li><?= htmlspecialchars($e, ENT_QUOTES) ?></li>
    <?php endforeach; unset($_SESSION['errors']); ?>
  </ul>
<?php endif; ?>

<!-- Récapitulatif du panier -->
<div class="bg-white rounded shadow mb-6 overflow-x-auto">
  <table class="w-full">
    <thead class="bg-gray-100">
      <tr>
        <th class="p-2 text-left">Produit</th>
        <th class="p-2 text-center">Qté</th>
        <th class="p-2 text-right">Sous-total</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($items as $it): ?>
        <tr class="border-t">
          <td class="p-2"><?= htmlspecialchars($it['product']['name'], ENT_QUOTES) ?></td>
          <td class="p-2 text-center"><?= (int)$it['quantity'] ?></td>
          <td class="p-2 text-right"><?= number_format($it['subtotal'], 2, ',', ' ') ?> €</td>
        </tr>
      <?php endforeach; ?>
      <tr class="bg-gray-50">
        <td colspan="2" class="p-2 text-right font-bold">Total :</td>
        <td class="p-2 text-right font-bold"><?= number_format($total, 2, ',', ' ') ?> €</td>
      </tr>
      <tr>
        <td colspan="3" class="p-2 text-right text-sm text-gray-600">
          Votre solde : <strong><?= number_format($balance, 2, ',', ' ') ?> €</strong>
        </td>
      </tr>
    </tbody>
  </table>
</div>

<!-- Formulaire de validation -->
<form action="/commande/valider"
      method="post"
      class="bg-white p-6 rounded shadow space-y-4">

  <!-- Adresse de livraison -->
  <label class="block">
    Adresse de livraison
    <input type="text" name="address" required
           value="<?= htmlspecialchars($_POST['address'] ?? '', ENT_QUOTES) ?>"
           class="w-full border rounded p-2">
  </label>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <label class="block">
      Ville
      <input type="text" name="city" required
             value="<?= htmlspecialchars($_POST['city'] ?? '', ENT_QUOTES) ?>"
             class="w-full border rounded p-2">
    </label>
    <label class="block">
      Code postal
      <input type="text" name="zip" required
             value="<?= htmlspecialchars($_POST['zip'] ?? '', ENT_QUOTES) ?>"
             class="w-full border rounded p-2">
    </label>
  </div>

  <label class="block">
    Email
    <input type="email" name="email" required
           value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES) ?>"
           class="w-full border rounded p-2">
  </label>

  <label class="block">
    Téléphone
    <input type="tel" name="phone" required
           value="<?= htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES) ?>"
           class="w-full border rounded p-2">
  </label>

  <!-- Méthode de paiement -->
  <fieldset class="border rounded p-4">
    <legend class="font-medium mb-2">Méthode de paiement</legend>

    <label class="inline-flex items-center space-x-2">
      <input type="radio" name="payment_method" value="balance" required
             <?= (($_POST['payment_method'] ?? '') === 'balance') ? 'checked' : '' ?>>
      <span>Payer avec mon solde (<?= number_format($balance, 2, ',', ' ') ?> €)</span>
    </label>

    <label class="inline-flex items-center space-x-2 ml-6">
      <input type="radio" name="payment_method" value="credit_card"
             <?= (($_POST['payment_method'] ?? '') === 'credit_card') ? 'checked' : '' ?>>
      <span>Carte de crédit</span>
    </label>

    <label class="inline-flex items-center space-x-2 ml-6">
      <input type="radio" name="payment_method" value="paypal"
             <?= (($_POST['payment_method'] ?? '') === 'paypal') ? 'checked' : '' ?>>
      <span>PayPal</span>
    </label>
  </fieldset>

  <!-- Champs Carte de crédit -->
  <div id="cc-fields"
       class="<?= (($_POST['payment_method'] ?? '') === 'credit_card') ? '' : 'hidden' ?>">
    <label class="block">
      Numéro de carte
      <input type="text" name="cc_number" pattern="\d{13,19}"
             placeholder="0000 0000 0000 0000"
             class="w-full border rounded p-2"
             value="<?= htmlspecialchars($_POST['cc_number'] ?? '', ENT_QUOTES) ?>">
    </label>
    <div class="grid grid-cols-2 gap-4">
      <label class="block">
        Expiration (MM/AA)
        <input type="text" name="cc_expiry" pattern="(0[1-9]|1[0-2])\/\d{2}"
               placeholder="MM/AA"
               class="w-full border rounded p-2"
               value="<?= htmlspecialchars($_POST['cc_expiry'] ?? '', ENT_QUOTES) ?>">
      </label>
      <label class="block">
        CVV
        <input type="text" name="cc_cvv" pattern="\d{3,4}" placeholder="123"
               class="w-full border rounded p-2"
               value="<?= htmlspecialchars($_POST['cc_cvv'] ?? '', ENT_QUOTES) ?>">
      </label>
    </div>
  </div>

  <!-- Champs PayPal -->
  <div id="pp-fields"
       class="<?= (($_POST['payment_method'] ?? '') === 'paypal') ? '' : 'hidden' ?>">
    <label class="block">
      E-mail PayPal
      <input type="email" name="pp_email"
             placeholder="votre@paypal.com"
             class="w-full border rounded p-2"
             value="<?= htmlspecialchars($_POST['pp_email'] ?? '', ENT_QUOTES) ?>">
    </label>
  </div>

  <!-- Conditions générales -->
  <label class="block">
    <input type="checkbox" name="terms" required>
    J’accepte les 
    <a href="/terms" class="text-blue-600 hover:underline">
      conditions générales de vente
    </a>.
  </label>

  <div class="flex space-x-2">
    <button type="submit"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
      Confirmer la commande
    </button>
    <button type="button"
            class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded"
            onclick="window.location.href='/panier'">
      Retour au panier
    </button>
  </div>
</form>

<script>
  function togglePaymentFields() {
    const sel = document.querySelector('input[name="payment_method"]:checked')?.value;
    document.getElementById('cc-fields').classList.toggle('hidden', sel !== 'credit_card');
    document.getElementById('pp-fields').classList.toggle('hidden', sel !== 'paypal');
  }
  document.querySelectorAll('input[name="payment_method"]')
          .forEach(r => r.addEventListener('change', togglePaymentFields));
  document.addEventListener('DOMContentLoaded', togglePaymentFields);
</script>
