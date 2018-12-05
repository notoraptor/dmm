<?php
require_once('../priv/videodetection.php');
$db = new Database();
if(!empty($_POST)) {
	$home_text_left = utils_safe_post('home_text_left');
	$home_text_right = utils_safe_post('home_text_right');
	$home_text_bottom = utils_safe_post('home_text_bottom');
	$contact_text = utils_safe_post('contact_text');
	$submission_title = utils_safe_post('submission_title');
	$submission_text = utils_safe_post('submission_text');
	$submission_bottom_photo_text = utils_safe_post('submission_bottom_photo_text');
	if (isset($_FILES['submission_bottom_photo']) && $_FILES['submission_bottom_photo']['name']) {
	    $previous = utils_submission_bottom_photo();
	    $uploaded = utils_upload('submission_bottom_photo', DIR_DB(), utils_submission_bottom_photo_name());
		$error = $uploaded['error'];
	    if(!$error) {
			utils_message_add_success('La nouvelle photo en base de la page de soumission a été mise en ligne.');
			if ($previous)
			    unlink($previous);
		} else
			utils_message_add_error('Erreur interne: impossible de mettre en ligne la nouvelle photo en bas de l page de soumission. '.$error);
    }
	$contact_video = hex2bin(utils_safe_post('contact_video'));
	if($contact_video && (!utils_valid_url($contact_video) || !Video::parse($contact_video)))
		utils_message_add_error("Le lien vidéo est invalide.");
	else {
		$db->config_update(array(
		        'home_text_left' => $home_text_left,
		        'home_text_right' => $home_text_right,
		        'home_text_bottom' => $home_text_bottom,
		        'contact_text' => $contact_text,
		        'submisssion_title' => $submission_title,
		        'submission_text' => $submission_text,
		        'submission_bottom_photo_text' => $submission_bottom_photo_text,
		        'contact_video' => $contact_video,
        ));
		utils_message_add_success("La configuration du site a été mise à jour.");
	}
}
$config = $db->config();
if(!$config) die("Erreur interne: impossible de charger la configuration du site.");
$post_video_link = utils_safe_post('contact_video');
if ($post_video_link)
    $post_video_link = hex2bin($post_video_link);
else
    $post_video_link = $config->contact_video();
$_POST = array(
	'contact_video' => $post_video_link,
	'home_text_left' => utils_safe_post('home_text_left', $config->home_text_left()),
	'home_text_right' => utils_safe_post('home_text_right', $config->home_text_right()),
	'home_text_bottom' => utils_safe_post('home_text_bottom', $config->home_text_bottom()),
	'contact_text' => utils_safe_post('contact_text', $config->contact_text()),
	'submission_title' => utils_safe_post('submission_title', $config->submission_title()),
	'submission_text' => utils_safe_post('submission_text', $config->submission_text()),
	'submission_bottom_photo_text' => utils_safe_post('submission_bottom_photo_text', $config->submission_bottom_photo_text()),
);
?>
<div class="configuration">
<form method="post" onsubmit="wrap();" enctype="multipart/form-data">
<fieldset>
	<legend>Configuration du site</legend>
	<div class="table">
		<?php
		echo utils_textarea('Home text left','home_text_left');
		echo utils_textarea('Home text right','home_text_right');
		echo utils_textarea('Home text bottom','home_text_bottom');
		echo utils_textarea('Contact text','contact_text');
		echo input_url("Contact video link", 'contact_video');
		echo utils_textarea('Submission title','submission_title');
		echo utils_textarea('Submission text','submission_text');
		echo utils_textarea('Submission bottom photo text','submission_bottom_photo_text');
		echo utils_input('Submission bottom photo', 'submission_bottom_photo', 'file');
		$submission_bottom_photo = utils_submission_bottom_photo();
		if ($submission_bottom_photo) {
		    echo '<div class="row">'.
                '<div class="cell name">Current bottom photo</div>'.
                '<div class="cell value">'.
                    '<img style="max-width: 200px; max-height: 200px" src="'.utils_as_link($submission_bottom_photo).'"/>'.
                '</div>'.
                '</div>';
        }
		?>
	</div>
	<div><input type="submit" value="Mettre à jour"/></div>
    <script type="text/javascript">//<!--
        var textAreas = [
            'home_text_left',
            'home_text_right',
            'home_text_bottom',
            'contact_text',
            'submission_title',
            'submission_text',
            'submission_bottom_photo_text',
        ];
        function wrap() {
            careful(['contact_video']);
            for(let i = 0; i < textAreas.length; ++i) {
                const text_area = document.getElementById(indices[i]);
                text_area.value = text_area.value.trim();
                if(!text_area.value.startsWith('<')) {
                    text_area.value = '<div>' + text_area.value + '</div>';
                }
            }
        }
        //--></script>
    <script src="nicEdit/nicEdit.js" type="text/javascript"></script>
    <script type="text/javascript">//<!--
        bkLib.onDomLoaded(function() {
            for (let index of textAreas)
                loadNiceEditor(index);
        });
        //--></script>
</fieldset>
</form>
</div>