<?php
function template(Data $data) {
if (!session_id()) session_start();
$menu_titles = array('DIVERSITY', 'MODELS', 'CONTACT');
$menu_names = array('index', 'models', 'contact');
$count_menu = count($menu_titles);
ob_start();
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="description" content="<?php echo utils_meta_description(true, $data->meta_description); ?>"/>
    <meta name="keywords" content="<?php echo utils_meta_keywords(true, $data->meta_keywords); ?>"/>
    <meta name="author" content="Steven Bocco"/>
    <title><?php echo $data->title; ?></title>
    <link rel="shortcut icon" type="image/x-icon" href="data/main/favicon.ico"/><!-- TODO favicon -->
    <link rel="icon" type="image/x-icon" href="data/main/favicon.ico"/>
    <link rel="stylesheet" href="css/bootstrap.css"/>
    <link rel="stylesheet" href="css/style.css"/>
	<?php if ($data->head != '') echo $data->head; ?>
</head>
<body <?php if ($data->pagename != '') echo 'id="' . $data->pagename . '"'; ?>>
<?php if ($data->messages != '') { ?>
    <div class="messages"><?php echo $data->messages; ?></div>
<?php } ?>
<div id="content" class="<?php echo $data->content_class;?>">
    <?php if ($data->show_menu) { ?>
        <div class="row align-items-center">
            <div class="col-md-9 menu-words">
                <nav class="row text-center my-5 align-items-center">
					<?php for ($i = 0; $i < $count_menu; ++$i) {
						$menu_title = $menu_titles[$i];
						$menu_name = $menu_names[$i];
						?>
                        <div class="col-md">
							<?php if ($data->pagename != $menu_name) { ?><a href="<?php echo $menu_name.'.php'; ?>"><?php } ?>
								<?php echo $menu_title; ?>
								<?php if ($data->pagename != $menu_name) { ?></a><?php } ?>
                        </div>
						<?php
					}
					?>
                </nav>
            </div>
            <div class="col-md-3 menu-logos">
                <nav class="row text-right my-5 align-items-center">
					<?php
                    $link_facebook = 'index.php';
					$link_instagram = 'index.php';
					if ($data->db) {
						$config = $data->db->config();
						$link_facebook = $config->link_facebook();
						$link_instagram = $config->link_instagram();
					}
					?>
                    <div class="col-md"><a class="top-link facebook py-1" target="_blank" href="<?php echo $link_facebook;?>"></a></div>
                    <div class="col-md"><a class="top-link instagram py-1" target="_blank" href="<?php echo $link_instagram;?>"></a></div>
                </nav>
            </div>
        </div>
    <?php };
	echo $data->content;
    ?>
</div>
<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="js/popper.min.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
<?php if ($data->scripts != '') echo $data->scripts; ?>
<script type="text/javascript">//<!--
    document.body.onload = function () {
        // Gestion des éléments survolables pour iPhone et les appareils mobiles.
        var hoverables = document.getElementsByClassName('hoverable');
        console.log(hoverables.length + ' éléments explicitement survolables.');
        for (var x = 0; x < hoverables.length; ++x) {
            var hoverable = hoverables[x];
            if (!hoverable.onclick) {
                hoverable.onclick = function () {
                    void(0); //console.log('clicked');
                };
            }
        }
    }
//--></script>
</body>
</html><?php
$content = ob_get_contents();
ob_end_clean();
return $content;
}
?>