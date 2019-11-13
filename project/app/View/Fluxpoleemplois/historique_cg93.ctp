<?php
	$this->pageTitle = 'Données du flux Pôle Emploi';
	$rowCnt = 0;

	// On récupère les profils en configuration
	$configurationProfils = Configure::read('Profil.Fluxpoleemplois.access');
	// On récupère le profil par défaut
	$blocs = $configurationProfils['by-default'];
	// On récupère le profil de l'utilisateur
	$profil = $this->Session->read( 'Auth.User.Group.code' );
	// Si le profil de l'utilisateur est configuré on le prend sinon on laisse le profil par défaut
	if (isset ($configurationProfils[$profil])) {
		$blocs = $accueil[$profil];
	}
?>
<h1><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.historique.titre' )); ?></h1>

<ul id="" class="ui-tabs-nav" style="margin-top: 20px;">
<?php
	foreach ($personnes as $key => $value) {
?>
	<li class="tab">
		<a href="/fluxpoleemplois/historique/<?php echo $foyer_id; ?>/<?php echo $key; ?>" class="<?php echo ($personne_id == $key ? 'active' : ''); ?>"><?php echo $value; ?></a>
	</li>
<?php
	}
?>
</ul>
<div class="tab" style="overflow: hidden;">
<?php
	$etatActuel = true;
	if (!is_null($historiques)) {
		foreach ($historiques as $historique) {
			$date = new DateTime ($historique['Historiqueetatpe']['date']);
			$dateAffichage = $date->format ('d/m/Y');

			$intituleEtat = __d( 'fluxpoleemplois', 'Fluxpoleemplois.historique.etat' );
			if ($etatActuel) {
				$intituleEtat = __d( 'fluxpoleemplois', 'Fluxpoleemplois.historique.etat_actuel' );
			}
			$etatActuel = false;
?>
		<h2><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.historique' ).$dateAffichage); ?></h2>
		<table class="index details" style="width: 95%;">
			<tbody>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.historique.date' )); ?></th>
					<td class="data string " style="width: 70%;"><?php echo ($dateAffichage); ?></td>
				</tr>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th><?php echo ($intituleEtat); ?></th>
					<td class="data string "><?php echo ($historique['Historiqueetatpe']['etat']); ?></td>
				</tr>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.historique.motif' )); ?></th>
					<td class="data string "><?php echo ($historique['Historiqueetatpe']['motif']); ?></td>
				</tr>
			</tbody>
		</table>
		<br />
<?php
		}
	}
?>
</div>