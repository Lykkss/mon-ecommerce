<?php
// app/Views/terms.php
?>
<div class="bg-white p-6 rounded shadow">
  <h2 class="text-3xl font-bold mb-4">Conditions Générales de Vente</h2>

  <section class="mb-6">
    <h3 class="text-xl font-semibold mb-2">1. Présentation de la marketplace</h3>
    <p>
      Bienvenue sur PokéCommerce. Les présentes Conditions Générales de Vente (CGV) régissent les relations contractuelles 
      entre la plateforme PokéCommerce et tout utilisateur souhaitant acheter ou vendre des produits sur notre site.
    </p>
  </section>

  <section class="mb-6">
    <h3 class="text-xl font-semibold mb-2">2. Inscription et compte</h3>
    <p>
      Pour acheter ou mettre en vente un produit, vous devez créer un compte utilisateur. Vous vous engagez à fournir des informations 
      exactes et à jour lors de votre inscription. Vous êtes responsable de la confidentialité de votre mot de passe et de toutes les 
      activités effectuées sous votre compte.
    </p>
  </section>

  <section class="mb-6">
    <h3 class="text-xl font-semibold mb-2">3. Mise en vente de produits</h3>
    <p>
      Toute personne disposant d’un compte peut publier un ou plusieurs produits à vendre. Chaque annonce doit comporter :
    </p>
    <ul class="list-disc list-inside ml-4 mb-2">
      <li>Un titre précis</li>
      <li>Une description détaillée</li>
      <li>Un prix de vente en euros (€)</li>
      <li>Un stock disponible (quantité)</li>
      <li>Une illustration (photo) du produit</li>
    </ul>
    <p>
      Vous garantissez que vous êtes le propriétaire légitime du produit mis en vente et que celui-ci est conforme à la réglementation en vigueur. 
      PokéCommerce ne saurait être tenu responsable en cas de litige entre acheteurs et vendeurs.
    </p>
  </section>

  <section class="mb-6">
    <h3 class="text-xl font-semibold mb-2">4. Achat et paiement</h3>
    <p>
      Lorsque vous achetez un produit, vous vous engagez à :
    </p>
    <ul class="list-disc list-inside ml-4 mb-2">
      <li>Payer le prix indiqué, frais de livraison inclus le cas échéant</li>
      <li>Fournir une adresse de livraison valide</li>
      <li>Choisir un mode de paiement accepté (carte bancaire, PayPal, etc.)</li>
    </ul>
    <p>
      Les paiements sont sécurisés via les plateformes partenaires. Aucun moyen de paiement autre que ceux proposés n’est accepté. 
      Les produits restent la propriété du vendeur jusqu’à la réception du paiement intégral.
    </p>
  </section>

  <section class="mb-6">
    <h3 class="text-xl font-semibold mb-2">5. Livraison et retours</h3>
    <p>
      Le vendeur est responsable de l’expédition du produit dans les 3 à 5 jours ouvrés suivant la confirmation du paiement. 
      Les délais et frais de livraison sont indiqués sur chaque fiche produit.
    </p>
    <p>
      En cas d’anomalie (produit endommagé, erreur de quantité, etc.), l’acheteur dispose d’un délai de 14 jours après réception pour contacter 
      le vendeur et convenir d’un retour ou d’un échange. PokéCommerce sert d’intermédiaire pour faciliter la résolution des litiges.
    </p>
  </section>

  <section class="mb-6">
    <h3 class="text-xl font-semibold mb-2">6. Garantie et responsabilité</h3>
    <p>
      Sauf mention contraire, les produits vendus sur PokéCommerce sont proposés « en l’état ». Le vendeur garantit qu’il n’y a pas de vice caché 
      rendant le produit impropre à son usage. L’acheteur dispose d’un délai de 30 jours pour signaler tout problème. Au-delà, aucun retour ne sera 
      accepté, sauf recours légal.
    </p>
    <p>
      PokéCommerce ne peut être tenu responsable des dommages indirects liés à l’utilisation de la plateforme ou à l’achat/vente de produits.
    </p>
  </section>

  <section class="mb-6">
    <h3 class="text-xl font-semibold mb-2">7. Protection des données personnelles</h3>
    <p>
      Les informations collectées lors de l’inscription et de la validation de commande sont utilisées uniquement pour le traitement des commandes 
      et la gestion du compte utilisateur. Conformément à la loi Informatique et Libertés, vous disposez d’un droit d’accès, de rectification et 
      de suppression de vos données personnelles. Pour exercer ce droit, contactez-nous via le formulaire de contact.
    </p>
  </section>

  <section class="mb-6">
    <h3 class="text-xl font-semibold mb-2">8. Propriété intellectuelle</h3>
    <p>
      Tous les contenus présents sur PokéCommerce (textes, logos, images, etc.) sont la propriété exclusive de leurs auteurs. Toute reproduction, 
      distribution ou utilisation non autorisée est strictement interdite.
    </p>
  </section>

  <section class="mb-6">
    <h3 class="text-xl font-semibold mb-2">9. Modification des CGV</h3>
    <p>
      PokéCommerce se réserve le droit de modifier les présentes Conditions Générales de Vente à tout moment. Les nouvelles CGV seront applicables 
      dès leur mise en ligne. Il est conseillé de consulter régulièrement cette page pour prendre connaissance des éventuelles mises à jour.
    </p>
  </section>

  <section class="mb-6">
    <h3 class="text-xl font-semibold mb-2">10. Contact</h3>
    <p>
      Pour toute question relative aux présentes CGV, vous pouvez nous contacter via notre page 
      <a href="/contact" class="text-blue-600 hover:underline">Contact</a> ou par e-mail à l’adresse :
      <strong>support@pokécommerce.fr</strong>.
    </p>
  </section>

  <p class="text-sm text-gray-600">
    Dernière mise à jour : <?= date('d/m/Y') ?>
  </p>
</div>
