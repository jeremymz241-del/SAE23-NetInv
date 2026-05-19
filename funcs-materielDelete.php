<?php
require_once('funcs-afficheDataTable.php');

function materielDeleteFormByID($id) {
  global $pdo;

  if (cbGetValue($_SESSION, 'user') !== 'admin') {
    cbPrintf('<h2 style="color:red;">Accès refusé !!!</h2>');
    materielList();
    return;
  }

  $stmt = $pdo->prepare("SELECT * FROM materiel WHERE id = ?");
  $stmt->execute([$id]);
  $row = $stmt->fetch();
  $stmt->closeCursor();

  if (!$row) {
    cbPrintf('<h2 style="color:red;">Équipement #%s introuvable !!!</h2>', $id);
    return;
  }

  cbPrintf('<h2>Supprimer l\'équipement #%s</h2>', $id);
  cbPrintf('<form action="%s" method="post" accept-charset="utf8">', $_SERVER['PHP_SELF']);
  cbPrintf('<input type="hidden" name="action" value="delete"/>');
  cbPrintf('<input type="hidden" name="id" value="%s"/>', $id);
  cbPrintf('<table class="update">');

  afficheTRTHTD('ID :', $row['id']);
  afficheTRTHTD('Nom :', htmlspecialchars($row['nom']));
  afficheTRTHTD('Type :', htmlspecialchars($row['type']));
  afficheTRTHTD('Adresse IP :', htmlspecialchars($row['adresse_ip']));
  afficheTRTHTD('Localisation :', htmlspecialchars($row['localisation']));

  $boutons  = sprintf('<input type="submit" value="Supprimer l\'équipement #%s"/> ', $id);
  $boutons .= sprintf('<a href="%s">Annuler</a>', $_SERVER['PHP_SELF']);
  afficheTRTD($boutons, 'colspan="2"');

  cbPrintf('</table>');
  cbPrintf('</form>');
}

function materielDeleteByID($id) {
  global $pdo;

  if (cbGetValue($_SESSION, 'user') !== 'admin') {
    cbPrintf('<h2 style="color:red;">Accès refusé !!!</h2>');
    materielList();
    return;
  }

  if (intval($id) > 0) {
    $stmt = $pdo->prepare("UPDATE materiel SET actif = 0 WHERE id = ?");
    $stmt->execute([$id]);
    $stmt->closeCursor();
    cbPrintf('<h2 style="color:green;">Équipement #%s supprimé.</h2>', $id);
  } else {
    cbPrintf('<h2 style="color:red;">Suppression impossible !!!</h2>');
  }
  materielList();
}
?>