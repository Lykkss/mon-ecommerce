<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>PokéCommerce</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
  <header class="bg-blue-800 text-white p-4">
    <h1 class="text-xl">PokéCommerce</h1>
  </header>
  <main class="container mx-auto p-4">
    <?php include __DIR__ . '/' . ($product ?? false ? 'show.php' : 'home.php'); ?>
  </main>
</body>
</html>
