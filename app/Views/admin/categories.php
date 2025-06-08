<?php /* app/Views/admin/categories.php */ ?>

<h2 class="text-2xl font-bold mb-4">Gérer les catégories</h2>

<?php if (!empty($_SESSION['success'])): ?>
  <div class="bg-green-100 p-3 rounded mb-4">
    <?= htmlspecialchars($_SESSION['success'], ENT_QUOTES) ?>
    <?php unset($_SESSION['success']); ?>
  </div>
<?php endif; ?>

<?php if (!empty($_SESSION['errors'])): ?>
  <div class="bg-red-100 p-3 rounded mb-4">
    <ul class="list-disc pl-5">
      <?php foreach($_SESSION['errors'] as $e): ?>
        <li><?= htmlspecialchars($e, ENT_QUOTES) ?></li>
      <?php endforeach; unset($_SESSION['errors']); ?>
    </ul>
  </div>
<?php endif; ?>

<!-- Formulaire création -->
<form action="/admin/categories/create" method="post" class="mb-6 flex space-x-2">
  <input type="text" name="name" required
         placeholder="Nouvelle catégorie"
         class="flex-1 border rounded p-2">
  <button type="submit"
          class="px-4 py-2 bg-blue-600 text-white rounded">
    Ajouter
  </button>
</form>

<!-- Liste -->
<table class="w-full bg-white rounded shadow">
  <thead class="bg-gray-100">
    <tr>
      <th class="p-2 text-left">ID</th>
      <th class="p-2 text-left">Nom</th>
      <th class="p-2">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($categories as $cat): ?>
    <tr class="border-t">
      <td class="p-2"><?= $cat['id'] ?></td>
      <td class="p-2"><?= htmlspecialchars($cat['name'], ENT_QUOTES) ?></td>
      <td class="p-2">
        <form action="/admin/categories/delete" method="post" class="inline">
          <input type="hidden" name="id" value="<?= $cat['id'] ?>">
          <button onclick="return confirm('Supprimer ?')"
                  class="text-red-600">Supprimer</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
