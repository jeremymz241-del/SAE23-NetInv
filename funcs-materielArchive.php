<?php
require_once('funcs-afficheDataTable.php');

function materielArchive() {
  global $pdo;

  if (cbGetValue($_SESSION, 'user') !== 'admin') {
    cbPrintf('<h2 style="color:red;">Accès refusé !!!</h2>');
    materielList();
    return;
  }

  // Restauration d'un équipement
  $restore = cbGetValue($_REQUEST, 'restore');
  if (intval($restore) > 0) {
    $stmt = $pdo->prepare("UPDATE materiel SET actif = 1 WHERE id = ?");
    $stmt->execute([intval($restore)]);
    $stmt->closeCursor();
    cbPrintf('<h2 style="color:green;">Équipement #%s restauré avec succès !</h2>', $restore);
  }

  cbPrintf('<h2>Équipements supprimés</h2>');
  cbPrintf('<a href="%s">← Retour à l\'inventaire</a><br/><br/>', $_SERVER['PHP_SELF']);

  $stmt = $pdo->prepare("SELECT * FROM materiel WHERE actif = 0 ORDER BY date_ajout DESC");
  $stmt->execute();
  $materiels = $stmt->fetchAll();
  $stmt->closeCursor();

  $nb = count($materiels);
  cbPrintf('<p><strong>%s équipement(s) supprimé(s)</strong></p>', $nb);

  if ($nb === 0) {
    cbPrintf('<p style="color:gray;">Aucun équipement supprimé.</p>');
    return;
  }

  printf("<table>\n");
  printf("<tr>");
  afficheTH('ID');
  afficheTH('Nom');
  afficheTH('Type');
  afficheTH('Adresse IP');
  afficheTH('Adresse MAC');
  afficheTH('Localisation');
  afficheTH('Date ajout');
  afficheTH('Action');
  printf("</tr>\n");

  foreach ($materiels as $row) {
    printf("<tr>");
    afficheTD($row['id']);
    afficheTD(htmlspecialchars($row['nom']));
    afficheTD(htmlspecialchars($row['type']));
    afficheTD(htmlspecialchars($row['adresse_ip']));
    afficheTD(htmlspecialchars($row['adresse_mac']));
    afficheTD(htmlspecialchars($row['localisation']));
    afficheTD(htmlspecialchars($row['date_ajout']));
    $lien = sprintf('<a href="%s?action=archive&restore=%s">♻️ Restaurer</a>', $_SERVER['PHP_SELF'], $row['id']);
    afficheTD($lien);
    printf("</tr>\n");
  }
  printf("</table>\n");
}
?>