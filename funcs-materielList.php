<?php
require_once('funcs-afficheDataTable.php');

function getIcone($type) {
  switch ($type) {
    case 'ordinateur': return '💻';
    case 'switch':     return '🔀';
    case 'serveur':    return '🖥️';
    default:           return '📦';
  }
}

function afficheTHSortable($label, $col, $currentSort, $currentOrder) {
  $newOrder = ($currentSort === $col && $currentOrder === 'ASC') ? 'DESC' : 'ASC';
  $arrow = '';
  if ($currentSort === $col) {
    $arrow = ($currentOrder === 'ASC') ? ' ▲' : ' ▼';
  }
  printf('<th><a href="?sort=%s&order=%s">%s%s</a></th>', $col, $newOrder, $label, $arrow);
}

function materielList() {
  global $pdo;
  $isAdmin = (cbGetValue($_SESSION, 'user') === 'admin');

  if ($isAdmin) {
    cbPrintf('<a href="%s?action=insertForm">➕ Ajouter un équipement</a>', $_SERVER['PHP_SELF']);
    cbPrintf('<a href="%s?action=archive">🗑️ Équipements supprimés</a><br/><br/>', $_SERVER['PHP_SELF']);
  }

  // Recherche
  $search = cbGetValue($_REQUEST, 'search');

  // Tri
  $colsAutorisees = ['id', 'nom', 'type', 'adresse_ip', 'adresse_mac', 'localisation', 'date_ajout'];
  $sort  = cbGetValue($_REQUEST, 'sort', 'type');
  $order = cbGetValue($_REQUEST, 'order', 'ASC');
  if (!in_array($sort, $colsAutorisees)) $sort = 'type';
  if ($order !== 'ASC' && $order !== 'DESC') $order = 'ASC';

  // Formulaire de recherche
  cbPrintf('<form action="%s" method="get" accept-charset="utf8">', $_SERVER['PHP_SELF']);
  cbPrintf('<table class="search">');
  cbPrintf('<tr>');
  cbPrintf('<td><input type="text" name="search" value="%s" placeholder="🔍 Rechercher un équipement..."/></td>', htmlspecialchars($search));
  cbPrintf('<td><input type="submit" value="Rechercher"/></td>');
  if ($search !== '') {
    cbPrintf('<td><a href="%s">✕ Effacer</a></td>', $_SERVER['PHP_SELF']);
  }
  cbPrintf('</tr>');
  cbPrintf('</table>');
  cbPrintf('</form>');

  // Requête
  if ($search !== '') {
    $like = '%' . $search . '%';
    $stmt = $pdo->prepare("SELECT * FROM materiel WHERE actif = 1 AND (
      nom LIKE ? OR type LIKE ? OR adresse_ip LIKE ? OR adresse_mac LIKE ? OR localisation LIKE ?
    ) ORDER BY $sort $order");
    $stmt->execute([$like, $like, $like, $like, $like]);
  } else {
    $stmt = $pdo->prepare("SELECT * FROM materiel WHERE actif = 1 ORDER BY $sort $order");
    $stmt->execute();
  }

  $materiels = $stmt->fetchAll();
  $stmt->closeCursor();

  // Compteurs par type
  $nbTotal = count($materiels);
  $nbOrdi  = 0; $nbSwitch = 0; $nbServeur = 0; $nbAutre = 0;
  foreach ($materiels as $row) {
    switch ($row['type']) {
      case 'ordinateur': $nbOrdi++;    break;
      case 'switch':     $nbSwitch++;  break;
      case 'serveur':    $nbServeur++; break;
      default:           $nbAutre++;   break;
    }
  }

  // Affichage compteurs
  cbPrintf('<div class="compteurs">');
  cbPrintf('<span class="compteur">📦 Total : <strong>%s</strong></span>', $nbTotal);
  cbPrintf('<span class="compteur">💻 Ordinateurs : <strong>%s</strong></span>', $nbOrdi);
  cbPrintf('<span class="compteur">🔀 Switchs : <strong>%s</strong></span>', $nbSwitch);
  cbPrintf('<span class="compteur">🖥️ Serveurs : <strong>%s</strong></span>', $nbServeur);
  if ($nbAutre > 0) cbPrintf('<span class="compteur">📦 Autres : <strong>%s</strong></span>', $nbAutre);
  cbPrintf('</div>');

  if ($search !== '') {
    cbPrintf('<p><strong>%s équipement(s) trouvé(s) pour "%s"</strong></p>', $nbTotal, htmlspecialchars($search));
  }

  if ($nbTotal === 0) {
    cbPrintf('<p style="color:gray;">Aucun équipement trouvé.</p>');
    return;
  }

  printf("<table>\n");
  printf("<tr>");
  afficheTHSortable('ID', 'id', $sort, $order);
  afficheTHSortable('Nom', 'nom', $sort, $order);
  afficheTHSortable('Type', 'type', $sort, $order);
  afficheTHSortable('Adresse IP', 'adresse_ip', $sort, $order);
  afficheTH('Adresse MAC');
  afficheTHSortable('Localisation', 'localisation', $sort, $order);
  afficheTHSortable('Date ajout', 'date_ajout', $sort, $order);
  afficheTH('Action');
  printf("</tr>\n");

  foreach ($materiels as $row) {
    printf("<tr>");
    afficheTD($row['id']);
    // Nom avec icône
    afficheTD(getIcone($row['type']) . ' ' . htmlspecialchars($row['nom']));
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
}
?>