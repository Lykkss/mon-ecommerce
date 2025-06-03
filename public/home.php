<?php // app/Views/home.php ?>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
  <?php foreach($products as $p): ?>
    <div class="bg-white rounded-2xl shadow-md overflow-hidden flex flex-col hover:shadow-lg transition">

      <!-- Partie cliquable : image + infos -->
      <a href="/produit/<?= $p['id'] ?>" class="block flex-1">
        <?php if(!empty($p['image'])): ?>
          <img src="/assets/<?= htmlspecialchars($p['image'], ENT_QUOTES) ?>"
               alt="<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>"
               class="h-48 w-full object-cover">
        <?php else: ?>
          <div class="h-48 w-full bg-gray-200 flex items-center justify-center">
            <span class="text-gray-400">Pas d’image</span>
          </div>
        <?php endif; ?>

        <div class="p-4 flex flex-col">
          <h2 class="text-lg font-semibold mb-2">
            <?= htmlspecialchars($p['name'], ENT_QUOTES) ?>
          </h2>
          <p class="text-gray-600 flex-1">
            <?= nl2br(htmlspecialchars($p['description'], ENT_QUOTES)) ?>
          </p>
        </div>
      </a>

      <!-- Zone d’action : prix + ajout au panier -->
      <div class="p-4 mt-auto flex items-center justify-between border-t bg-white">
        <span class="text-xl font-bold">
          <?= number_format($p['price'], 2, ',', ' ') ?> €
        </span>
        <form action="/panier/ajouter" method="post" class="flex items-center space-x-2">
          <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
          <input type="number" name="quantity" value="1" min="1" max="<?= $p['stock'] ?? '' ?>"
                 class="w-16 border rounded text-center">
          <button type="submit"
                  class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-xl">
            Ajouter
          </button>
        </form>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<div class="mt-6">
  <a href="/produits" class="text-blue-600 hover:underline">
    Voir tous les produits
  </a>
</div>
<script>
  // Si l'utilisateur n'est pas connecté, on cache le bouton "Ajouter au panier"
  const addToCartButtons = document.querySelectorAll('button[type="submit"]');
  addToCartButtons.forEach(button => {
    button.addEventListener('click', function(event) {
      if (!<?= json_encode(isset($_SESSION['user'])) ?>) {
        event.preventDefault();
        alert('Vous devez être connecté pour ajouter un produit au panier.');
      }
    });
  });
</script>

