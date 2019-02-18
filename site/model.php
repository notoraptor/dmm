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
$data->content_class = 'model';
capture_start();
?>
<div class="wrapper d-flex">
    <div class="header pl-5 d-flex">
        <div class="logo-div"><a class="logo" href="index.php"><img src="data/main/dmm_logo_cropped.png"/></a></div>
        <div class="info flex-grow-1 d-flex">
            <div class="info-inner align-self-center">
                <h1><?php echo $model->first_name();?></h1>
                <h2><?php echo $model->hint();?></h2>
                <div class="buttons">
                    <?php if ($model->instagram_link()) { echo '<a target="_blank" class="button btn btn-outline-dark" href="'.$model->instagram_link().'">INSTAGRAM</a>'; } ?>
                    <?php if (utils_model_card($model->id())) { echo '<a target="_blank" class="button btn btn-outline-dark" href="'.utils_as_link(utils_model_card($model->id())).'">MODEL CARD</a>'; } ?>
                    <?php if ($model->video_link()) { echo '<a target="_blank" class="button btn btn-outline-dark" href="'.$model->video_link().'">CLIP</a>'; } ?>
                    <a target="_blank" class="button btn btn-outline-dark" href="engage.php?id=<?php echo $model->id();?>">ENGAGER</a>
                </div>
            </div>
        </div>
    </div>
    <div class="photos">
        <?php
        if ($model->photos()) {
            foreach ($model->photos() as $photo) {
                ?><img class="mx-2" src="<?php echo $photo->getURL();?>"/><?php
            }
        } ?>
    </div>
</div>
<div class="details d-flex align-items-end">
    <?php
    $details = array();
    if ($model->hauteur()) $details[] = array('hauteur', $model->hauteur());
    if ($model->taille()) $details[] = array('taille', $model->taille());
    if ($model->taille_poitrine()) $details[] = array('poitrine', $model->taille_poitrine());
    if ($model->taille_hanches()) $details[] = array('hanches', $model->taille_hanches());
    if ($model->taille_chaussures()) $details[] = array('chaussures', $model->taille_chaussures());
    if ($model->yeux()) $details[] = array('yeux', $model->yeux());
    if ($model->cheveux()) $details[] = array('cheveux', $model->cheveux());
    for ($i = 0; $i < count($details); ++$i) {
        $detail = $details[$i];
        $detail_name = $detail[0];
        $detail_value = $detail[1];
        ?>
        <div class="detail px-2">
            <span class="name"><?php echo $detail_name; ?></span>
            <span class="value"><?php echo $detail_value; ?></span>
        </div>
        <?php
    }
    ?>
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