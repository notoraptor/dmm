<?php
if(!session_id()) session_start();
require_once('server_infos.php');
require_once('priv/utils.php');
require_once('priv/Data.php');
require_once('priv/videodetection.php');
require_once('_template.php');

$db = new Database();
$contact_photos = $db->contact_photos();
$agents = $db->agents();
$photos_lines = utils_array_to_lines($contact_photos, 4);
$config = $db->config();
$data = new Data($db);
$contact_video = $config->contact_video();
$contact_submission_photo = utils_contact_submission_photo();
$contact_unique_photo = utils_contact_unique_photo();

capture_start();
?>
<div class="contact pb-5">
    <div>
        <video id="vid" loop autoplay>
            <source src="data/videos/ULTRA-MEGA-FINAL-BOUNCE-DMM.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
    <div class="agents row text-center py-5 px-5 mx-5">
        <?php foreach($agents as $agent) {
            ?>
            <div class="agent col-md pt-5">
                <div class="name"><?php echo $agent->full_name();?></div>
                <div class="role"><?php echo $agent->role();?></div>
                <?php if ($agent->email()) { ?><div class="email"><a target="_blank" href="mailto:<?php echo $agent->email();?>"><?php echo $agent->email();?></a></div><?php } ?>
            </div>
            <?php
        } ?>
    </div>
    <?php
    if ($contact_submission_photo) {
        ?>
        <div class="contact-submission-photo mt-5 text-center">
            <img class="img-fluid" src="<?php echo utils_as_link($contact_submission_photo);?>"/>
            <div class="button-submission-wrapper">
                <div class="my-table">
                    <div class="my-cell">
                        <a class="button btn btn-outline-dark btn-lg" href="submission.php">for model submission</a>
                    </div>
                </div>
            </div>
        </div>
        <?php
    } else {
		?>
        <div class="text-center pt-5">
            <a class="btn btn-dark btn-lg no-border-radius" href="submission.php">for model submission</a>
        </div>
		<?php
    }
	if ($contact_unique_photo) {
		?>
        <div id="bottom" class="text-center my-5 py-5">
            <img id="bottom-photo" class="img-fluid w-25" src="<?php echo utils_as_link($contact_unique_photo);?>"/>
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
function onScroll() {
    const navBar = document.getElementsByClassName('navigation-wrapper')[0];
    const agents = document.getElementsByClassName('agents')[0];
    if (window.pageYOffset >= agents.offsetTop) {
        navBar.classList.add('sticked');
    } else {
        navBar.classList.remove('sticked');
    }
}
function onBottomImageHover() {
    const image = document.getElementById('bottom-photo');
    if (image) {
        image.onmouseover = function () {
            image.classList.remove('w-25');
            image.classList.add('w-100');
        };
        image.onmouseout = function () {
            image.classList.remove('w-100');
            image.classList.add('w-25');
        };
    }
};
window.onscroll = onScroll;
onScroll();
onBottomImageHover();
//--></script>
<?php
capture_end($data->scripts);

$data->title = 'Contact | DMM';
$data->pagename = 'contact';
$data->content_class = 'container-fluid';
echo template($data);
?>

