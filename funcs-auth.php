<?php
function auth($user, $pass) {
  global $pdo;

  if ($user === '') return false;

  $stmt = $pdo->prepare("SELECT * FROM users WHERE `user` = ? AND `pass` = ?");
  $stmt->execute([$user, $pass]);
  $result = $stmt->fetch();

  if ($result) {
    $_SESSION['user'] = $result['user'];
    $_SESSION['id']   = $result['id'];
    cbPrintf('<h2>Utilisateur [%s] authentifié !</h2>', $user);
    return true;
  } else {
    cbPrintf('<h2 style="color:red;">BAD PASSWORD for [%s] !!!</h2>', $user);
    return false;
  }
}
?>