<?php
if(!session_id()) session_start();
require_once('server_infos.php');
require_once('priv/utils.php');
require_once('priv/Data.php');
require_once('_template.php');
$id = utils_s_get('id');
if (!ctype_digit($id))
	utils_redirection('index.php');
$db = new Database();
$model = $db->model($id);
if(!$model) utils_redirection('index.php');
$data = new Data();
$data->title = $model->full_name(). ' | DMM';
$data->pagename = 'model';
$data->show_menu = false;
$data->meta_description[] = $model->full_name();
$data->meta_keywords[] = $model->full_name();
$data->content_class = 'model py-5 d-flex';
capture_start();
?>
<div class="header pl-5 d-flex">
    <div>
        <a class="logo" href="index.php"><img src="data/main/dmm_logo_cropped.png"/></a>
    </div>
    <div class="details align-self-center">
        <h1><?php echo $model->first_name();?></h1>
        <h2><?php echo $model->hint();?></h2>
        <div class="buttons">
			<?php if ($model->instagram_link()) { echo '<a target="_blank" class="button btn btn-outline-dark" href="'.$model->instagram_link().'">INSTAGRAM</a>'; } ?>
			<?php if (utils_model_card($model->id())) { echo '<a target="_blank" class="button btn btn-outline-dark" href="'.utils_as_link(utils_model_card($model->id())).'">MODEL CARD</a>'; } ?>
			<?php if ($model->video_link()) { echo '<a target="_blank" class="button btn btn-outline-dark" href="'.$model->video_link().'">CLIP</a>'; } ?>
        </div>
    </div>
</div>
<div class="photos">
<?php
if ($model->photos()) {
    foreach ($model->photos() as $photo) {
        ?><img class="my-img-fluid mx-2" src="<?php echo $photo->getURL();?>"/><?php
    }
} ?>
</div>
<?php
capture_end($data->content);
capture_start();
?>
<script type="text/javascript">//<!--
    // Taken from: https://stackoverflow.com/a/23967214
    // http://www.dte.web.id/2013/02/event-mouse-wheel.html
    (function() {
        function scrollHorizontally(e) {
            e = window.event || e;
            var delta = Math.max(-1, Math.min(1, (e.wheelDelta || -e.detail)));
            document.documentElement.scrollLeft -= (delta*40); // Multiplied by 40
            document.body.scrollLeft -= (delta*40); // Multiplied by 40
            e.preventDefault();
        }
        if (window.addEventListener) {
            // IE9, Chrome, Safari, Opera
            window.addEventListener("mousewheel", scrollHorizontally, false);
            // Firefox
            window.addEventListener("DOMMouseScroll", scrollHorizontally, false);
        } else {
            // IE 6/7/8
            window.attachEvent("onmousewheel", scrollHorizontally);
        }
    })();
// --></script>
<?php
capture_end($data->scripts);
echo template($data, $model);
?>