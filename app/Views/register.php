<h2 class="text-2xl font-bold mb-4">Inscription</h2>
<?php if (!empty($_SESSION['errors'])): ?>
  <ul class="mb-4 text-red-600">
    <?php foreach($_SESSION['errors'] as $e): ?>
      <li><?= htmlspecialchars($e) ?></li>
    <?php endforeach;
    unset($_SESSION['errors']); ?>
  </ul>
<?php endif; ?>
<form action="/register" method="post" class="space-y-4 bg-white p-6 rounded shadow">
  <label class="block">
    Username
    <input type="text" name="username" required class="w-full border rounded p-2">
  </label>
  <label class="block">
    Email
    <input type="email" name="email" required class="w-full border rounded p-2">
  </label>
  <label class="block">
    Mot de passe
    <input type="password" name="password" required class="w-full border rounded p-2">
  </label>
  <label class="block">
    Confirmer le mot de passe
    <input type="password" name="password2" required class="w-full border rounded p-2">
  </label>
  <label class="block">
    Nom complet (facultatif)
    <input type="text" name="fullname" class="w-full border rounded p-2">
  </label>
  <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
    S’inscrire
  </button>
</form>
<p class="mt-4">
  Déjà un compte ? <a href="/login" class="text-blue-600 hover:underline">Connectez-vous</a>.
</p>
