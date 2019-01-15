<?php
$db = new Database();
$agent = false;

function get_post($name, $default = '') {return strip_tags(utils_safe_post($name, $default));}

if(!empty($_POST)) {
    $first_name = get_post('first_name');
    $last_name = get_post('last_name');
    $role = get_post('role');
    $email = get_post('email');
	$agent = null;
	$values = array(
		'first_name'=> $first_name,
		'last_name'=> $last_name,
		'role'=> $role,
		'email'=> $email
	);
	if($first_name == '') utils_message_add_error('Veuillez indiquer un prénom !');
	else if($last_name == '') utils_message_add_error('Veuillez indiquer un nom !');
	else if($role == '') utils_message_add_error('Veuillez indiquer un rôle !');
	else if(!utils_valid_email($email))
		utils_message_add_error("Le courriel est invalide.");
	else {
		$agent = $db->agent_create($values);
		if(!$agent)
		    utils_message_add_error('Ce prénom et ce nom correspondent déjà à un agent.');
		else
		    utils_message_add_success('L\'agent '.$first_name.' '.$last_name.' a été créé. <a href="index.php?panel=newagent">Créer un autre agent.</a>');
	}
	if (!$agent) {
		$_POST = $values;
	}
} else {
    $_POST = array();
}
if($agent) {
	utils_request_redirection('index.php?panel=agent&id='.$agent->id());
} else {
?><h2><a href="index.php?panel=agents">Agents</a> / Créer un nouvel agent</h2>
<div class="newagent">
<form method="post">
<fieldset>
<legend>Création d'un nouvel agent.</legend>
<p>Veuillez créer un nouvel agent en définissant les infos élémentaires. Vous pourrez ensuite modifier toutes les données de l'agent après sa création.</p>
<div class="table">
	<?php
	$help = '(navigateurs récents) appuyez sur la touche BAS dans le champ pour afficher des valeurs prédéfinies proposées.';
	echo utils_required_input('Prénom', 'first_name', 'text', '');
	echo utils_required_input('Nom', 'last_name', 'text', '');
	echo utils_input('Rôle', 'role', 'text', 'list="current-roles"', $help);
	echo utils_input('Courriel', 'email', 'email');
	echo utils_datalist('current-hairs', $db->list_roles());
	?>
</div>
<input type="submit" value="créer l'agent"/>
</fieldset>
</form>
</div>
<?php } ?>