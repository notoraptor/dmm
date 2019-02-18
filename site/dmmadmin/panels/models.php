<?php
$db = new Database();
?><h2>Modèles</h2>
<h3><a href="index.php?panel=newmodel">Créer un nouveau modèle</a></h3>
<?php
$models = $db->models();
usort($models, function($a, $b) {
	$coll = collator_create( 'fr_FR' );
	return collator_compare( $coll, $a->full_name(), $b->full_name() );
	// return strcmp($a->full_name(), $b->full_name());
});
if(!empty($models)) { ?>
<h3>Gestion des modèles</h3>
<div class="models"><?php
foreach($models as &$model) {
	$fullName = $model->first_name().' '.$model->last_name();
	$photos = $model->photos();
	$photo = null;
	if (count($photos)) {
	    $photo = $photos[0];
    }
	?><div class="model">
		<div class="profilePhoto"<?php if($photo) { ?> style="background-image:url('<?php echo $photo->getURL();?>');"<?php } ?>>
			<?php if($photo) { ?>
			<a href="index.php?panel=model&id=<?php echo $model->id();?>"></a>
			<?php } else { ?>
			<div style="color:rgb(240,240,240);">&nbsp;</div>
			<?php } ?>
		</div>
		<div class="editionLinks">
			<div>
                <strong style="font-size:1.2rem;"><a href="index.php?panel=model&id=<?php echo $model->id();?>"><?php echo $fullName;?></a></strong>
				<?php if($model->instagram_link()) { ?>
				<span class="instalink"><a target="_blank" href="<?php echo $model->instagram_link();?>"><img src="<?php echo server_http()?>/data/utils/instagram-gold.svg"/></a></span>
				<?php } ?>
			</div>
			<div><a href="index.php?panel=modelportfolio&id=<?php echo $model->id();?>">Portfolio</a></div>
			<div>
				<a style="color:red;"
                   href="index.php?panel=deletemodel&id=<?php echo $model->id();?>"
                   onclick="return confirm('Voulez-vous vraiment supprimer le modèle <?php echo $fullName;?> ?');">
                    <strong>Supprimer</strong>
                </a>
			</div>
		</div>
	</div><?php
}
?></div>
<?php }
?>