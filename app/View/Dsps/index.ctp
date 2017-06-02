<?php
	$this->pageTitle = 'Recherche par DSPs';
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<h1><?php echo $this->pageTitle;?></h1>
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
	$pagination = $this->Xpaginator->paginationBlock( 'Personne', $this->passedArgs );
?>

<?php echo $this->Form->create( 'Dsp', array( 'type' => 'post', 'action' => '/index/', 'id' => 'Search', 'class' => ( ( !empty( $this->request->data ) ) ? 'folded' : 'unfolded' ) ) );?>
	<?php
		echo $this->Search->blocAllocataire();
		echo $this->Search->blocAdresse( $mesCodesInsee, $cantons );
	?>
	<fieldset>
		<legend>Recherche par dossier</legend>
		<?php
			echo $this->Form->input( 'Dossier.numdemrsa', array( 'label' => 'Numéro de demande RSA' ) );
			echo $this->Form->input( 'Dossier.matricule', array( 'label' => __d( 'dossier', 'Dossier.matricule' ), 'maxlength' => 15 ) );

			echo $this->Form->input( 'Calculdroitrsa.toppersdrodevorsa', array( 'label' => 'Soumis à Droit et Devoir', 'type' => 'select', 'options' => $options['Calculdroitrsa']['toppersdrodevorsa'], 'empty' => true ) );
			echo $this->Search->natpf( $options['Detailcalculdroitrsa']['natpf'] );

			$valueDossierDernier = isset( $this->request->data['Dossier']['dernier'] ) ? $this->request->data['Dossier']['dernier'] : true;
			echo $this->Form->input( 'Dossier.dernier', array( 'label' => 'Uniquement la dernière demande RSA pour un même allocataire', 'type' => 'checkbox', 'checked' => $valueDossierDernier ) );
			echo $this->Search->etatdosrsa($etatdosrsa);
		?>
	</fieldset>

	<fieldset>
		<legend>Données socio-professionnelles</legend>
		<?php
			if( Configure::read( 'Cg.departement' ) == 93 ) {
				echo $this->Form->input( 'Detaildifsoc.difsoc', array( 'label' => 'Difficultés sociales', 'type' => 'select', 'options' => $options['Detaildifsoc']['difsoc'], 'empty' => true ) );
				echo $this->Form->input( 'Detailaccosocindi.nataccosocindi', array( 'label' => 'Domaine d\'accompagnement individuel', 'type' => 'select', 'options' => $options['Detailaccosocindi']['nataccosocindi'], 'empty' => true ) );
				echo $this->Form->input( 'Detaildifdisp.difdisp', array( 'label' => 'Obstacles à la recherche d\'emploi', 'type' => 'select', 'options' => $options['Detaildifdisp']['difdisp'], 'empty' => true ) );
			}
		?>

		<fieldset>
			<legend>Situation professionnelle</legend>
			<?php
				echo $this->Form->input( 'Dsp.nivetu', array( 'label' => "Quelle est votre niveau d'étude ? ", 'type' => 'select', 'options' => $options['Donnees']['nivetu'], 'empty' => true ) );
				echo $this->Form->input( 'Dsp.hispro', array( 'label' => "Passé professionnel ", 'type' => 'select', 'options' => $options['Donnees']['hispro'], 'empty' => true ) );

				echo $this->Romev3->fieldset( 'Deractromev3', array( 'options' => $options ) );
				echo $this->Form->input( 'Dsp.libsecactderact', array( 'label' => __d( 'dsp', 'Dsp.libsecactderact' ) ) );
				echo $this->Form->input( 'Dsp.libderact', array( 'label' => __d( 'dsp', 'Dsp.libderact' ) ) );

				if( Configure::read( 'Cg.departement' ) == 66 ) {
					echo '<fieldset><legend>Dernière activité (ROME V2)</legend>';
						echo $this->Form->input( 'Dsp.libsecactderact66_secteur_id' , array( 'label' => "Dans quel secteur d'activité avez-vous exercé votre activité professionnelle ? ", 'type' => 'select', 'options' => $options['Coderomesecteurdsp66'], 'empty' => true ) );
						echo $this->Form->input( 'Dsp.libderact66_metier_id' , array( 'label' => "Précisez quelle a été l'activité professionnelle ? ", 'type' => 'select', 'options' => $options['Coderomemetierdsp66'], 'empty' => true ) );
					echo '</fieldset>';
				}

				if( Configure::read( 'Cg.departement' ) != 93 ) {
					echo $this->Romev3->fieldset( 'Deractdomiromev3', array( 'options' => $options ) );
				}

				echo $this->Form->input( 'Dsp.libsecactdomi', array( 'label' => __d( 'dsp', 'Dsp.libsecactdomi' ) ) );
				echo $this->Form->input( 'Dsp.libactdomi', array( 'label' => __d( 'dsp', 'Dsp.libactdomi' ) ) );

				if( Configure::read( 'Cg.departement' ) == 66 ) {
					echo '<fieldset><legend>Dernière activité dominante (ROME V2)</legend>';
						echo $this->Form->input( 'Dsp.libsecactdomi66_secteur_id' , array( 'label' => "Dans quel secteur d'activité avez-vous exercé votre activité professionnelle dominante ? ", 'type' => 'select', 'options' => $options['Coderomesecteurdsp66'], 'empty' => true ) );
						echo $this->Form->input( 'Dsp.libactdomi66_metier_id' , array( 'label' => "Précisez quelle a été l'activité professionnelle dominante ? ", 'type' => 'select', 'options' => $options['Coderomemetierdsp66'], 'empty' => true ) );
					echo '</fieldset>';
				}

				echo $this->Romev3->fieldset( 'Actrechromev3', array( 'options' => $options ) );
				echo $this->Form->input( 'Dsp.libsecactrech', array( 'label' => __d( 'dsp', 'Dsp.libsecactrech' ) ) );
				echo $this->Form->input( 'Dsp.libemploirech', array( 'label' => __d( 'dsp', 'Dsp.libemploirech' ) ) );

				if( Configure::read( 'Cg.departement' ) == 66 ) {
					echo '<fieldset><legend>Emploi recherché (ROME V2)</legend>';
						echo $this->Form->input('Dsp.libsecactrech66_secteur_id' , array('label' => "Quel est le secteur d'activité recherché ? ",  'type' => 'select', 'options' => $options['Coderomesecteurdsp66'], 'empty' => true ) );
						echo $this->Form->input( 'Dsp.libemploirech66_metier_id' , array( 'label' => "Quel est l'emploi recherché ? ", 'type' => 'select', 'options' => $options['Coderomemetierdsp66'], 'empty' => true ) );
					echo '</fieldset>';
				}
			?>
		</fieldset>
	</fieldset>

	<?php
		echo $this->Search->referentParcours( $structuresreferentesparcours, $referentsparcours );
		echo $this->Search->paginationNombretotal();
	?>

	<div class="submit noprint">
		<?php echo $this->Form->button( 'Filtrer', array( 'type' => 'submit' ) );?>
		<?php echo $this->Form->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
	</div>
<?php echo $this->Form->end();?>

<script type="text/javascript">
<?php if ( Configure::read( 'Cg.departement' ) == 66 ):?>
	document.observe("dom:loaded", function() {
 		dependantSelect( 'DspLibderact66MetierId', 'DspLibsecactderact66SecteurId' );
 		try { $( 'DspLibderact66MetierId' ).onchange(); } catch(id) { }

		dependantSelect( 'DspLibactdomi66MetierId', 'DspLibsecactdomi66SecteurId' );
		try { $( 'DspLibactdomi66MetierId' ).onchange(); } catch(id) { }

		dependantSelect( 'DspLibemploirech66MetierId', 'DspLibsecactrech66SecteurId' );
		try { $( 'DspLibemploirech66MetierId' ).onchange(); } catch(id) { }
	} );
<?php endif;?>
</script>

<!-- Résultats -->
<?php if( isset( $dsps ) ):?>

	<h2 class="noprint">Résultats de la recherche</h2>

	<?php if( is_array( $dsps ) && count( $dsps ) > 0  ):?>
		<?php
			// TODO: à factoriser avec Cohortesrendezvous::cohorte() + les exportcsv
			$fields = Hash::normalize( (array)Configure::read( 'Dsps.index.fields' ) );

			$virtualFields = array();
			foreach( $checkboxesVirtualFields as $checkboxVirtualField ) {
				list( $model, $field ) = model_field( $checkboxVirtualField );
				$virtualFields[] = "Donnees.{$field}";
			}

			// On recherche le type de chacun des champs
			foreach( $fields as $fieldName => $params ) {
				$params = (array)$params;
				if( !isset( $params['type'] ) ) {
					$fields[$fieldName]['type'] = $this->Default3->DefaultTable->DefaultTableCell->DefaultData->type( $fieldName );
				}
			}
		?>

		<?php echo $pagination;?>
		<table id="searchResults" class="tooltips">
			<thead>
				<?php
					$headers = (array)Configure::read( 'Dsps.index.header' );
					if( !empty( $headers ) ) {
						echo $this->Html->tableHeaders( $headers );
					}
				?>
				<tr>
					<?php foreach( $fields as $fieldName => $params ):?>
						<?php list( $model, $field ) = model_field( $fieldName );?>
						<?php if( in_array( $model, $sortableModels ) ): ?>
							<?php $label = isset( $params['label'] ) ? $params['label'] : __d( 'dsps', $fieldName ); ?>
							<th><?php echo $this->Xpaginator->sort( $label, $fieldName );?></th>
						<?php else: ?>
							<th><?php echo h( __d( 'dsps', $fieldName ) );?></th>
						<?php endif; ?>
					<?php endforeach;?>

					<th class="action noprint">Actions</th>
					<th class="innerTableHeader noprint">Informations complémentaires</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach( $dsps as $index => $dsp ):?>
					<?php
						$title = $dsp['Dossier']['numdemrsa'];

						// "Traduction" des champs virtuels des cases à cocher pour obtenir des listes
						// TODO: à faire pour l'infobulle
						foreach( $checkboxesVirtualFields as $path ) {
							list( $modelName, $fieldName ) = model_field( $path );

							if( Hash::check( $dsp, "Donnees.{$fieldName}" ) ) {
								$values = vfListeToArray( Hash::get( $dsp, "Donnees.{$fieldName}" ) );

								$cell = '';
								if( !empty( $values ) ) {
									$cell .= '<ul>';
									foreach( $values as $value ) {
										$cell .= '<li>- '.h( value( $options[$modelName][$fieldName], $value ) ).'</li>';
									}
									$cell .= '</ul>';
								}
								$dsp = Hash::insert( $dsp, "Donnees.{$fieldName}", $cell );
							}
						}

						// Infobulle
						$innerTable = $this->Default3->view(
							$dsp,
							(array)Configure::read( 'Dsps.index.innerTable' ),
							array(
								'class' => 'innerTable',
								'id' => "innerTablesearchResults{$index}",
								'options' => $options,
								'th' => true
							)
						);

						if( !empty( $dsp['DspRev']['id'] ) ) {
							$viewLink = $this->Xhtml->viewLink(
								'Voir le dossier « '.$title.' »',
								array( 'controller' => 'dsps', 'action' => 'view_revs', $dsp['DspRev']['id'] ),
								$this->Permissions->check( 'dsps', 'view_revs' )
							);
						}
						else {
							$viewLink = $this->Xhtml->viewLink(
								'Voir le dossier « '.$title.' »',
								array( 'controller' => 'dsps', 'action' => 'view', $dsp['Personne']['id'] ),
								$this->Permissions->check( 'dsps', 'view' )
							);
						}

						$row = array();

						// Choix de la valeur suivant le type du champ
						foreach( $fields as $fieldName => $params ) {
							$value = Hash::get( $dsp, $fieldName );

							if( $params['type'] === 'date' ) {
								$value = date_short( $value );
							}
							else if( Hash::check( $options, $fieldName ) ) {
								$value = h( value( Hash::get( $options, $fieldName ), $value ) );
							}
							else if( !in_array( $fieldName, $virtualFields ) ) {
								$value = h( $value );
							}
							$row[] = $value;
						}

						$row = array_merge(
							$row,
							array(
								array( $viewLink, array( 'class' => 'noprint' ) ),
								array( $innerTable, array( 'class' => 'innerTableCell noprint' ) ),
							)
						);

						echo $this->Xhtml->tableCells(
							$row,
							array( 'class' => 'odd', 'id' => "innerTableTrigger{$index}" ),
							array( 'class' => 'even', 'id' => "innerTableTrigger{$index}" )
						);
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
 					array( 'controller' => 'dsps', 'action' => 'exportcsv' ) + Hash::flatten( $this->request->data, '__' ),
 					$this->Permissions->check( 'dsps', 'exportcsv' )
 				);
			?></li>
		</ul>
	<?php echo $pagination;?>

	<?php else:?>
		<p>Vos critères n'ont retourné aucun dossier.</p>
	<?php endif?>
<?php endif?>

<?php echo $this->Search->observeDisableFormOnSubmit( 'Search' ); ?>
