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
$data = new Data();
$contact_video = $config->contact_video();

capture_start();
?>
<div class="contacts">
    <p><?php echo $config->contact_text();?></p>
	<?php if ($contact_video) {
		$video_info = get_video_codes($contact_video);
		echo $video_info[0];
		$data->scripts .= $video_info[1];
	} ?>
    <div class="photos">
	<?php foreach ($photos_lines as $line) {
		?><div class="row">
		<?php foreach ($line as $photo) {
			?>
            <div class="col">
                <div class="photo">
                    <img class="img-fluid" src="<?php echo $photo->getURL();?>"/>
                </div>
            </div>
			<?php
		} ?>
        </div><?php
	} ?>
    </div>
    <div class="agents">
		<?php foreach($agents as $agent) {
			?>
            <div class="agent">
                <div class="name"><?php echo $agent->full_name();?></div>
                <div class="role"><?php echo $agent->role();?></div>
                <div class="email"><?php echo $agent->email();?></div>
            </div>
			<?php
		} ?>
    </div>
    <a class="btn btn-outline-dark" href="submission.php">for model submission</a>
</div>
<?php
capture_end($data->content);

$data->title = 'DMM';
$data->pagename = 'contacts';
echo template($data);
?>

