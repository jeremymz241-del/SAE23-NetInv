<?php
require_once('funcs-afficheDataTable.php');

function materielInsertForm() {
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

  cbPrintf('<h2>Nouvel équipement</h2>');
  cbPrintf('<form action="%s" method="post" accept-charset="utf8">', $_SERVER['PHP_SELF']);
  cbPrintf('<input type="hidden" name="action" value="insert"/>');
  cbPrintf('<table class="update">');

  afficheTRTHTD('Nom :', sprintf('<input type="text" name="nom" value="%s"/>', htmlspecialchars($nom)));

  // Select type
  $types = ['ordinateur', 'switch', 'serveur', 'autre'];
  $select = '<select name="type">';
  foreach ($types as $t) {
    $selected = ($type === $t) ? ' selected' : '';
    $select .= sprintf('<option value="%s"%s>%s</option>', $t, $selected, $t);
  }
  $select .= '</select>';
  afficheTRTHTD('Type :', $select);

  afficheTRTHTD('Adresse IP :', sprintf('<input type="text" name="adresse_ip" value="%s"/>', htmlspecialchars($adresse_ip)));
  afficheTRTHTD('Adresse MAC :', sprintf('<input type="text" name="adresse_mac" value="%s"/>', htmlspecialchars($adresse_mac)));
  afficheTRTHTD('Localisation :', sprintf('<input type="text" name="localisation" value="%s"/>', htmlspecialchars($localisation)));
  afficheTRTD('<input type="submit" value="Ajouter l\'équipement"/>', 'colspan="2"');

  cbPrintf('</table>');
  cbPrintf('</form>');
}

function materielInsert() {
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

  if ($nom === '' || $type === '') {
    cbPrintf('<h2 style="color:red;">Erreur : nom et type obligatoires !!!</h2>');
    materielInsertForm();
    return;
  }

  $stmt = $pdo->prepare("INSERT INTO materiel (nom, type, adresse_ip, adresse_mac, localisation) VALUES (?, ?, ?, ?, ?)");
  $stmt->execute([$nom, $type, $adresse_ip, $adresse_mac, $localisation]);
  $stmt->closeCursor();
  cbPrintf('<h2 style="color:green;">Équipement [%s] ajouté avec succès !</h2>', htmlspecialchars($nom));
  materielList();
}
?>