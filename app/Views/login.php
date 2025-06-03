<!-- app/Views/login.php -->
<h2 class="text-2xl font-bold mb-4">Connexion</h2>

<?php if (!empty($_SESSION['errors'])): ?>
  <ul class="mb-4 text-red-600">
    <?php foreach($_SESSION['errors'] as $e): ?>
      <li><?= htmlspecialchars($e) ?></li>
    <?php endforeach;
    unset($_SESSION['errors']); ?>
  </ul>
<?php endif; ?>

<form action="/login" method="post" class="space-y-4 bg-white p-6 rounded shadow">
  <label class="block">
    Email
    <input type="email" name="email" required class="w-full border rounded p-2">
  </label>
  <label class="block">
    Mot de passe
    <input type="password" name="password" required class="w-full border rounded p-2">
  </label>
  <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
    Se connecter
  </button>
</form>

<p class="mt-4">
  Pas encore de compte ? 
  <a href="/register" class="text-blue-600 hover:underline">Inscrivez-vous</a>.
</p>
