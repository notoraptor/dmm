<?php
$db = new Database();
?><h2>Agents</h2>
<h3><a href="index.php?panel=newagent">Créer un nouvel agent</a></h3>
<?php
$agents = $db->agents();
if(!empty($agents)) { ?>
<h3>Gestion des agents</h3>
<div class="models"><?php
foreach($agents as &$agent) {
	$fullName = $agent->first_name().' '.$agent->last_name();
	$photo = null;
	?><div class="model">
		<div class="profilePhoto"<?php if($photo) { ?> style="background-image:url('<?php echo $photo->getURL();?>');"<?php } ?>>
			<?php if($photo) { ?>
			<a href="index.php?panel=agent&id=<?php echo $agent->id();?>"></a>
			<?php } else { ?>
			<div style="color:rgb(240,240,240);">&nbsp;</div>
			<?php } ?>
		</div>
		<div class="editionLinks">
			<div>
                <strong style="font-size:1.2rem;"><a href="index.php?panel=agent&id=<?php echo $agent->id();?>"><?php echo $fullName;?></a></strong>
			</div>
			<div>
				<a style="color:red;"
                   href="index.php?panel=deleteagent&id=<?php echo $agent->id();?>"
                   onclick="return confirm('Voulez-vous vraiment supprimer l\'agent <?php echo $fullName;?> ?');">
                    <strong>Supprimer</strong>
                </a>
			</div>
		</div>
	</div><?php
}
?></div>
<?php }
?>