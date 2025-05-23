<?php if (!empty($_SESSION['errors'])): ?>
  <ul class="mb-4 text-red-600">
    <?php foreach ($_SESSION['errors'] as $e): ?>
      <li><?= htmlspecialchars($e, ENT_QUOTES) ?></li>
    <?php endforeach; unset($_SESSION['errors']); ?>
  </ul>
<?php endif; ?>

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
    </tbody>
  </table>
</div>

<form action="/commande/valider" method="post" class="bg-white p-6 rounded shadow space-y-4">
  <!-- Adresse de livraison -->
  <label class="block">
    Adresse de livraison
    <input type="text" name="address" required class="w-full border rounded p-2"
           value="<?= htmlspecialchars($_POST['address'] ?? '', ENT_QUOTES) ?>">
  </label>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <label class="block">
      Ville
      <input type="text" name="city" required class="w-full border rounded p-2"
             value="<?= htmlspecialchars($_POST['city'] ?? '', ENT_QUOTES) ?>">
    </label>
    <label class="block">
      Code postal
      <input type="text" name="zip" required class="w-full border rounded p-2"
             value="<?= htmlspecialchars($_POST['zip'] ?? '', ENT_QUOTES) ?>">
    </label>
  </div>

  <label class="block">
    Email
    <input type="email" name="email" required class="w-full border rounded p-2"
           value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES) ?>">
  </label>

  <label class="block">
    Téléphone
    <input type="tel" name="phone" required class="w-full border rounded p-2"
           value="<?= htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES) ?>">
  </label>

  <!-- Méthode de paiement : radio buttons -->
  <fieldset class="border rounded p-4">
    <legend class="font-medium mb-2">Méthode de paiement</legend>
    <label class="inline-flex items-center space-x-2">
      <input type="radio" name="payment_method" value="credit_card" required
             <?= (($_POST['payment_method'] ?? '') === 'credit_card') ? 'checked' : '' ?>>
      <span>Carte de crédit</span>
    </label>
    <label class="inline-flex items-center space-x-2 ml-6">
      <input type="radio" name="payment_method" value="paypal"
             <?= (($_POST['payment_method'] ?? '') === 'paypal') ? 'checked' : '' ?>>
      <span>PayPal</span>
    </label>
  </fieldset>

  <!-- Champs Carte de crédit (radio = credit_card) -->
  <div id="cc-fields" class="<?= (($_POST['payment_method'] ?? '') === 'credit_card') ? '' : 'hidden' ?>">
    <label class="block">
      Numéro de carte
      <input type="text" name="cc_number" pattern="[0-9]{13,19}" placeholder="0000 0000 0000 0000"
             class="w-full border rounded p-2"
             value="<?= htmlspecialchars($_POST['cc_number'] ?? '', ENT_QUOTES) ?>">
    </label>
    <div class="grid grid-cols-2 gap-4">
      <label class="block">
        Expiration (MM/AA)
        <input type="text" name="cc_expiry" pattern="(0[1-9]|1[0-2])\/\d{2}" placeholder="MM/AA"
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

  <!-- Champs PayPal (radio = paypal) -->
  <div id="pp-fields" class="<?= (($_POST['payment_method'] ?? '') === 'paypal') ? '' : 'hidden' ?>">
    <label class="block">
      E-mail PayPal
      <input type="email" name="pp_email" class="w-full border rounded p-2"
             placeholder="votre@paypal.com"
             value="<?= htmlspecialchars($_POST['pp_email'] ?? '', ENT_QUOTES) ?>">
    </label>
  </div>

  <label class="block">
    <input type="checkbox" name="terms" required>
    J’accepte les 
    <a href="/terms" class="text-blue-600 hover:underline">conditions générales de vente</a>.
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
  const pmRadios = document.querySelectorAll('input[name="payment_method"]');
  const ccFields   = document.getElementById('cc-fields');
  const ppFields   = document.getElementById('pp-fields');

  function togglePaymentFields() {
    const sel = document.querySelector('input[name="payment_method"]:checked')?.value;
    if (sel === 'credit_card') {
      ccFields.classList.remove('hidden');
      ccFields.querySelectorAll('input').forEach(i => i.required = true);
      ppFields.classList.add('hidden');
      ppFields.querySelectorAll('input').forEach(i => i.required = false);
    } else if (sel === 'paypal') {
      ppFields.classList.remove('hidden');
      ppFields.querySelectorAll('input').forEach(i => i.required = true);
      ccFields.classList.add('hidden');
      ccFields.querySelectorAll('input').forEach(i => i.required = false);
    } else {
      ccFields.classList.add('hidden');
      ccFields.querySelectorAll('input').forEach(i => i.required = false);
      ppFields.classList.add('hidden');
      ppFields.querySelectorAll('input').forEach(i => i.required = false);
    }
  }

  pmRadios.forEach(r => r.addEventListener('change', togglePaymentFields));
  document.addEventListener('DOMContentLoaded', togglePaymentFields);
</script>
