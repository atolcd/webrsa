<?php
	$this->pageTitle = 'Recherche par CER';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js', 'prototype.maskedinput.js' ) );
	}

	$departement = Configure::read( 'Cg.departement' );
?>
<h1><?php
    echo $this->pageTitle;
//    $this->set('title_for_layout', $this->pageTitle);
?></h1>

<script type="text/javascript">
	document.observe("dom:loaded", function() {
		<?php if( $departement == 58 ): ?>
			new MaskedInput( '#ContratinsertionDureeEngag', '9?9' );
		<?php endif;?>

		observeDisableFieldsetOnCheckbox( 'ContratinsertionCreated', $( 'ContratinsertionCreatedFromDay' ).up( 'fieldset' ), false );

		observeDisableFieldsetOnCheckbox( 'ContratinsertionDdCi', $( 'ContratinsertionDdCiFromDay' ).up( 'fieldset' ), false );

		observeDisableFieldsetOnCheckbox( 'ContratinsertionDfCi', $( 'ContratinsertionDfCiFromDay' ).up( 'fieldset' ), false );

		dependantSelect( 'ContratinsertionReferentId', 'ContratinsertionStructurereferenteId' );
	});
</script>

<?php
	if( is_array( $this->request->data ) ) {
		echo '<ul class="actionMenu"><li>'.$this->Xhtml->link(
			$this->Xhtml->image(
				'icons/application_form_magnify.png',
				array( 'alt' => '' )
			).' Formulaire',
			'#',
			array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "$( 'Search' ).toggle(); return false;" )
		).'</li></ul>';
	}
?>
<?php $pagination = $this->Xpaginator->paginationBlock( 'Contratinsertion', $this->passedArgs );?>
<?php echo $this->Form->create( 'Critereci', array( 'type' => 'post', 'action' => '/index/', 'id' => 'Search', 'class' => ( ( is_array( $this->request->data ) && !empty( $this->request->data['Contratinsertion']['recherche'] ) ) ? 'folded' : 'unfolded' ) ) );?>
	<?php
		echo $this->Search->blocAllocataire( $trancheage );
		echo $this->Search->blocAdresse( $mesCodesInsee, $cantons );
	?>
	<fieldset>
		<legend>Recherche par dossier</legend>
		<?php
			echo $this->Form->input( 'Dossier.numdemrsa', array( 'label' => 'Numéro de demande RSA' ) );
			echo $this->Form->input( 'Dossier.matricule', array( 'label' => __d( 'dossier', 'Dossier.matricule' ), 'maxlength' => 15 ) );
			echo $this->Search->natpf( $natpf );

			$valueDossierDernier = isset( $this->request->data['Dossier']['dernier'] ) ? $this->request->data['Dossier']['dernier'] : true;
			echo $this->Form->input( 'Dossier.dernier', array( 'label' => 'Uniquement la dernière demande RSA pour un même allocataire', 'type' => 'checkbox', 'checked' => $valueDossierDernier ) );
			echo $this->Search->etatdosrsa( $etatdosrsa );

			echo $this->Search->referentParcours( $structuresreferentesparcours, $referentsparcours );
		?>
	</fieldset>

	<fieldset>
		<legend>Recherche par CER</legend>
			<?php
				$valueContratinsertionDernier = isset( $this->request->data['Contratinsertion']['dernier'] ) ? $this->request->data['Contratinsertion']['dernier'] : false;
				echo $this->Form->input( 'Contratinsertion.dernier', array( 'label' => 'Uniquement le dernier contrat d\'insertion pour un même allocataire', 'type' => 'checkbox', 'checked' => $valueContratinsertionDernier ) );
			?>
			<?php echo $this->Form->input( 'Contratinsertion.recherche', array( 'label' => false, 'type' => 'hidden', 'value' => true ) );?>
			<?php if($departement != 58 ){
					echo $this->Form->input( 'Contratinsertion.forme_ci', array(  'type' => 'radio', 'options' => $forme_ci, 'legend' => 'Forme du contrat', 'div' => false, ) );
				}
			?>

			<?php echo $this->Form->input( 'Contratinsertion.created', array( 'label' => 'Filtrer par date de saisie du contrat', 'type' => 'checkbox' ) );?>
			<fieldset>
				<legend>Date de saisie du contrat</legend>
				<?php
					$created_from = Set::check( $this->request->data, 'Contratinsertion.created_from' ) ? Set::extract( $this->request->data, 'Contratinsertion.created_from' ) : strtotime( '-1 week' );
					$created_to = Set::check( $this->request->data, 'Contratinsertion.created_to' ) ? Set::extract( $this->request->data, 'Contratinsertion.created_to' ) : strtotime( 'now' );
				?>
				<?php echo $this->Form->input( 'Contratinsertion.created_from', array( 'label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 120, 'selected' => $created_from ) );?>
				<?php echo $this->Form->input( 'Contratinsertion.created_to', array( 'label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 120,  'maxYear' => date( 'Y' ) + 5, 'selected' => $created_to ) );?>
			</fieldset>

			<?php echo $this->Form->input( 'Contratinsertion.structurereferente_id', array( 'label' => ( $departement == 93 ) ? 'Structure établissant le CER' : __d( 'rendezvous', 'Rendezvous.lib_struct' ), 'type' => 'select', 'options' => $struct, 'empty' => true ) ); ?>
			<?php echo $this->Form->input( 'Contratinsertion.referent_id', array( 'label' => ( $departement == 93 ) ? 'Personne établissant le CER' : __( 'Nom du référent' ), 'type' => 'select', 'options' => $referents, 'empty' => true ) ); ?>
			<?php
				if( $departement == 93 ) {
					echo $this->Form->input( 'Cer93.positioncer', array( 'label' => 'Statut du contrat', 'type' => 'select', 'options' => (array)Hash::get( $options, 'Cer93.positioncer' ), 'empty' => true ) );
				}
				else {
					echo $this->Form->input( 'Contratinsertion.decision_ci', array( 'label' => 'Statut du contrat', 'type' => 'select', 'options' => $decision_ci, 'empty' => true ) );
				}

				if( $departement == 66 ) {
					echo $this->Form->input( 'Contratinsertion.positioncer', array( 'label' => 'Position du contrat', 'type' => 'select', 'options' => $numcontrat['positioncer'], 'empty' => true ) );
				}

				if( $departement == 58 ) {
					echo $this->Form->input( 'Contratinsertion.duree_engag', array( 'label' => 'Filtrer par durée du CER', 'type' => 'text' ) );
				}
				else {
					echo $this->Form->input( 'Contratinsertion.duree_engag', array( 'label' => 'Filtrer par durée du CER', 'type' => 'select', 'empty' => true, 'options' => $duree_engag ) );
				}

				if( $departement == 93 ) {
					// 1. Partie "Expériences professionnelles significatives"
					echo $this->Html->tag(
						'fieldset',
						$this->Html->tag( 'legend', __d( 'criteresci', 'Expprocer93Expprocer93' ) )
						.$this->Romev3->fieldset( 'Expprocer93', array( 'options' => array( 'Expprocer93' => $options['Catalogueromev3'] ), 'domain' => 'criteresci' ) )
						.$this->Html->tag(
							'fieldset',
							$this->Html->tag( 'legend', __d( 'criteresci', 'Expprocer93Insee' ) )
							.$this->Xform->input( 'Expprocer93.secteuracti_id', array( 'type' => 'select', 'options' => (array)Hash::get( $options, 'Expprocer93.secteuracti_id' ), 'empty' => true, 'domain' => 'criteresci' ) )
							.$this->Xform->input( 'Expprocer93.metierexerce_id', array( 'type' => 'select', 'options' => (array)Hash::get( $options, 'Expprocer93.metierexerce_id' ), 'empty' => true, 'domain' => 'criteresci' ) )
						)
					);

					// 2. Partie "Emploi trouvé"
					echo $this->Html->tag(
						'fieldset',
						$this->Html->tag( 'legend', __d( 'criteresci', 'Emptrouvromev3Emptrouvromev3' ) )
						.$this->Romev3->fieldset( 'Emptrouvromev3', array( 'options' => array( 'Emptrouvromev3' => $options['Catalogueromev3'] ), 'domain' => 'criteresci' ) )
						.$this->Html->tag(
							'fieldset',
							$this->Html->tag( 'legend', __d( 'criteresci', 'Emptrouvromev3Insee' ) )
							.$this->Xform->input( 'Cer93.secteuracti_id', array( 'type' => 'select', 'options' => (array)Hash::get( $options, 'Expprocer93.secteuracti_id' ), 'empty' => true, 'domain' => 'criteresci' ) )
							.$this->Xform->input( 'Cer93.metierexerce_id', array( 'type' => 'select', 'options' => (array)Hash::get( $options, 'Expprocer93.metierexerce_id' ), 'empty' => true, 'domain' => 'criteresci' ) )
						)
					);

					echo $this->Html->tag(
						'fieldset',
						$this->Html->tag( 'legend', 'Filtrer par ce sur quoi le contrat porte' )
						.$this->Form->input( 'Cer93Sujetcer93.sujetcer93_id', array( 'label' => 'Sujet du CER', 'type' => 'select', 'options' => (array)Hash::get( $options, 'Cer93Sujetcer93.sujetcer93_id' ), 'empty' => true ) )
						.$this->Form->input( 'Cer93Sujetcer93.soussujetcer93_id', array( 'label' => 'Sous sujet du CER', 'type' => 'select', 'options' => (array)Hash::get( $options, 'Cer93Sujetcer93.soussujetcer93_id' ), 'empty' => true ) )
						.$this->Form->input( 'Cer93Sujetcer93.valeurparsoussujetcer93_id', array( 'label' => 'Valeur par sous sujet du CER', 'type' => 'select', 'options' => (array)Hash::get( $options, 'Cer93Sujetcer93.valeurparsoussujetcer93_id' ), 'empty' => true ) )
						.$this->Romev3->fieldset( 'Sujetromev3', array( 'options' => array( 'Sujetromev3' => $options['Catalogueromev3'] ) ) )
					);

					// Activation / désactivation de la partie "Votre contrat porte sur l'emploi (ROME v.3)" en fonciton des réponses à "Votre contrat porte sur"
					$activationPath = Configure::read( 'Cer93.Sujetcer93.Romev3.path' );
					$activationValues = (array)Configure::read( 'Cer93.Sujetcer93.Romev3.values' );

					$activationSujetcer93 = ( 'Sujetcer93.Sujetcer93.{n}.sujetcer93_id' === $activationPath );
					$activationSoussujetcer93 = ( 'Sujetcer93.Sujetcer93.{n}.soussujetcer93_id' === $activationPath );
					$activationIds = array();

					if( $activationSujetcer93 ) {
						$master = 'Cer93Sujetcer93.sujetcer93_id';
						$activationIds = $activationValues;
					}
					else if( $activationSoussujetcer93 ) {
						$master = 'Cer93Sujetcer93.soussujetcer93_id';
						foreach( array_keys( $options['Cer93Sujetcer93']['soussujetcer93_id'] ) as $soussujetcer93_id ) {
							if( in_array( suffix( $soussujetcer93_id ), $activationValues ) ) {
								$activationIds[] = $soussujetcer93_id;
							}
						}
					}

					if( $activationSujetcer93 || $activationSoussujetcer93 ) {
						echo $this->Observer->disableFieldsetOnValue(
							$master,
							'Sujetromev3FieldsetId',
							$activationIds,
							false,
							true
						);
					}
				}

                echo $this->Search->date( 'Contratinsertion.datevalidation_ci', 'Date de validation du contrat' );
            ?>

			<!-- Contratinsertion sur la date de début du CER -->
			<?php echo $this->Form->input( 'Contratinsertion.dd_ci', array( 'label' => 'Filtrer par date de début du contrat', 'type' => 'checkbox' ) );?>
			<fieldset>
				<legend>Date de début du contrat</legend>
				<?php
					$dd_ci_from = Set::check( $this->request->data, 'Contratinsertion.dd_ci_from' ) ? Set::extract( $this->request->data, 'Contratinsertion.dd_ci_from' ) : strtotime( '-1 week' );
					$dd_ci_to = Set::check( $this->request->data, 'Contratinsertion.dd_ci_to' ) ? Set::extract( $this->request->data, 'Contratinsertion.dd_ci_to' ) : strtotime( 'now' );
				?>
				<?php echo $this->Form->input( 'Contratinsertion.dd_ci_from', array( 'label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => 58 == $departement ? date( 'Y' ) + 3 : date( 'Y' ), 'minYear' => date( 'Y' ) - 120, 'selected' => $dd_ci_from ) );?>
				<?php echo $this->Form->input( 'Contratinsertion.dd_ci_to', array( 'label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'minYear' => date( 'Y' ) - 120,  'maxYear' => date( 'Y' ) + 5, 'selected' => $dd_ci_to ) );?>
			</fieldset>

			<!-- Contratinsertion sur la date de fin du CER -->
			<?php echo $this->Form->input( 'Contratinsertion.df_ci', array( 'label' => 'Filtrer par date de fin du contrat', 'type' => 'checkbox' ) );?>
			<fieldset>
				<legend>Date de fin du contrat</legend>
				<?php
					$df_ci_from = Set::check( $this->request->data, 'Contratinsertion.df_ci_from' ) ? Set::extract( $this->request->data, 'Contratinsertion.df_ci_from' ) : strtotime( '-1 week' );
					$df_ci_to = Set::check( $this->request->data, 'Contratinsertion.df_ci_to' ) ? Set::extract( $this->request->data, 'Contratinsertion.df_ci_to' ) : strtotime( 'now' );
				?>
				<?php echo $this->Form->input( 'Contratinsertion.df_ci_from', array( 'label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => 58 == $departement ? date( 'Y' ) + 3 : date( 'Y' ), 'minYear' => date( 'Y' ) - 120, 'selected' => $df_ci_from ) );?>
				<?php echo $this->Form->input( 'Contratinsertion.df_ci_to', array( 'label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'minYear' => date( 'Y' ) - 120,  'maxYear' => date( 'Y' ) + 5, 'selected' => $df_ci_to ) );?>
			</fieldset>

			<?php
				$params = (
					58 == $departement
					? array(
						'minYear_from' => date( 'Y' ) - 120,
						'maxYear_from' => date( 'Y' ) + 3,
						'minYear_to' => date( 'Y' ) - 120,
						'maxYear_to' => date( 'Y' ) + 5,
					)
					: array(
						'minYear_from' => date( 'Y' ) - 120,
						'maxYear_from' => date( 'Y' ),
						'minYear_to' => date( 'Y' ) - 120,
						'maxYear_to' => date( 'Y' ) + 5,
					)
				);
				echo $this->Search->date( 'Contratinsertion.periode_validite', 'Période de validité', $params );
			?>

			<?php
				echo $this->Form->input( 'Contratinsertion.arriveaecheance', array( 'label' => 'Allocataire dont le CER est arrivé à échéance', 'type' => 'checkbox' )  );
				echo $this->Form->input(
					'Contratinsertion.echeanceproche',
					array(
						'label' => sprintf(
							'CER arrivant à échéance (se terminant sous %s)',
							localized_interval( Configure::read( 'Criterecer.delaiavanteecheance' ), array( 'precision' => 'd' ) )
						),
						'type' => 'checkbox'
					)
				);
			?>

			<?php if( $departement == 66 ) {
					$nbjours = Configure::read( 'Criterecer.delaidetectionnonvalidnotifie' );
					$nbjoursTranslate = str_replace('days','jours', $nbjours);

					echo $this->Form->input( 'Contratinsertion.notifienonvalide', array( 'label' => 'CER non validé et notifié il y a '.$nbjoursTranslate, 'type' => 'checkbox' )  );
					echo $this->Form->input( 'Contratinsertion.istacitereconduction', array( 'label' => 'Hors tacite reconduction', 'type' => 'checkbox' )  );
				}
			?>
	</fieldset>
	<fieldset>
		<legend>Filtrer par dernière orientation</legend>
		<?php
			if( $departement == 58 ) {
				echo $this->Form->input( 'Personne.etat_dossier_orientation', array( 'label' => __d( 'personne', 'Personne.etat_dossier_orientation' ), 'type' => 'select', 'options' => (array)Hash::get( $options, 'Personne.etat_dossier_orientation' ), 'empty' => true ) );
			}
			echo $this->Form->input( 'Orientstruct.typeorient', array( 'label' => 'Type d\'orientation', 'type' => 'select', 'empty' => true, 'options' => $typesorients )  );
			if( $departement == 66 ) {
				echo $this->Form->input( 'TypeorientAExclure.id', array( 'label' => 'Type d\'orientation à exclure', 'type' => 'select', 'multiple' => 'checkbox', 'options' => $typesorientsNiveau0 )  );
			}
		?>
	</fieldset>
	<?php echo $this->Search->paginationNombretotal( 'Contratinsertion.paginationNombreTotal' );?>
	<div class="submit noprint">
		<?php echo $this->Form->button( 'Rechercher', array( 'type' => 'submit' ) );?>
		<?php echo $this->Form->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
	</div>

<?php echo $this->Form->end();?>

<!-- Résultats -->
<?php if( isset( $contrats ) ):?>

	<h2 class="noprint">Résultats de la recherche</h2>
	<?php
		$domain_search_plugin = ( $departement == 93 ) ? 'search_plugin_93' : 'search_plugin';
	?>

	<?php if( is_array( $contrats ) && count( $contrats ) > 0  ):?>

		<?php echo $pagination;?>
		<table id="searchResults" class="tooltips">
			<thead>
				<tr>
					<th><?php echo $this->Xpaginator->sort( 'Nom de l\'allocataire', 'Personne.nom' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Commune de l\'allocataire', 'Adresse.nomcom' );?></th>
					<th><?php echo $this->Xpaginator->sort( ( $departement == 93 ) ? 'Personne établissant le CER' : 'Référent lié', 'Referent.nom_complet' );?></th>
					<th><?php echo $this->Xpaginator->sort( __d( 'dossier', 'Dossier.matricule' ), 'Dossier.matricule' );?></th>
					<?php if( $departement == 93 ):?>
						<th><?php echo $this->Xpaginator->sort( 'Type d\'orientation', 'Typeorient.lib_type_orient' );?></th>
					<?php endif;?>
					<th><?php echo $this->Xpaginator->sort( 'Date de saisie du contrat', 'Contratinsertion.created' );?></th>
					<?php if( $departement == 93 ):?>
						<th><?php echo $this->Xpaginator->sort( 'Durée du contrat', 'Contratinsertion.duree_engag' );?></th>
					<?php endif;?>
					<th><?php echo $this->Xpaginator->sort( 'Rang du contrat', 'Contratinsertion.rg_ci' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Décision', 'Contratinsertion.decision_ci' ).$this->Xpaginator->sort( ' ', 'Contratinsertion.datevalidation_ci' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Forme du CER', 'Contratinsertion.forme_ci' );?></th>
					<?php if( $departement != 93 ):?>
                        <th><?php echo $this->Xpaginator->sort( 'Position du CER', 'Contratinsertion.positioncer' );?></th>
                    <?php endif;?>
					<th><?php echo $this->Xpaginator->sort( 'Date de fin du contrat', 'Contratinsertion.df_ci' );?></th>
					<th class="action noprint">Actions</th>
					<th class="innerTableHeader noprint">Informations complémentaires</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$controller = 'contratsinsertion';
					if( $departement == 93 ) {
						$controller = 'cers93';
					}
					foreach( $contrats as $index => $contrat ):?>
					<?php
						$title = $contrat['Dossier']['numdemrsa'];
//debug($contrat);
						/***/
						$position = Set::classicExtract( $contrat, 'Contratinsertion.positioncer' );
						$datenotif = Set::classicExtract( $contrat, 'Contratinsertion.datenotification' );
						if( empty( $datenotif ) ) {
							$positioncer = Set::enum( Set::classicExtract( $contrat, 'Contratinsertion.positioncer' ), $numcontrat['positioncer'] );
						}
						else if( !empty( $datenotif ) && in_array( $position, array( 'nonvalid', 'encours' ) ) ){
							$positioncer = Set::enum( Set::classicExtract( $contrat, 'Contratinsertion.positioncer' ), $numcontrat['positioncer'] ).' le '.date_short( $datenotif );
						}
						else {
							$positioncer = Set::enum( Set::classicExtract( $contrat, 'Contratinsertion.positioncer' ), $numcontrat['positioncer'] );
						}

						$innerTableParCg = '';
						if( $departement == 58 ) {
							$innerTableParCg .= '<tr>
								<th>'.__d( 'personne', 'Personne.etat_dossier_orientation' ).'</th>
								<td>'.h( value( (array)Hash::get( $options, 'Personne.etat_dossier_orientation' ), Hash::get( $contrat, 'Personne.etat_dossier_orientation' ) ) ).'</td>
							</tr>';
						}

						$innerTable = '<table id="innerTablesearchResults'.$index.'" class="innerTable">
							<tbody>
							<!-- <tr>
									<th>Commune de naissance</th>
									<td>'.$contrat['Personne']['nomcomnai'].'</td>
								</tr> -->
								<tr>
									<th>Date de naissance</th>
									<td>'.date_short( $contrat['Personne']['dtnai'] ).'</td>
								</tr>
								<tr>
									<th>Code INSEE</th>
									<td>'.$contrat['Adresse']['numcom'].'</td>
								</tr>
								<tr>
									<th>NIR</th>
									<td>'.$contrat['Personne']['nir'].'</td>
								</tr>
								<tr>
									<th>Rôle</th>
									<td>'.value( $rolepers, Set::classicExtract( $contrat, 'Prestation.rolepers' ) ).'</td>
								</tr>
								<tr>
									<th>État du dossier</th>
									<td>'.value( $etatdosrsa, Set::classicExtract( $contrat, 'Situationdossierrsa.etatdosrsa' ) ).'</td>
								</tr>
								<tr>
									<th>'.__d( $domain_search_plugin, 'Structurereferenteparcours.lib_struc' ).'</th>
									<td>'.Hash::get( $contrat, 'Structurereferenteparcours.lib_struc' ).'</td>
								</tr>
								<tr>
									<th>'.__d( $domain_search_plugin, 'Referentparcours.nom_complet' ).'</th>
									<td>'.Hash::get( $contrat, 'Referentparcours.nom_complet' ).'</td>
								</tr>
								'.$innerTableParCg.'
							</tbody>
						</table>';

                        if( $departement != 93 ) {
                            echo $this->Xhtml->tableCells(
                                array(
                                    h( $contrat['Personne']['nom'].' '.$contrat['Personne']['prenom'] ),
                                    h( $contrat['Adresse']['nomcom'] ),
                                    h( @$contrat['Referent']['nom_complet'] ),
                                    h( $contrat['Dossier']['matricule'] ),
                                    h( $this->Locale->date( 'Date::short', Set::extract( $contrat, 'Contratinsertion.date_saisi_ci' ) ) ),
                                    h( $contrat['Contratinsertion']['rg_ci'] ),
                                    h( Set::extract( $decision_ci, Set::extract( $contrat, 'Contratinsertion.decision_ci' ) ).' '.$this->Locale->date( 'Date::short', Set::extract( $contrat, 'Contratinsertion.datevalidation_ci' ) ) ),//date_short($contrat['Contratinsertion']['datevalidation_ci']) ),
                                    h( Set::enum( $contrat['Contratinsertion']['forme_ci'], $forme_ci ) ),
                                    h( $positioncer ),
                                    h( $this->Locale->date( 'Date::short', Set::extract( $contrat, 'Contratinsertion.df_ci' ) ) ),
                                    array(
                                        $this->Xhtml->viewLink(
                                            'Voir le dossier « '.$title.' »',
                                            array( 'controller' => $controller, 'action' => 'index', $contrat['Contratinsertion']['personne_id'] )
                                        ),
                                        array( 'class' => 'noprint' )
                                    ),
                                    array( $innerTable, array( 'class' => 'innerTableCell noprint' ) ),
                                ),
                                array( 'class' => 'odd', 'id' => 'innerTableTrigger'.$index ),
                                array( 'class' => 'even', 'id' => 'innerTableTrigger'.$index )
                            );
                        }
                        else {
							$lib_type_orient = Hash::get( $contrat, 'Typeorient.lib_type_orient' );
							$duree = Hash::get( $contrat, 'Cer93.duree' );
							if( empty( $duree ) ) {
								$duree = $contrat['Contratinsertion']['duree_engag'];
							}
							$duree = "{$duree} mois";

							echo $this->Xhtml->tableCells(
                                array(
                                    h( $contrat['Personne']['nom'].' '.$contrat['Personne']['prenom'] ),
                                    h( $contrat['Adresse']['nomcom'] ),
                                    h( @$contrat['Referent']['nom_complet'] ),
                                    h( $contrat['Dossier']['matricule'] ),
									h( empty( $lib_type_orient ) ? 'Non orienté' : $lib_type_orient ),
                                    h( $this->Locale->date( 'Date::short', Set::extract( $contrat, 'Contratinsertion.created' ) ) ),
									h( $duree ),
                                    h( $contrat['Contratinsertion']['rg_ci'] ),
                                    h(
										Hash::get( $options['Cer93']['positioncer'], Hash::get( $contrat, 'Cer93.positioncer' ) )
										.(
											Hash::get( $contrat, 'Contratinsertion.decision_ci' ) == 'V'
											? ' '.$this->Locale->date( 'Date::short', Hash::get( $contrat, 'Contratinsertion.datedecision' ) )
											: ''
										)
									),
                                    h( Set::enum( $contrat['Contratinsertion']['forme_ci'], $forme_ci ) ),
                                    h( $this->Locale->date( 'Date::short', Set::extract( $contrat, 'Contratinsertion.df_ci' ) ) ),
                                    array(
                                        $this->Xhtml->viewLink(
                                            'Voir le dossier « '.$title.' »',
                                            array( 'controller' => $controller, 'action' => 'index', $contrat['Contratinsertion']['personne_id'] ),
											$this->Permissions->check( $controller, 'index' ) && !Hash::get( $contrat, 'Contratinsertion.horszone' )
                                        ),
                                        array( 'class' => 'noprint' )
                                    ),
                                    array( $innerTable, array( 'class' => 'innerTableCell noprint' ) ),
                                ),
                                array( 'class' => 'odd', 'id' => 'innerTableTrigger'.$index ),
                                array( 'class' => 'even', 'id' => 'innerTableTrigger'.$index )
                            );
                        }
					?>
				<?php endforeach;?>
			</tbody>
		</table>
		<ul class="actionMenu">
			<li><?php
				echo $this->Xhtml->printLinkJs(
					'Imprimer le tableau',
					array( 'onclick' => 'printit(); return false;', 'class' => 'noprint' )
				);
			?></li>
			<li><?php
				echo $this->Xhtml->exportLink(
					'Télécharger le tableau',
					array( 'controller' => 'criteresci', 'action' => 'exportcsv' ) + Hash::flatten( $this->request->data, '__' ),
					( $this->Permissions->check( 'criteresci', 'exportcsv' ) )
				);
			?></li>
		</ul>
	<?php echo $pagination;?>

	<?php else:?>
		<p class="notice">Vos critères n'ont retourné aucun dossier.</p>
	<?php endif?>

<?php endif?>

<?php
	if( $departement == 93 ) {
		echo $this->Observer->dependantSelect(
			array(
				'Cer93Sujetcer93.sujetcer93_id' => 'Cer93Sujetcer93.soussujetcer93_id',
				'Cer93Sujetcer93.soussujetcer93_id' => 'Cer93Sujetcer93.valeurparsoussujetcer93_id',
			)
		);
	}

	echo $this->Search->observeDisableFormOnSubmit( 'Search' );
?>