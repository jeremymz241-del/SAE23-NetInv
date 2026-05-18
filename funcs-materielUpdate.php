<?php
require_once('funcs-afficheDataTable.php');

function materielUpdateFormByID($id) {
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

  cbPrintf('<h2>Modifier l\'équipement #%s</h2>', $id);
  cbPrintf('<form action="%s" method="post" accept-charset="utf8">', $_SERVER['PHP_SELF']);
  cbPrintf('<input type="hidden" name="action" value="update"/>');
  cbPrintf('<input type="hidden" name="id" value="%s"/>', $id);
  cbPrintf('<table class="update">');

  afficheTRTHTD('ID :', sprintf('<input type="text" value="%s" readonly/>', $id));
  afficheTRTHTD('Nom :', sprintf('<input type="text" name="nom" value="%s"/>', htmlspecialchars($row['nom'])));

  $types = ['ordinateur', 'switch', 'serveur', 'autre'];
  $select = '<select name="type">';
  foreach ($types as $t) {
    $selected = ($row['type'] === $t) ? ' selected' : '';
    $select .= sprintf('<option value="%s"%s>%s</option>', $t, $selected, $t);
  }
  $select .= '</select>';
  afficheTRTHTD('Type :', $select);

  afficheTRTHTD('Adresse IP :', sprintf('<input type="text" name="adresse_ip" value="%s"/>', htmlspecialchars($row['adresse_ip'])));
  afficheTRTHTD('Adresse MAC :', sprintf('<input type="text" name="adresse_mac" value="%s"/>', htmlspecialchars($row['adresse_mac'])));
  afficheTRTHTD('Localisation :', sprintf('<input type="text" name="localisation" value="%s"/>', htmlspecialchars($row['localisation'])));
  afficheTRTD('<input type="submit" value="Mettre à jour"/>', 'colspan="2"');

  cbPrintf('</table>');
  cbPrintf('</form>');
}

function materielUpdateByID($id) {
  global $pdo;

  if (cbGetValue($_SESSION, 'user') !== 'admin') {
    cbPrintf('<h2 style="color:red;">Accès refusé !!!</h2>');
    materielList();
    return;
  }

  $nom          = cbGetValue($_REQUEST, 'nom');
  $type         = cbGetValue($_REQUEST, 'type');
  $adresse_ip   = cbGetValue($_REQUEST, 'adresse_ip');
  $adresse_mac  = cbGetValue($_REQUEST, 'adresse_mac');
  $localisation = cbGetValue($_REQUEST, 'localisation');

  if (intval($id) > 0 && $nom !== '') {
    $stmt = $pdo->prepare("UPDATE materiel SET nom=?, type=?, adresse_ip=?, adresse_mac=?, localisation=? WHERE id=?");
    $stmt->execute([$nom, $type, $adresse_ip, $adresse_mac, $localisation, $id]);
    $stmt->closeCursor();
    cbPrintf('<h2 style="color:green;">Équipement #%s modifié avec succès !</h2>', $id);
  } else {
    cbPrintf('<h2 style="color:red;">Modification impossible !!!</h2>');
  }
  materielList();
}
?>