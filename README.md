# Pokémon E-Commerce

Une application d’e-commerce en PHP permettant de vendre et gérer des Pokémon : ajout d’articles, panier, checkout, espace utilisateur, factures, administration (CRUD produits, gestion du stock, etc.).

---

## 📝 Présentation

Ce projet est un site de vente en ligne de Pokémon, développé avec :
- PHP 8.1 + Apache (Docker)
- MySQL 8.0 (BDD principale)
- PostgreSQL + SonarQube (analyse de code)
- MailHog (dev mail)
- Swagger UI (documentation API)
- PHPMyAdmin (administration MySQL)
- Tailwind CSS pour le style minimaliste

Fonctionnalités principales :
- Inscription / authentification (roles `user` et `admin`)
- Catalogue public, pages produit
- Panier (session) + checkout sécurisé (formulaire adresse, paiement simulé)
- Création automatique de facture, envoi e-mail de confirmation (via MailHog)
- Espace personnel → profil, factures (PDF inclus)
- Back-office admin : gestion des produits, des utilisateurs, du stock
- Upload d’images pour les produits
- Analyse qualité de code avec SonarQube

---

## ⚙️ Prérequis

1. **Docker & Docker Compose**  
   — pour lancer chaque service en conteneur (PHP/Apache, bases, SonarQube…).  
   Versions testées : Docker 20.10+, Docker Compose 1.29+ (ou v2).

2. **Git**  
   — pour cloner et versionner le projet.

3. **SonarScanner CLI** (optionnel)  
   — si vous souhaitez lancer l’analyse statique localement.  
   (Voir section “Analyse SonarQube” plus bas.)

---

## 🛠️ Installation & configuration

1. **Cloner le dépôt**  
   ```bash
   git clone https://github.com/Lykkss/mon-ecommerce.git
   cd mon-ecommerce

2. **Composer : installer les dépendances PHP**  

Le Dockerfile exécute déjà composer install lors de la construction de l’image, donc vous n’avez rien à faire en local.
Si vous voulez installer manuellement (sans Docker) :
    ```bash
    composer install

## 🐳 Lancement avec Docker Compose

À la racine du projet, exécutez :
    ```bash
    docker-compose up -d --build

Cela va démarrer les conteneurs suivants :

php : conteneur Apache + PHP 8.1 (port 8000)

db : MySQL 8.0 (port 3307)

phpmyadmin : PhpMyAdmin (port 8080)

swagger : Swagger UI (port 8081)

mailhog : MailHog (SMTP 1025, Web UI 8025)

db_sonar : PostgreSQL pour Sonar (sans port exposé public)

sonarqube : SonarQube CE (port 9000)

Une fois tous les services en état healthy, ouvrez votre navigateur :

Frontend PHP : http://localhost:8000/

PhpMyAdmin : http://localhost:8080/ (identifiants : user root / pass root)

Swagger UI : http://localhost:8081/

MailHog Web UI : http://localhost:8025/

SonarQube : http://localhost:9000/ (admin/P@ssw0rd123! par défaut)

## 🔧 Configuration MySQL et création de la base

À la première montée, un script d’initialisation SQL est copié depuis ./data/ vers la base MySQL :

docker-compose.yml monte ./data:/docker-entrypoint-initdb.d:ro

Le fichier data/init.sql crée la base pokemon_db et les tables nécessaires.

Vérifiez avec : docker-compose exec db mysql -u root -proot -D pokemon_db -e "SHOW TABLES;"

## 🚀 Utilisation

1. **Inscription / Connexion**
    Accédez à http://localhost:8000/register pour créer un compte.
    Puis http://localhost:8000/login pour vous connecter.

2. **Catalogue Public**

Page d’accueil / affiche la liste des produits.

Cliquez sur un produit pour voir ses détails (/produit/{id}).

Depuis la page produit, ajoutez-le au panier (quantité configurable).

3. **Panier**

Page /panier liste les articles.

Vous pouvez supprimer un produit ou accéder à ses détails.

Cliquez sur Commander pour passer à l’étape de paiement.

4. **Chekout/Panier**

Si vous n’êtes pas loggé·e, vous êtes redirigé·e vers /login.

Sinon vous voyez un formulaire (/commande) pour renseigner adresse, e-mail, numéro de téléphone, méthode de paiement (simulée).

Une fois la commande validée :

Création de la facture en base (table invoices + invoice_items).

Décrémentation du stock.

Envoi d’un e-mail de confirmation via MailHog (SMTP sur mailhog:1025).

Vidage du panier et page de remerciement (order_success).

5. **Mon Compte & Factures**

Dans le menu en haut, cliquez sur Mon compte (/compte).

Vous pouvez mettre à jour vos informations de profil.

Onglet Factures (/compte/factures) liste vos factures.

Cliquer sur une facture vous affiche le détail (/compte/facture/{id}) et propose un lien PDF (/compte/facture/{id}/pdf).


6. **Administration (role = admin)**

Connectez-vous avec un compte dont le champ role en base est admin.

Dans le menu en haut, lien Dashboard Admin :

Produits (/admin/products) → CRUD complet (create/edit/delete).

Utilisateurs (/admin/users) → CRUD utilisateurs.

Stock (/admin/stock) → consultation et mise à jour globale ou par article.

7. **Swagger UI / API**

Vous trouverez le fichier de spécification OpenAPI dans public/swagger/swagger.yaml.

Swagger UI est disponible sur http://localhost:8081, et pointe vers /swagger/swagger.yaml.


## 📦 Structure du projet

mon-ecommerce/
├── app/                    # Code source PHP (MVC)
│   ├── Controllers/        # Controllers (Home, Cart, Checkout, Auth, Account, Sell, Invoice, Stock, Admin/…)
│   ├── Core/               # Router, Database, etc.
│   ├── Models/             # Modèles (Product, User, Invoice, InvoiceItem, Stock…)
│   └── Views/              # Vues (layout.php, home.php, cart.php, checkout.php, etc.)
├── data/                   # Scripts d’initialisation MySQL (init.sql)
├── public/                 # DocumentRoot Apache
│   ├── index.php           # Front Controller
│   ├── assets/             # CSS, images, avatars, etc.
│   └── swagger/            # swagger.yaml
├── docker-compose.yml      # Orchestration Docker
├── Dockerfile              # Image PHP/Apache
├── composer.json           # Dépendances PHP
├── sonar-project.properties# Config SonarScanner (analyse statique)
└── README.md               # ← ce fichier

## 📊 Qualité de code & SonarQube

1. **Démarrage de SonarQube & PostgreSQL**

docker-compose up -d db_sonar sonarqube

→ SonarQube est accessible sur http://localhost:9000 (identifiant par défaut admin / P@ssw0rd123!).

2. **Créer un jeton (token)**

Connectez-vous à l’UI SonarQube (admin / P@ssw0rd123!).

Dans votre espace utilisateur → “Security” → “Generate Tokens”.

Copiez le jeton généré (par exemple sqp_88dd84713ad648a110337d25d9e00827946238b6).

3. **Configurer le fichier sonar-project.properties**

sonar.projectKey=mon-ecommerce
sonar.projectName=Mon E-Commerce PHP
sonar.sources=app,public
sonar.host.url=http://localhost:9000
sonar.login=<TOKEN_SONAR>

4. **Lancer l'analyse**

Depuis la racine :

sonar-scanner \
  -Dsonar.projectKey=mon-ecommerce \
  -Dsonar.sources=. \
  -Dsonar.host.url=http://localhost:9000 \
  -Dsonar.login=<TOKEN_SONAR>

→ Une fois terminé, les métriques et issues s’affichent dans l’interface SonarQube.


