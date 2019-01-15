<?php
function get_post($name, $default = '') {return strip_tags(utils_safe_post($name, $default));}
$db = new Database();
if(!utils_has_s_get('id')) utils_request_redirection('index.php?panel=agents');
else {
$id = utils_s_get('id');
$agent = $db->agent($id);
if(!$agent) utils_request_redirection('index.php?panel=agents');
else {
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
		$agent = $db->agent_update($id, $values);
		if(!$agent)
			utils_message_add_error('Ce prénom et ce nom correspondent déjà à un agent.');
		else
			utils_message_add_success('L\'agent '.$first_name.' '.$last_name.' a été modifié.');
	}
}
$_POST = $agent->to_post();

$fullName = $agent->full_name();
$profilePhoto = null;
?>
<div class="modelEdition">
<div class="table breadcumbs">
	<div class="cell main">
		<h2><a href="index.php?panel=agents">Agents</a> / <?php echo $fullName;?></h2>
		<p>
            <a style="color:red;"
               href="index.php?panel=deleteagent&id=<?php echo $agent->id();?>"
               onclick="return confirm('Voulez-vous vraiment supprimer l\'agent <?php echo $fullName;?> ?');">
                <strong>Supprimer cet agent</strong>
            </a>
		</p>
	</div>
	<div class="cell photo"><?php if($profilePhoto) { ?><img src="<?php echo $profilePhoto['url'];?>"/><?php } ?></div>
</div>
<form method="post">
<fieldset>
<legend>Modifier ses infos.</legend>
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
<p><input type="submit" value="modifier cet agent"/></p>
</fieldset>
</form>
</div>
<?php
}
}
 ?>