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

$class_1 = 'long-width';
$class_2 = 'long-width';

if ($home_photo_1) {
    $size_1 = getimagesize($home_photo_1);
    $width = $size_1[0];
    $height = $size_1[0];
    if ($height > $width)
        $class_1 = 'long-height';
}
if ($home_photo_2) {
	$size_1 = getimagesize($home_photo_2);
	$width = $size_1[0];
	$height = $size_1[0];
	if ($height > $width)
		$class_2 = 'long-height';
}

capture_start();
?>
<div class="pt-5">
    <div class="logo-wrapper mt-5">
        <div class="logo" style="background-image: url('data/main/dmm_logo_cropped.png');"></div>
    </div>
    <div class="images">
        <div class="images-wrapper">
			<?php if ($home_photo_1) { ?>
            <img class="image-1 <?php echo $class_1;?>" src="<?php echo utils_as_link($home_photo_1);?>"/>
            <?php } ?>
			<?php if ($home_photo_2) { ?>
            <img class="image-2 <?php echo $class_1;?>" src="<?php echo utils_as_link($home_photo_2);?>"/>
            <?php } ?>
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
        <div class="bottom text-center"><?php echo $config->home_text_bottom();?></div>
    </div>
</div>
<?php
capture_end($data->content);

$data->title = 'DMM';
$data->pagename = 'home';
echo template($data);
?>