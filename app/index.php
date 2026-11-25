<?php
require __DIR__ . '/bootstrap.php';

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Requête préparée : pas d'injection SQL
    $stmt = db()->prepare('SELECT password_hash FROM users WHERE username = :u');
    $stmt->execute(['u' => $username]);
    $hash = $stmt->fetchColumn();

    if ($hash !== false && password_verify($password, $hash)) {
        session_regenerate_id(true); // contre la fixation de session
        $_SESSION['user'] = $username;
        header('Location: /hello.php');
        exit;
    }
    $error = 'Identifiants invalides';
}
?>
<!doctype html>
<html lang="fr">
<head><meta charset="utf-8"><title>Connexion</title></head>
<body>
  <h1>Connexion</h1>
  <?php if ($error): ?><p style="color:red"><?= e($error) ?></p><?php endif; ?>
  <form method="post">
    <label>Utilisateur <input name="username" required></label><br>
    <label>Mot de passe <input name="password" type="password" required></label><br>
    <button type="submit">Se connecter</button>
  </form>
</body>
</html>
