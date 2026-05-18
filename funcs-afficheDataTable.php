<?php
function afficheTH($label) {
  printf('<th>%s</th>', htmlspecialchars($label));
}
function afficheTD($val, $attrs = '') {
  if ($attrs != '') printf('<td %s>%s</td>', $attrs, $val);
  else printf('<td>%s</td>', $val);
}
function afficheTRTHTD($label, $val) {
  printf('<tr><th>%s</th><td>%s</td></tr>', htmlspecialchars($label), $val);
}
function afficheTRTD($val, $attrs = '') {
  if ($attrs != '') printf('<tr><td %s>%s</td></tr>', $attrs, $val);
  else printf('<tr><td>%s</td></tr>', $val);
}
?>