<?php
function template($data) {
if (!session_id()) session_start();
if ($data->meta_description == '')
	$data->meta_description = 'SILK fashion modeling agency, official website';
if ($data->meta_keywords == '') {
	$data->meta_keywords = implode(',', array(
		'fashion', 'modeling agency', 'silk', 'photography', 'booking', 'montreal', 'modeling', 'agency', 'model',
		'models', 'silk girl', 'silk man', 'silk team', 'silkgirl', 'silkteam'
	));
}
$menu_titles = array('ABOUT', 'MODELS', 'CONTACTS');
$menu_names = array('home', 'models', 'contacts');
$count_menu = count($menu_titles);
ob_start();
?><!DOCTYPE html>
<html lang="en">
<head>
    <base href="<?php echo server_http(); ?>/"/>
    <meta charset="UTF-8"/>
    <meta name="description" content="<?php echo $data->meta_description; ?>"/>
    <meta name="keywords" content="<?php echo $data->meta_keywords; ?>"/>
    <meta name="author" content="Steven Bocco"/>
    <title><?php echo $data->title; ?></title>
    <link rel="shortcut icon" type="image/x-icon" href="data/main/favicon.ico"/><!-- TODO favicon -->
    <link rel="icon" type="image/x-icon" href="data/main/favicon.ico"/>
    <link rel="stylesheet" href="lib/css/bootstrap.css"/>
    <link rel="stylesheet" href="css/style.css"/>
	<?php if ($data->head != '') echo $data->head; ?>
</head>
<body <?php if ($data->pagename != '') echo 'id="' . $data->pagename . '"'; ?>>
<?php if ($data->messages != '') { ?>
    <div class="messages"><?php echo $data->messages; ?></div>
<?php } ?>
<div id="content" class="container">
    <?php if ($data->show_menu) { ?>
    <div id="menu">
        <nav class="row menu-bar text-center">
		<?php for ($i = 0; $i < $count_menu; ++$i) {
			$menu_title = $menu_titles[$i];
			$menu_name = $menu_names[$i];
			?>
            <div class="col-md menu py-2 py-md-0">
				<?php if ($data->pagename != $menu_name) { ?>
                <a href="<?php echo $menu_name.'.php'; ?>"><?php } ?>
					<?php echo $menu_title; ?>
					<?php if ($data->pagename != $menu_name) { ?></a><?php } ?>
            </div>
			<?php
		} ?>
            <div class="col-md menu py-2 py-md-0"><a href="facebook">facebook</a></div>
            <div class="col-md menu py-2 py-md-0"><a href="instagram">instagram</a></div>
        </nav>
    </div>
    <?php }; ?>
    <div class="mt-5 mb-3 page">
		<?php echo $data->content; ?>
    </div>
</div>
<script type="text/javascript" src="lib/js/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="lib/js/popper.min.js"></script>
<script type="text/javascript" src="lib/js/bootstrap.js"></script>
<?php if ($data->scripts != '') echo $data->scripts; ?>
<script type="text/javascript" src="js/AnimateScroll.min.js"></script>
<script type="text/javascript"><!--
    document.body.onload = function () {
        // Gestion des éléments survolables pour iPhone et les appareils mobiles.
        var hoverables = document.getElementsByClassName('hoverable');
        console.log(hoverables.length + ' éléments explicitement survolables.');
        for (var x = 0; x < hoverables.length; ++x) {
            var hoverable = hoverables[x];
            if (!hoverable.onclick) {
                hoverable.onclick = function () {
                    void(0);
                    //console.log('clicked');
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