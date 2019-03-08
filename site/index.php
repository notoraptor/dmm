<?php
if(!session_id()) session_start();
require_once('server_infos.php');
require_once('priv/utils.php');
require_once('priv/Data.php');
require_once('_template.php');

$db = new Database();
$config = $db->config();
$data = new Data($db);
$home_photo_1 = utils_home_photo_1();
$home_photo_2 = utils_home_photo_2();

capture_start();
?>
<div class="pt-5">
    <div class="logo-wrapper mt-5">
        <div class="logo" style="background-image: url('data/main/dmm_logo_cropped.png');"></div>
    </div>
    <div class="images">
        <div class="images-wrapper">
			<?php if ($home_photo_1) { ?><div class="photo-1" style="background-image: url('<?php echo utils_as_link($home_photo_1);?>');"></div><?php } ?>
			<?php if ($home_photo_2) { ?><div class="photo-2" style="background-image: url('<?php echo utils_as_link($home_photo_2);?>');"></div><?php } ?>
        </div>
    </div>
    <div class="texts">
        <div class="row texts-wrapper">
            <div class="left col-md-8"><?php echo $config->home_text_left();?></div>
            <div class="right col-md-4">
                <div class="right-wrapper">
					<?php echo $config->home_text_right();?>
                </div>
            </div>
        </div>
        <div class="bottom mt-5 pt-5 text-center"><?php echo $config->home_text_bottom();?></div>
    </div>
</div>
<?php
capture_end($data->content);

$data->title = 'DMM';
$data->pagename = 'home';
echo template($data);
?>