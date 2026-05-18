<?php
$title = 'Inventaire du parc informatique';
include('login.php');
require_once('funcs-afficheDataTable.php');
require_once('funcs-materielList.php');
require_once('funcs-materielInsert.php');
require_once('funcs-materielUpdate.php');
require_once('funcs-materielDelete.php');

cbPrintf('<h1>%s</h1>', $title);

$action = cbGetValue($_REQUEST, 'action');
$id     = cbGetValue($_REQUEST, 'id');

switch ($action) {
  case 'insertForm':  materielInsertForm();         break;
  case 'insert':      materielInsert();             break;
  case 'updateForm':  materielUpdateFormByID($id);  break;
  case 'update':      materielUpdateByID($id);      break;
  case 'deleteForm':  materielDeleteFormByID($id);  break;
  case 'delete':      materielDeleteByID($id);      break;
  default:            materielList();               break;
}

include('html-fin.php');
?>