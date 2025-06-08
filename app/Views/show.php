<?php
// app/Views/show.php

/** @var array $product */
use App\Models\User;
use App\Models\Comment;
use App\Models\Favorite;

// Récupère l’auteur du produit
$seller = User::findById((int)$product['author_id']);

// Récupère les commentaires pour ce produit
$comments = Comment::findByProduct((int)$product['id']);

// Vérifie si l’utilisateur a mis en favori
$isFav = false;
if (!empty($_SESSION['user_id'])) {
    $isFav = Favorite::isFavorited((int)$_SESSION['user_id'], (int)$product['id']);
}
?>

<div class="bg-white p-6 rounded shadow mb-6">
  <h1 class="text-3xl font-bold mb-4">
    <?= htmlspecialchars($product['name'], ENT_QUOTES) ?>
  </h1>

  <?php if (!empty($product['image'])): ?>
    <img src="/<?= htmlspecialchars($product['image'], ENT_QUOTES) ?>"
         alt="<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>"
         class="w-full h-64 object-cover rounded mb-4">
  <?php endif; ?>

  <p class="text-gray-700 mb-4">
    <?= nl2br(htmlspecialchars($product['description'], ENT_QUOTES)) ?>
  </p>

  <p class="text-xl font-semibold mb-2">
    <?= number_format($product['price'], 2, ',', ' ') ?> €
  </p>

  <!-- Affichage du vendeur -->
  <?php if ($seller): ?>
    <p class="text-sm text-gray-600 mb-4">
      Publié par : 
      <strong>
        <?= htmlspecialchars($seller['fullname'] ?? $seller['username'], ENT_QUOTES) ?>
      </strong>
    </p>
  <?php endif; ?>

  <!-- Bouton Favoris -->
  <?php if (!empty($_SESSION['user_id'])): ?>
    <form action="/produit/<?= (int)$product['id'] ?>/favorite"
          method="post"
          class="mb-4">
      <button type="submit"
              class="px-4 py-2 <?= $isFav ? 'bg-red-500' : 'bg-gray-300' ?> text-white rounded">
        <?= $isFav ? 'Retirer des favoris' : 'Ajouter aux favoris' ?>
      </button>
    </form>
  <?php endif; ?>

  <!-- Bouton d'ajout au panier -->
  <form action="/panier/ajouter" method="post" class="inline-block">
    <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
    <button type="submit"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
      Ajouter au panier
    </button>
  </form>
</div>

<!-- Section Commentaires -->
<section id="comments" class="bg-white p-6 rounded shadow">
  <h2 class="text-2xl font-bold mb-4">Commentaires</h2>

  <!-- Formulaire d’ajout de commentaire -->
  <?php if (!empty($_SESSION['user_id'])): ?>
    <form action="/produit/<?= (int)$product['id'] ?>/comment"
          method="post"
          class="mb-6">
      <textarea name="content" required
                class="w-full border rounded p-2"
                rows="3"
                placeholder="Votre commentaire…"></textarea>
      <button type="submit"
              class="mt-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded">
        Poster
      </button>
    </form>
  <?php else: ?>
    <p class="mb-4 text-gray-600">
      <a href="/login" class="text-blue-600 hover:underline">Connectez-vous</a>
      pour laisser un commentaire.
    </p>
  <?php endif; ?>

  <!-- Liste des commentaires -->
  <?php if (empty($comments)): ?>
    <p class="text-gray-600">Aucun commentaire pour le moment.</p>
  <?php else: ?>
    <ul class="space-y-4">
      <?php foreach ($comments as $c): ?>
        <li class="border-t pt-4">
          <p class="text-gray-800"><?= nl2br(htmlspecialchars($c['content'], ENT_QUOTES)) ?></p>
          <p class="mt-1 text-sm text-gray-500">
            Par <?= htmlspecialchars($c['fullname'], ENT_QUOTES) ?>
            le <?= htmlspecialchars($c['created_at'], ENT_QUOTES) ?>
          </p>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</section>
