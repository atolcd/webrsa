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
<h1>Données du flux Pôle Emploi</h1>

<ul id="" class="ui-tabs-nav">
<?php
	foreach ($personnes as $key => $value) {
?>
	<li class="tab">
		<a href="/fluxpoleemplois/personne/<?php echo $foyer_id; ?>/<?php echo $key; ?>" class="<?php echo ($personne_id == $key ? 'active' : ''); ?>"><?php echo $value; ?></a>
	</li>
<?php
	}
?>
</ul>
<div class="tab" style="overflow: hidden;">
	<div class="colonne-accueil gauche">
<?php
	if (isset ($blocs['individu']) && $blocs['individu']) {
?>
		<h2><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.individu' )); ?></h2>
		<table class="index details" style="width: 95%;">
			<tbody>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.individu_nir' )); ?></th>
					<td class="data string " style="width: 70%"><?php echo (isset ($donnees['Informationpe']['nir']) ? $donnees['Informationpe']['nir'] : ''); ?></td>
				</tr>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.individu_nom_marital' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['individu_nom_marital']) ? $donnees['Informationpe']['individu_nom_marital'] : ''); ?></td>
				</tr>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.individu_nom_naissance' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['nom']) ? $donnees['Informationpe']['nom'] : ''); ?></td>
				</tr>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.individu_prenom' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['prenom']) ? $donnees['Informationpe']['prenom'] : ''); ?></td>
				</tr>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.individu_date_naissance' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['dtnai']) ? $donnees['Informationpe']['dtnai'] : ''); ?></td>
				</tr>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.individu_certification_identite' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['individu_certification_identite']) ? $donnees['Informationpe']['individu_certification_identite'] : ''); ?></td>
				</tr>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.individu_commune_residence' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['individu_commune_residence']) ? $donnees['Informationpe']['individu_commune_residence'] : ''); ?></td>
				</tr>
			</tbody>
		</table>
		<br />
<?php
	}

	if (isset ($blocs['inscription']) && $blocs['inscription']) {
?>
		<h2><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.inscription' )); ?></h2>
		<table class="index details" style="width: 95%;">
			<tbody>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.inscription_date_debut_ide' )); ?></th>
					<td class="data string " style="width: 70%"><?php echo (isset ($donnees['Informationpe']['inscription_date_debut_ide']) ? $donnees['Informationpe']['inscription_date_debut_ide'] : ''); ?></td>
				</tr>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.inscription_code_categorie' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['inscription_code_categorie']) ? $donnees['Informationpe']['inscription_code_categorie'] : ''); ?></td>
				</tr>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.inscription_lib_categorie' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['inscription_lib_categorie']) ? $donnees['Informationpe']['inscription_lib_categorie'] : ''); ?></td>
				</tr>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.inscription_code_situation' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['inscription_code_situation']) ? $donnees['Informationpe']['inscription_code_situation'] : ''); ?></td>
				</tr>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.inscription_lib_situation' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['inscription_lib_situation']) ? $donnees['Informationpe']['inscription_lib_situation'] : ''); ?></td>
				</tr>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.inscription_date_cessation_ide' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['inscription_date_cessation_ide']) ? $donnees['Informationpe']['inscription_date_cessation_ide'] : ''); ?></td>
				</tr>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.inscription_motif_cessation_ide' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['inscription_motif_cessation_ide']) ? $donnees['Informationpe']['inscription_motif_cessation_ide'] : ''); ?></td>
				</tr>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.inscription_lib_cessation_ide' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['inscription_lib_cessation_ide']) ? $donnees['Informationpe']['inscription_lib_cessation_ide'] : ''); ?></td>
				</tr>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.inscription_date_radiation_ide' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['inscription_date_radiation_ide']) ? $donnees['Informationpe']['inscription_date_radiation_ide'] : ''); ?></td>
				</tr>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.inscription_motif_radiation_ide' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['inscription_motif_radiation_ide']) ? $donnees['Informationpe']['inscription_motif_radiation_ide'] : ''); ?></td>
				</tr>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.inscription_lib_radiation_ide' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['inscription_lib_radiation_ide']) ? $donnees['Informationpe']['inscription_lib_radiation_ide'] : ''); ?></td>
				</tr>
			</tbody>
		</table>
		<br />
<?php
	}

	if (isset ($blocs['formation']) && $blocs['formation']) {
?>
		<h2><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.formation' )); ?></h2>
		<table class="index details" style="width: 95%;">
			<tbody>
				<tr class="odd">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.formation_code_niveau' )); ?></th>
					<td class="data string " style="width: 70%"><?php echo (isset ($donnees['Informationpe']['formation_code_niveau']) ? $donnees['Informationpe']['formation_code_niveau'] : ''); ?></td>
				</tr>
				<tr class="even">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.formation_lib_niveau' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['formation_lib_niveau']) ? $donnees['Informationpe']['formation_lib_niveau'] : ''); ?></td>
				</tr>
				<tr class="even">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.formation_code_secteur' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['formation_code_secteur']) ? $donnees['Informationpe']['formation_code_secteur'] : ''); ?></td>
				</tr>
				<tr class="even">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.formation_lib_secteur' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['formation_lib_secteur']) ? $donnees['Informationpe']['formation_lib_secteur'] : ''); ?></td>
				</tr>
			</tbody>
		</table>
		<br />
<?php
	}

	if (isset ($blocs['romev3']) && $blocs['romev3']) {
?>
		<h2><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.romev3' )); ?></h2>
		<table class="index details" style="width: 95%;">
			<tbody>
				<tr class="odd">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.romev3_code_rome' )); ?></th>
					<td class="data string " style="width: 70%"><?php echo (isset ($donnees['Informationpe']['romev3_code_rome']) ? $donnees['Informationpe']['romev3_code_rome'] : ''); ?></td>
				</tr>
				<tr class="even">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.romev3_lib_rome' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['romev3_lib_rome']) ? $donnees['Informationpe']['romev3_lib_rome'] : ''); ?></td>
				</tr>
			</tbody>
		</table>
<?php
	}
?>
	</div>
	<div class="colonne-accueil droite">
<?php
	if (isset ($blocs['allocataire']) && $blocs['allocataire']) {
?>
		<h2><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.allocataire' )); ?></h2>
		<table class="index details" style="width: 95%;">
			<tbody>
				<tr class="odd">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.allocataire_identifiant_caf' )); ?></th>
					<td class="data string " style="width: 70%"><?php echo (isset ($donnees['Informationpe']['allocataire_identifiant_caf']) ? $donnees['Informationpe']['allocataire_identifiant_caf'] : ''); ?></td>
				</tr>
				<tr class="even">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.allocataire_identifiant_msa' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['allocataire_identifiant_msa']) ? $donnees['Informationpe']['allocataire_identifiant_msa'] : ''); ?></td>
				</tr>
				<tr class="odd">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.allocataire_code_pe' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['allocataire_code_pe']) ? $donnees['Informationpe']['allocataire_code_pe'] : ''); ?></td>
				</tr>
				<tr class="even">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.allocataire_identifiant_pe' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['allocataire_identifiant_pe']) ? $donnees['Informationpe']['allocataire_identifiant_pe'] : ''); ?></td>
				</tr>
			</tbody>
		</table>
		<br />
<?php
	}

	if (isset ($blocs['structure_principale']) && $blocs['structure_principale']) {
?>
		<h2><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.structure_principale' )); ?></h2>
		<table class="index details" style="width: 95%;">
			<tbody>
				<tr class="odd">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.suivi_structure_principale_nom' )); ?></th>
					<td class="data string " style="width: 70%"><?php echo (isset ($donnees['Informationpe']['suivi_structure_principale_nom']) ? $donnees['Informationpe']['suivi_structure_principale_nom'] : ''); ?></td>
				</tr>
				<tr class="even">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.suivi_structure_principale_voie' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['suivi_structure_principale_voie']) ? $donnees['Informationpe']['suivi_structure_principale_voie'] : ''); ?></td>
				</tr>
				<tr class="odd">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.suivi_structure_principale_complement' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['suivi_structure_principale_complement']) ? $donnees['Informationpe']['suivi_structure_principale_complement'] : ''); ?></td>
				</tr>
				<tr class="even">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.suivi_structure_principale_code_postal' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['suivi_structure_principale_code_postal']) ? $donnees['Informationpe']['suivi_structure_principale_code_postal'] : ''); ?></td>
				</tr>
				<tr class="odd">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.suivi_structure_principale_cedex' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['suivi_structure_principale_cedex']) ? $donnees['Informationpe']['suivi_structure_principale_cedex'] : ''); ?></td>
				</tr>
				<tr class="even">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.suivi_structure_principale_bureau' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['suivi_structure_principale_bureau']) ? $donnees['Informationpe']['suivi_structure_principale_bureau'] : ''); ?></td>
				</tr>
			</tbody>
		</table>
		<br />
<?php
	}

	if (isset ($blocs['structure_deleguee']) && $blocs['structure_deleguee']) {
?>
		<h2><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.structure_deleguee' )); ?></h2>
		<table class="index details" style="width: 95%;">
			<tbody>
				<tr class="odd">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.suivi_structure_deleguee_nom' )); ?></th>
					<td class="data string " style="width: 70%"><?php echo (isset ($donnees['Informationpe']['suivi_structure_deleguee_nom']) ? $donnees['Informationpe']['suivi_structure_deleguee_nom'] : ''); ?></td>
				</tr>
				<tr class="even">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.suivi_structure_deleguee_voie' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['suivi_structure_deleguee_voie']) ? $donnees['Informationpe']['suivi_structure_deleguee_voie'] : ''); ?></td>
				</tr>
				<tr class="odd">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.suivi_structure_deleguee_complement' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['suivi_structure_deleguee_complement']) ? $donnees['Informationpe']['suivi_structure_deleguee_complement'] : ''); ?></td>
				</tr>
				<tr class="even">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.suivi_structure_deleguee_code_postal' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['suivi_structure_deleguee_code_postal']) ? $donnees['Informationpe']['suivi_structure_deleguee_code_postal'] : ''); ?></td>
				</tr>
				<tr class="odd">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.suivi_structure_deleguee_cedex' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['suivi_structure_deleguee_cedex']) ? $donnees['Informationpe']['suivi_structure_deleguee_cedex'] : ''); ?></td>
				</tr>
				<tr class="even">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.suivi_structure_deleguee_bureau' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['suivi_structure_deleguee_bureau']) ? $donnees['Informationpe']['suivi_structure_deleguee_bureau'] : ''); ?></td>
				</tr>
			</tbody>
		</table>
		<br />
<?php
	}

	if (isset ($blocs['ppae']) && $blocs['ppae']) {
?>
		<h2><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.ppae' )); ?></h2>
		<table class="index details" style="width: 95%;">
			<tbody>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.ppae_conseiller_pe' )); ?></th>
					<td class="data string " style="width: 70%"><?php echo (isset ($donnees['Informationpe']['ppae_conseiller_pe']) ? $donnees['Informationpe']['ppae_conseiller_pe'] : ''); ?></td>
				</tr>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.ppae_date_signature' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['ppae_date_signature']) ? $donnees['Informationpe']['ppae_date_signature'] : ''); ?></td>
				</tr>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.ppae_date_notification' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['ppae_date_notification']) ? $donnees['Informationpe']['ppae_date_notification'] : ''); ?></td>
				</tr>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.ppae_axe_code' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['ppae_axe_code']) ? $donnees['Informationpe']['ppae_axe_code'] : ''); ?></td>
				</tr>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.ppae_axe_libelle' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['ppae_axe_libelle']) ? $donnees['Informationpe']['ppae_axe_libelle'] : ''); ?></td>
				</tr>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.ppae_modalite_code' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['ppae_modalite_code']) ? $donnees['Informationpe']['ppae_modalite_code'] : ''); ?></td>
				</tr>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.ppae_modalite_libelle' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['ppae_modalite_libelle']) ? $donnees['Informationpe']['ppae_modalite_libelle'] : ''); ?></td>
				</tr>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th><?php echo (__d( 'fluxpoleemplois', 'Fluxpoleemplois.ppae_date_dernier_ent' )); ?></th>
					<td class="data string "><?php echo (isset ($donnees['Informationpe']['ppae_date_dernier_ent']) ? $donnees['Informationpe']['ppae_date_dernier_ent'] : ''); ?></td>
				</tr>
			</tbody>
		</table>
<?php
	}
?>
	</div>
</div>