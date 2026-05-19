<?php
require_once('funcs-afficheDataTable.php');

function validerIP($ip) {
  if ($ip === '') return true; // Champ optionnel
  return filter_var($ip, FILTER_VALIDATE_IP) !== false;
}

function validerMAC($mac) {
  if ($mac === '') return true; // Champ optionnel
  return preg_match('/^([0-9A-Fa-f]{2}:){5}[0-9A-Fa-f]{2}$/', $mac);
}

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

  afficheTRTHTD('Nom * :', sprintf('<input type="text" name="nom" value="%s" placeholder="ex: PC-Bureau-01"/>', htmlspecialchars($nom)));

  $types = ['ordinateur', 'switch', 'serveur', 'autre'];
  $select = '<select name="type">';
  foreach ($types as $t) {
    $selected = ($type === $t) ? ' selected' : '';
    $select .= sprintf('<option value="%s"%s>%s</option>', $t, $selected, $t);
  }
  $select .= '</select>';
  afficheTRTHTD('Type * :', $select);

  afficheTRTHTD('Adresse IP :', sprintf('<input type="text" name="adresse_ip" value="%s" placeholder="ex: 192.168.1.10"/>', htmlspecialchars($adresse_ip)));
  afficheTRTHTD('Adresse MAC :', sprintf('<input type="text" name="adresse_mac" value="%s" placeholder="ex: AA:BB:CC:DD:EE:FF"/>', htmlspecialchars($adresse_mac)));
  afficheTRTHTD('Localisation :', sprintf('<input type="text" name="localisation" value="%s" placeholder="ex: Salle 101"/>', htmlspecialchars($localisation)));
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

  $erreurs = false;

  if ($nom === '') {
    cbPrintf('<h2 style="color:red;">Erreur : le nom est obligatoire !</h2>');
    $erreurs = true;
  }

  if (!validerIP($adresse_ip)) {
    cbPrintf('<h2 style="color:red;">Erreur : adresse IP invalide ! (ex: 192.168.1.10)</h2>');
    $erreurs = true;
  }

  if (!validerMAC($adresse_mac)) {
    cbPrintf('<h2 style="color:red;">Erreur : adresse MAC invalide ! (ex: AA:BB:CC:DD:EE:FF)</h2>');
    $erreurs = true;
  }

  if ($erreurs) {
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