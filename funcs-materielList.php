<?php
require_once('funcs-afficheDataTable.php');

function materielList() {
  global $pdo;
  $isAdmin = (cbGetValue($_SESSION, 'user') === 'admin');

  if ($isAdmin) {
    cbPrintf('<a href="%s?action=insertForm">➕ Ajouter un équipement</a><br/>', $_SERVER['PHP_SELF']);
  }

  $stmt = $pdo->prepare("SELECT * FROM materiel WHERE actif = 1 ORDER BY type, nom");
  $stmt->execute();

  printf("<table>\n");
  $entete = false;
  while ($row = $stmt->fetch()) {
    if (!$entete) {
      $entete = true;
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
    }
    printf("<tr>");
    afficheTD($row['id']);
    afficheTD(htmlspecialchars($row['nom']));
    afficheTD(htmlspecialchars($row['type']));
    afficheTD(htmlspecialchars($row['adresse_ip']));
    afficheTD(htmlspecialchars($row['adresse_mac']));
    afficheTD(htmlspecialchars($row['localisation']));
    afficheTD(htmlspecialchars($row['date_ajout']));

    $actions = '';
    if ($isAdmin) {
      $actions .= sprintf('<a href="%s?action=updateForm&id=%s">Modifier</a> ', $_SERVER['PHP_SELF'], $row['id']);
      $actions .= sprintf('<a href="%s?action=deleteForm&id=%s">Supprimer</a>', $_SERVER['PHP_SELF'], $row['id']);
    }
    if ($actions === '') $actions = '-';
    afficheTD($actions);
    printf("</tr>\n");
  }
  printf("</table>\n");
  $stmt->closeCursor();
}
?>