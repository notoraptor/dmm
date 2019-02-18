<?php
if(!session_id()) session_start();
require_once('server_infos.php');
require_once('priv/utils.php');
require_once('priv/Data.php');
require_once('_template.php');
function get_post($name, $default = '') {return strip_tags(utils_safe_post($name, $default));}
$db = new Database();
$config = $db->config();
$data = new Data();
$data->title = 'Engager | DMM';
$data->pagename = 'engage';
$attention_message = '';
$attention_type = '';
if (!empty($_POST)) {
	$name = get_post('name');
	$email = get_post('email');
	$phone = get_post('phone');
	$subject = get_post('subject');
	$message = get_post('message');
	if (!$name) {
		$attention_message = 'Un nom est requis.';
		$attention_type = 'error';
	} else if (!$subject) {
		$attention_message = 'Un sujet est requis.';
		$attention_type = 'error';
	} else if (!$message) {
		$attention_message = 'Un message est requis.';
		$attention_type = 'error';
	} else if (!$email && !$phone) {
		$attention_message = 'Un téléphone ou un courriel est requis.';
		$attention_type = 'error';
	} else if ($email && !utils_valid_email($email)) {
		$attention_message = 'Courriel invalid.';
		$attention_type = 'error';
	} else {
        $message = str_replace("\r\n", "<br/>", $message);
        $message = str_replace("\n", "<br/>", $message);
        $message = str_replace("\r", "<br/>", $message);
		$email_subject = 'DMM / FORMULAIRE D\'ENGAGEMENT ('.date('d/m/Y - H:i:s').')';
		$body = '';
		capture_start();
		if (!$email)
			$email = '(none)';
		if (!$phone)
			$phone = '(none)';
		?>
		<div>
			<h1><?php echo $email_subject;?></h1>
			<table>
				<tr><td><strong>Nom:</strong></td><td><?php echo $name;?></td></tr>
				<tr><td><strong>Courriel:</strong></td><td><?php echo $email;?></td></tr>
				<tr><td><strong>Téléphone:</strong></td><td><?php echo $phone;?></td></tr>
				<tr><td><strong>Sujet:</strong></td><td><?php echo $subject;?></td></tr>
				<tr><td><strong>Message:</strong></td><td><?php echo $message;?></td></tr>
			</table>
		</div>
		<?php
		capture_end($body);
		$sent = utils_mail($config->site_email(), $email_subject, $body);
		if ($sent) {
			$attention_message = 'Votre requête a été envoyée. Nous vous contacterons bientôt. Merci!';
			$attention_type = 'success';
		} else {
			$attention_message = "Erreur interne pendant l'envoi de votre requête. Veuillez réessayer plus tard!";
			$attention_type = 'error';
		}
	}
}
capture_start();
?>
<div class="engage container">
    <h1>Engager</h1>
	<?php if($attention_message) { ?>
        <div class="p-2 message-<?php echo $attention_type;?>"><?php echo $attention_message;?></div>
	<?php }; ?>
    <form method="post" class="mt-5">
        <fieldset class="form-group">
            <legend>Remplir ce formulaire afin qu'un de nos agents puisse vous contacter</legend>
            <div class="form-row">
                <div class="form-group col-sm">
                    <input type="text" name="name" class="form-control" placeholder="Nom" value="<?php echo utils_s_post('name', '');?>"/>
                </div>
                <div class="form-group col-sm">
                    <input type="email" name="email" class="form-control" placeholder="Courriel" value="<?php echo utils_s_post('email', '');?>"/>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-sm">
                    <input type="text" name="phone" class="form-control" placeholder="Téléphone"  value="<?php echo utils_s_post('phone', '');?>"/>
                </div>
                <div class="form-group col-sm">
                    <input type="text" name="subject" class="form-control" placeholder="Sujet"  value="<?php echo utils_s_post('subject', '');?>"/>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col">
                    <textarea class="form-control" name="message" placeholder="Message"><?php echo utils_s_post('message', '');?></textarea>
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-dark px-4">Send</button>
            </div>
        </fieldset>
    </form>
</div>
<?php
capture_end($data->content);
echo template($data);
?>