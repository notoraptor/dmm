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
$contact_unique_photo = utils_contact_unique_photo();

capture_start();
?>
<div class="contacts pb-5">
    <div class="elements">
        <div>
            <video id="vid" loop autoplay controls muted>
                <source src="data/videos/ULTRA-MEGA-FINAL-BOUNCE-DMM.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
        <div class="agents row my-5 element">
			<?php foreach($agents as $agent) {
				?>
                <div class="agent my-4 col-md">
                    <div class="name"><?php echo $agent->full_name();?></div>
                    <div class="role"><?php echo $agent->role();?></div>
					<?php if ($agent->email()) { ?><div class="email"><a target="_blank" href="mailto:<?php echo $agent->email();?>"><?php echo $agent->email();?></a></div><?php } ?>
                </div>
				<?php
			} ?>
        </div>
        <div class="my-5 py-5 text-center">
            <a class="btn btn-dark btn-lg no-border-radius" href="submission.php">for model submission</a>
        </div>
        <?php
        if ($contact_unique_photo) {
            ?>
            <div class="element" id="contact-unique-photo-wrapper">
                <img id="contact-unique-photo" class="img-fluid" src="<?php echo utils_as_link($contact_unique_photo);?>"/>
            </div>
            <?php
        }
        ?>
    </div>
</div>
<?php
capture_end($data->content);

$data->title = 'Contact | DMM';
$data->pagename = 'contact';
echo template($data);
?>

