<?php
require_once('../priv/videodetection.php');

function get_photo_field($title, $name, $photo_name, $photo_getter) {
	if (isset($_FILES[$name]) && $_FILES[$name]['name']) {
		$previous = $photo_getter();
		$uploaded = utils_upload($name, DIR_DB(), $photo_name);
		$error = $uploaded['error'];
		if(!$error) {
			utils_message_add_success('Mise à jour: '.$title);
			if ($previous && $previous != $photo_getter())
				unlink($previous);
		} else
			utils_message_add_error('Erreur interne: impossible de mettre à jour: '.$title.'. '.$error);
	}
}

$db = new Database();
if(!empty($_POST)) {
	$site_email = utils_safe_post('site_email');
	$link_facebook = hex2bin(utils_safe_post('link_facebook'));
	$link_instagram = hex2bin(utils_safe_post('link_instagram'));
	$home_text_left = utils_safe_post('home_text_left');
	$home_text_right = utils_safe_post('home_text_right');
	$home_text_bottom = utils_safe_post('home_text_bottom');
	$contact_text = utils_safe_post('contact_text');
	$submission_title = utils_safe_post('submission_title');
	$submission_text = utils_safe_post('submission_text');
	$submission_bottom_photo_text = utils_safe_post('submission_bottom_photo_text');

	get_photo_field('Home photo 1', 'home_photo_1', utils_home_photo_1_name(), 'utils_home_photo_1');
	get_photo_field('Home photo 2', 'home_photo_2', utils_home_photo_2_name(), 'utils_home_photo_2');
	get_photo_field('Submission photo', 'submission_photo', utils_submission_photo_name(), 'utils_submission_photo');
	get_photo_field('Submission bottom photo', 'submission_bottom_photo', utils_submission_bottom_photo_name(), 'utils_submission_bottom_photo');
	get_photo_field('Contact unique photo', 'contact_unique_photo', utils_contact_unique_photo_name(), 'utils_contact_unique_photo');

	$contact_video = hex2bin(utils_safe_post('contact_video'));
	if($contact_video && (!utils_valid_url($contact_video) || !Video::parse($contact_video)))
		utils_message_add_error("Le lien vidéo est invalide.");
	else if ($link_instagram && !utils_valid_url($link_instagram))
	    utils_message_add_error('Le lien instagram est invalide.');
	else if ($link_facebook && !utils_valid_url($link_facebook))
	    utils_message_add_error('Le lien facebook est invalide.');
	else if ($site_email && !utils_valid_email($site_email)) {
		utils_message_add_error("Le courriel est invalide.");
    }
	else {
		$db->config_update(array(
		        'site_email' => $site_email,
		        'link_facebook' => $link_facebook,
		        'link_instagram' => $link_instagram,
		        'home_text_left' => $home_text_left,
		        'home_text_right' => $home_text_right,
		        'home_text_bottom' => $home_text_bottom,
		        'contact_text' => $contact_text,
		        'submission_title' => $submission_title,
		        'submission_text' => $submission_text,
		        'submission_bottom_photo_text' => $submission_bottom_photo_text,
		        'contact_video' => $contact_video,
        ));
		utils_message_add_success("La configuration du site a été mise à jour.");
	}
}
$config = $db->config();
if(!$config) die("Erreur interne: impossible de charger la configuration du site.");
$_POST = array(
	'contact_video' => utils_encoded_url_from_post('contact_video', $config->contact_video()),
	'site_email' => utils_safe_post('site_email', $config->site_email()),
	'link_facebook' => utils_encoded_url_from_post('link_facebook', $config->link_facebook()),
	'link_instagram' => utils_encoded_url_from_post('link_instagram', $config->link_instagram()),
	'home_text_left' => utils_safe_post('home_text_left', $config->home_text_left()),
	'home_text_right' => utils_safe_post('home_text_right', $config->home_text_right()),
	'home_text_bottom' => utils_safe_post('home_text_bottom', $config->home_text_bottom()),
	'contact_text' => utils_safe_post('contact_text', $config->contact_text()),
	'submission_title' => utils_safe_post('submission_title', $config->submission_title()),
	'submission_text' => utils_safe_post('submission_text', $config->submission_text()),
	'submission_bottom_photo_text' => utils_safe_post('submission_bottom_photo_text', $config->submission_bottom_photo_text()),
);

function add_photo_field($title, $name, $current_photo) {
	echo utils_input($title, $name, 'file');
	if ($current_photo) {
		echo '<div class="row">'.
			'<div class="cell name">['.$title.'] current</div>'.
			'<div class="cell value">'.
			'<img style="max-width: 200px; max-height: 200px" src="'.utils_as_link($current_photo).'"/>'.
			'</div>'.
			'</div>';
	}
}

?>
<div class="configuration">
<form method="post" onsubmit="wrap();" enctype="multipart/form-data">
<fieldset>
	<legend>Configuration du site</legend>
    <div><input type="submit" value="Mettre &agrave; jour"/></div>
	<div class="table">
		<?php
		echo utils_input('Site email','site_email', 'email');
		echo input_url("Instagram link", 'link_instagram');
		echo input_url("Facebook link", 'link_facebook');
		echo utils_textarea('Home text left','home_text_left');
		echo utils_textarea('Home text right','home_text_right');
		echo utils_textarea('Home text bottom','home_text_bottom');
		echo utils_textarea('Contact text','contact_text');
		echo input_url("Contact video link", 'contact_video');
		echo utils_textarea('Details page middle title','submission_title');
		echo utils_textarea('Details page text','submission_text');
		echo utils_textarea('Details page title','submission_bottom_photo_text');

		add_photo_field('Details page photo', 'submission_bottom_photo', utils_submission_bottom_photo());
		add_photo_field('Submission photo', 'submission_photo', utils_submission_photo());
		add_photo_field('Home photo 1', 'home_photo_1', utils_home_photo_1());
		add_photo_field('Home photo 2', 'home_photo_2', utils_home_photo_2());
		add_photo_field('Contact unique photo', 'contact_unique_photo', utils_contact_unique_photo());
		?>
	</div>
	<div><input type="submit" value="Mettre &agrave; jour"/></div>
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
            careful(['contact_video', 'link_facebook', 'link_instagram']);
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