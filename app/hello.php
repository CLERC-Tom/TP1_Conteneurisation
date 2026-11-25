<?php
require __DIR__ . '/bootstrap.php';

if (empty($_SESSION['user'])) {
    header('Location: /index.php');
    exit;
}
?>
<!doctype html>
<html lang="fr">
<head><meta charset="utf-8"><title>Hello</title></head>
<body>
  <h1>Hello World !</h1>
  <p>Connecté en tant que <b><?= e($_SESSION['user']) ?></b></p>
  <!-- hostname = conteneur / pod qui a répondu : utile pour observer les replicas -->
  <p><small>Servi par <?= e(gethostname()) ?></small></p>
  <a href="/logout.php">Se déconnecter</a>
</body>
</html>
