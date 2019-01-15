<?php
header("Content-type: application/xml");
require_once("server_infos.php");
require_once("priv/utils.php");
$db = new Database();
$models = $db->models();
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
echo '<url><loc>'.server_http().'</loc></url>';
echo '<url><loc>'.server_http().'contact</loc></url>';
if($models) foreach($models as $m) {
    echo '<url><loc>'.server_http().'/model.php?id='.$m->id().'</loc></url>';
    // todo
}
// todo
echo '</urlset>';
?>
