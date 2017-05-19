<?php
	App::uses('WebrsaAccess', 'Utility');
	WebrsaAccess::init($dossierMenu);
    $domain = "actioncandidat_personne_".Configure::read( 'ActioncandidatPersonne.suffixe' );

	$typeFiche = '';
	if( Configure::read( 'Cg.departement' ) == 66 ){
		$typeFiche = 'de candidature';
	}
	else if( Configure::read( 'Cg.departement' ) != 66 ){
		$typeFiche = 'de prescription';
	}
?>

<?php

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( $domain, "ActionscandidatsPersonnes::{$this->action}" )
	);

	echo $this->element( 'ancien_dossier' );
?>

<?php
	if( ( $orientationLiee == 0 ) ) {
		echo '<p class="error">Cette personne ne possède pas d\'orientation. Impossible de créer une fiche '.$typeFiche.'</p>';
	}
	else if( ( $referentLie == 0 ) ) {
		echo '<p class="error">Cette personne ne possède pas de référent lié. Impossible de créer une fiche '.$typeFiche.'</p>';
	}
?>
	<?php if( empty( $actionscandidats_personnes ) ):?>
		<p class="notice">Cette personne ne possède pas de fiche <?php echo $typeFiche;?></p>
	<?php endif;?>

	<?php if( $orientationLiee != 0 && $referentLie != 0 ):?>
		<ul class="actionMenu">
			<?php
				echo '<li>'.$this->Xhtml->addLink(
					'Ajouter une fiche de candidature',
					array( 'controller' => 'actionscandidats_personnes', 'action' => 'add', $personne_id ),
					WebrsaAccess::addIsEnabled('/actionscandidats_personnes/add', $ajoutPossible)
				).' </li>';
			?>
		</ul>
	<?php endif;?>
	<?php if( !empty( $actionscandidats_personnes ) ):?>
			<table class="tooltips default2">
				<thead>
					<tr>
						<th>Intitulé de l'action</th>
						<th>Nom du prescripteur</th>
						<th>Libellé du partenaire</th>
						<th>Date de la signature</th>
					<?php if ((int)Configure::read('Cg.departement') === 66) {?>
						<th>Date du bilan</th>
					<?php } ?>
						<th>Position de la fiche</th>
						<th>Sortie le</th>
						<th>Motif de la sortie</th>
						<th colspan="<?php echo (int)Configure::read('Cg.departement') === 66 ? 7 : 6;?>" class="action">Actions</th>
					</tr>
				</thead>
				<tbody>
	<?php
		foreach( $actionscandidats_personnes as $actioncandidat_personne ){
			$datas = array(
				h( Set::classicExtract( $actioncandidat_personne, 'Actioncandidat.name' ) ),
				h( Set::classicExtract( $actioncandidat_personne, 'Referent.nom_complet' ) ),
				h( Set::classicExtract( $actioncandidat_personne, 'Partenaire.libstruc' ) ),
				h( date_short( Set::classicExtract( $actioncandidat_personne, 'ActioncandidatPersonne.datesignature' ) ) ),
			);
			if ((int)Configure::read('Cg.departement') === 66) {
				$datas[] = h( date_short( Hash::get( $actioncandidat_personne, 'ActioncandidatPersonne.datebilan' ) ) );
			}

			$datas = array_merge(
				$datas,
				array(
					h( Set::classicExtract( $options['ActioncandidatPersonne']['positionfiche'], Set::classicExtract( $actioncandidat_personne, 'ActioncandidatPersonne.positionfiche' ) ) ),
					h( date_short( Set::classicExtract( $actioncandidat_personne, 'ActioncandidatPersonne.sortiele' ) ) ),
					h( Set::classicExtract( $actioncandidat_personne, 'Motifsortie.name' ) ),

					$this->Default2->button(
						'view',
						array( 'controller' => 'actionscandidats_personnes', 'action' => 'view',
						$actioncandidat_personne['ActioncandidatPersonne']['id'] ),
						array(
							'title' => 'Voir la fiche',
							'enabled' => WebrsaAccess::isEnabled($actioncandidat_personne, '/actionscandidats_personnes/view')
						)
					),
					$this->Default2->button(
						'edit',
						array( 'controller' => 'actionscandidats_personnes', 'action' => 'edit',
						$actioncandidat_personne['ActioncandidatPersonne']['id'] ),
						array(
							'title' => 'Modifier la fiche',
							'enabled' => WebrsaAccess::isEnabled($actioncandidat_personne, '/actionscandidats_personnes/edit')
						)
					),
					$this->Default2->button(
						'cancel',
						array( 'controller' => 'actionscandidats_personnes', 'action' => 'cancel',
						$actioncandidat_personne['ActioncandidatPersonne']['id'] ),
						array(
							'title' => 'Annuler la fiche',
							'enabled' => WebrsaAccess::isEnabled($actioncandidat_personne, '/actionscandidats_personnes/cancel')
						)
					),
					$this->Default2->button(
						'print',
						array( 'controller' => 'actionscandidats_personnes', 'action' => 'printFiche',
						$actioncandidat_personne['ActioncandidatPersonne']['id'] ),
						array(
							'title' => 'Imprimer la fiche de candidature',
							'enabled' => WebrsaAccess::isEnabled($actioncandidat_personne, '/actionscandidats_personnes/printFiche')
						)
					),
				),
				(int)Configure::read('Cg.departement') === 66 ? array(
					$this->Default2->button(
						'email',
						array( 'controller' => 'actionscandidats_personnes', 'action' => 'maillink', $actioncandidat_personne['ActioncandidatPersonne']['id'] ),
						array(
							'label' => 'Envoi mail partenaire',
							'title' => 'Envoi Mail Partenaire',
							'enabled' => WebrsaAccess::isEnabled($actioncandidat_personne, '/actionscandidats_personnes/maillink')
						)
					),
				) : array(),
				array(
					$this->Default2->button(
						'filelink',
						array( 'controller' => 'actionscandidats_personnes', 'action' => 'filelink',
						$actioncandidat_personne['ActioncandidatPersonne']['id'] ),
						array(
							'label' => 'Fichiers liés ('
								.$actioncandidat_personne['ActioncandidatPersonne']['nb_fichiers_lies'].')',
							'title' => 'Lier des fichiers',
							'enabled' => WebrsaAccess::isEnabled($actioncandidat_personne, '/actionscandidats_personnes/filelink')
						)
					)
				)
			);

			echo $this->Xhtml->tableCells( $datas, array( 'class' => 'odd' ), array( 'class' => 'even' ) );
		}

	?>
				</tbody>
			</table>
<?php  endif;?>