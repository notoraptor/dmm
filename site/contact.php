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
<div class="contacts pt-5 pb-5">
    <div class="row">
        <?php
        if ($contact_video) {
            $video_info = get_video_codes($contact_video);
            $data->scripts .= $video_info[1];
            ?><div class="col-lg-7" style="position: relative;"><?php echo $video_info[0]; ?></div><?php
        }
        if ($contact_unique_photo) {
            ?><div class="col-lg-3"><img class="img-fluid" src="<?php echo utils_as_link($contact_unique_photo);?>"/></div><?php
        }
        ?>
        <div class="agents col-lg-2">
            <?php foreach($agents as $agent) {
                ?>
                <div class="agent my-4">
                    <div class="name"><?php echo $agent->full_name();?></div>
                    <div class="role"><?php echo $agent->role();?></div>
                    <?php if ($agent->email()) { ?><div class="email"><a target="_blank" href="mailto:<?php echo $agent->email();?>"><?php echo $agent->email();?></a></div><?php } ?>
                </div>
                <?php
            } ?>
            <div class="mt-5">
                <a class="button btn btn-outline-dark" href="submission.php">for model submission</a>
            </div>
        </div>
    </div>
</div>
<?php
capture_end($data->content);

$data->title = 'Contact | DMM';
$data->pagename = 'contact';
echo template($data);
?>

