<?php
if(!session_id()) session_start();
require_once('server_infos.php');
require_once('priv/utils.php');
require_once('priv/Data.php');
require_once('_template.php');

$db = new Database();
$config = $db->config();
$data = new Data();
$home_photo_1 = utils_home_photo_1();
$home_photo_2 = utils_home_photo_2();

capture_start();
?>
<div>
    <div class="logo"><img class="img-fluid" src="data/main/dmm_logo_cropped.png"/></div>
    <?php if ($home_photo_1) { ?><div class="home-photo-1"><img class="img-fluid" src="<?php echo utils_as_link($home_photo_1);?>"/></div><?php } ?>
    <div <?php if ($home_photo_2) { ?> class="second-photo" <?php } ?>>
		<?php if ($home_photo_2) { ?><div class="home-photo-2"><img class="img-fluid" src="<?php echo utils_as_link($home_photo_2);?>"/></div><?php } ?>
        <div class="home-text">
            <div class="float-left home-text-left"><?php echo $config->home_text_left();?></div>
            <div class="float-right home-text-right"><?php echo $config->home_text_right();?></div>
            <div class="home-text-bottom text-center"><?php echo $config->home_text_bottom();?></div>
        </div>
    </div>
</div>
<?php
capture_end($data->content);

$data->title = 'DMM';
$data->pagename = 'home';
echo template($data);
?>