<h1><?php echo __d('accueils', 'Accueil.titre'); ?></h1>

<?php
	$count = count($articles);
	if ($count > 0) {
		for ($i = 0; $i < $count; $i++) { ?>
<div class="notice-accueil">
	<?php if (strlen ($articles[$i]['Accueilarticle']['title']) > 0) {?>
	<strong><?php echo $articles[$i]['Accueilarticle']['title']; ?></strong><br />
	<?php } ?>
	<?php echo $articles[$i]['Accueilarticle']['content']; ?>
</div>
<?php
		}
	}
	else {
?>
<p class="notice-accueil">
	<strong><?php echo __d('accueils', 'Accueil.article.aucun'); ?></strong>
</p>
<?php
	}
?>
<br />

<?php
$path = '../View/Accueils/';
$colonnePaire = '<div class="colonne-accueil">';
$colonneImpaire = '<div class="colonne-accueil">';
$compteur = 0;

if (count ($blocs) > 0) {
?>
<p class="notice-accueil">
	<strong><?php echo $libelleReference; ?></strong>
</p>
<?php
	foreach ($blocs as $key => $value) {
		if (isset ($results[$key])) {
			ob_start();
			if (file_exists ($path.$key.$departement.'.ctp')) {
				require_once ($key.$departement.'.ctp');
			}
			else if (file_exists($path.$key.'.ctp')) {
				require_once ($key.'.ctp');
			}

			if ($compteur++%2 == 0) {
				$colonnePaire .= ob_get_contents();
			}
			else {
				$colonneImpaire .= ob_get_contents();
			}
			ob_end_clean();
		}
	}
}

$colonnePaire .= '</div>';
$colonneImpaire .= '</div>';
echo ('<div class="global-colonne-accueil">');
echo ($colonnePaire);
echo ($colonneImpaire);
echo ('</div>');
echo ('<div class="clearer"></div>');
?>