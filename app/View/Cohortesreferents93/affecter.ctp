<?php
	$this->pageTitle = '1. Affectation d\'un référent';
	echo $this->Xhtml->tag( 'h1', $this->pageTitle );

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js' ) );
	}

	echo '<ul class="actionMenu"><li>'.$this->Xhtml->link(
		$this->Xhtml->image(
			'icons/application_form_magnify.png',
			array( 'alt' => '' )
		).' Formulaire',
		'#',
		array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "var form = $$( 'form' ); form = form[0]; $( form ).toggle(); return false;" )
	).'</li></ul>';

	// Moteur de recherche
?>
<?php echo $this->Xform->create( null, array( 'type' => 'post', 'action' => $this->action, 'id' => 'Search', 'class' => ( ( is_array( $this->request->data ) && !empty( $this->request->data ) && isset( $this->request->data['Search']['active'] ) ) ? 'folded' : 'unfolded' ) ) );?>
	<?php echo $this->Xform->input( 'Search.active', array( 'type' => 'hidden', 'value' => true ) );?>
	<fieldset>
		<legend>Recherche par affectation</legend>
		<?php echo $this->Form->input( 'Search.Referent.filtrer', array( 'type' => 'checkbox', 'label' => 'Filtrer par désignation' ) );?>
		<fieldset class="invisible" id="SearchFiltreReferent>">
			<?php
				echo $this->Form->input( 'Search.Referent.designe', array( 'type' => 'radio', 'options' => $options['Referent']['designe'], 'legend' => false, 'separator' => '<br/>' ) );
				echo $this->Form->input( 'Search.PersonneReferent.referent_id', array( 'label' => 'Nom du référent', 'type' => 'select', 'options' => $options['referents'], 'empty' => true ) );
				echo $this->Search->date( 'Search.PersonneReferent.dddesignation', 'Date d\'affectation' );
			?>
		</fieldset>
		<?php
			echo $this->Search->multipleCheckboxChoice( $options['Personne']['situation'], 'Search.Personne.situation' );

			echo $this->Form->input( 'Search.Dossier.transfere', array( 'label' => 'Dossier transféré ?', 'empty' => true, 'options' => $options['exists'] ) );
		?>
	</fieldset>
	<?php
		echo $this->Search->blocAllocataire( array(), array(), 'Search' );
		echo $this->Search->toppersdrodevorsa( $options['toppersdrodevorsa'], 'Search.Calculdroitrsa.toppersdrodevorsa' );
		echo $this->Form->input( 'Search.Dsp.exists', array( 'label' => 'Possède une DSP ?', 'type' => 'select', 'options' => $options['exists'], 'empty' => true ) );
		echo $this->Form->input( 'Search.Contratinsertion.exists', array( 'label' => 'Possède un CER ?', 'type' => 'select', 'options' => $options['exists'], 'empty' => true ) );
		echo $this->Search->date( 'Search.Orientstruct.date_valid' );
// 		echo $this->Form->input( 'Search.Dossier.dernier', array( 'label' => 'Uniquement la dernière demande RSA pour un même allocataire', 'type' => 'checkbox' ) );
		echo $this->Search->blocAdresse( $options['mesCodesInsee'], $options['cantons'], 'Search' );
// 		echo $this->Search->etatdosrsa( $options['etatdosrsa'], 'Search.Situationdossierrsa.etatdosrsa' );
		echo $this->Search->blocDossier( $options['etatdosrsa'], 'Search' );
		echo $this->Search->paginationNombretotal( 'Search.Pagination.nombre_total' );
		echo $this->Search->observeDisableFormOnSubmit( 'Search' );

	?>
	<div class="submit noprint">
		<?php echo $this->Xform->button( 'Rechercher', array( 'type' => 'submit' ) );?>
		<?php echo $this->Xform->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
	</div>
<?php echo $this->Xform->end();?>

<script type="text/javascript">
	document.observe( "dom:loaded", function() {
		observeDisableFieldsetOnCheckbox(
			'SearchReferentFiltrer',
			'SearchFiltreReferent>',
			false
		);
		observeDisableFieldsOnRadioValue(
			'Search',
			'data[Search][Referent][designe]',
			[ 'SearchPersonneReferentReferentId', 'SearchPersonneReferentDddesignation' ],
			[ '1' ],
			true
		);
	} );
</script>

<?php
	// Résultats de la recherche
	if( isset( $personnes_referents ) ) {
		if( empty( $personnes_referents ) ) {
			echo $this->Xhtml->tag( 'p', 'Aucun résultat', array( 'class' => 'notice' ) );
		}
		else {
			$pagination = $this->Xpaginator->paginationBlock( 'Personne', $this->passedArgs );
			echo $pagination;

			echo $this->Xform->create( null, array( 'id' => 'PersonneReferent' ) );
			echo '<table id="searchResults" class="tooltips">';
			echo '<thead>
					<tr>
						<th>Commune</th>
						<th>Date de demande</th>
						<th>Date d\'orientation</th>
						<th>Date de naissance</th>
						<th>Soumis à droits et devoirs</th>
						<th>Présence d\'une DSP</th>
						<th>Nom, prénom</th>
						<th>Rang CER</th>
						<th>État CER</th>
						<th>Date de fin de CER</th>
						<th>Date d\'affectation</th>
						<th>Structure référente source</th>
						<th class="action">Action</th>
						<th class="action">Affectation</th>
						<th class="action">Détails</th>
					</tr>
				</thead>';
			echo '<tbody>';
			foreach( $personnes_referents as $index => $personne_referent ) {

				$innerTable = '<table id="innerTablesearchResults'.$index.'" class="innerTable">
						<tbody>
							<tr>
								<th>N° de dossier</th>
								<td>'.$personne_referent['Dossier']['numdemrsa'].'</td>
							</tr>
							<tr>
								<th>Date ouverture de droit</th>
								<td>'.date_short( $personne_referent['Dossier']['dtdemrsa'] ).'</td>
							</tr>
							<tr>
								<th>Date de naissance</th>
								<td>'.date_short( $personne_referent['Personne']['dtnai'] ).'</td>
							</tr>
							<tr>
								<th>N° CAF</th>
								<td>'.$personne_referent['Dossier']['matricule'].'</td>
							</tr>
							<tr>
								<th>NIR</th>
								<td>'.$personne_referent['Personne']['nir'].'</td>
							</tr>
							<tr>
								<th>Code postal</th>
								<td>'.$personne_referent['Adresse']['codepos'].'</td>
							</tr>
							<tr>
								<th>Date de fin de droit</th>
								<td>'.$personne_referent['Situationdossierrsa']['dtclorsa'].'</td>
							</tr>
							<tr>
								<th>Motif de fin de droit</th>
								<td>'.Set::enum( $personne_referent['Situationdossierrsa']['moticlorsa'], $options['moticlorsa'] ).'</td>
							</tr>
							<tr>
								<th>Rôle</th>
								<td>'.Set::enum( $personne_referent['Prestation']['rolepers'], $options['rolepers'] ).'</td>
							</tr>
							<tr>
								<th>Etat du dossier</th>
								<td>'.Set::classicExtract( $options['etatdosrsa'], $personne_referent['Situationdossierrsa']['etatdosrsa'] ).'</td>
							</tr>
							<tr>
								<th>Présence DSP</th>
								<td>'.$this->Xhtml->boolean( $personne_referent['Dsp']['exists'] ).'</td>
							</tr>
							<tr>
								<th>Adresse</th>
								<td>'.$personne_referent['Adresse']['numvoie'].' '.$personne_referent['Adresse']['libtypevoie'].' '.$personne_referent['Adresse']['nomvoie'].' '.$personne_referent['Adresse']['codepos'].' '.$personne_referent['Adresse']['nomcom'].'</td>
							</tr>
							<tr>
								<th>CER signé dans la structure</th>
								<td>'.$this->Xhtml->boolean( $personne_referent['Contratinsertion']['interne'] ).'</td>
							</tr>
							<tr>
								<th>Situation allocataire</th>
								<td>'.h( Set::enum( $personne_referent['Personne']['situation'], $options['Personne']['situation'] ) ).'</td>
							</tr>
						</tbody>
					</table>';

				echo $this->Html->tableCells(
					array(
						$personne_referent['Adresse']['nomcom'],
						date_short( $personne_referent['Dossier']['dtdemrsa'] ),
						date_short( $personne_referent['Orientstruct']['date_valid'] ),
						date_short( $personne_referent['Personne']['dtnai'] ),
						$this->Xhtml->boolean( $personne_referent['Calculdroitrsa']['toppersdrodevorsa'] ),
						$this->Xhtml->boolean( $personne_referent['Dsp']['exists'] ),
						$personne_referent['Personne']['nom_complet_court'],
						$personne_referent['Contratinsertion']['rg_ci'],
						Set::enum( $personne_referent['Cer93']['positioncer'], $options['Cer93']['positioncer'] ),
						date_short( $personne_referent['Contratinsertion']['df_ci'] ),
						date_short( $personne_referent['PersonneReferent']['dddesignation'] ),
						$personne_referent['Structurereferente']['lib_struc'],
						// Choix du référent
						array(
							$this->Form->input( "PersonneReferent.{$index}.id", array( 'type' => 'hidden' ) )
							.$this->Form->input( "PersonneReferent.{$index}.dossier_id", array( 'type' => 'hidden' ) )
							.$this->Form->input( "PersonneReferent.{$index}.personne_id", array( 'type' => 'hidden' ) )
							.$this->Form->input( "PersonneReferent.{$index}.dddesignation", array( 'type' => 'hidden' ) )
							.$this->Form->input( "PersonneReferent.{$index}.action", array( 'div' => false, 'legend' => false, 'type' => 'radio', 'options' => $options['actions'], 'separator' => '<br />' ) ),
							array( 'class' => ( isset( $this->validationErrors['PersonneReferent'][$index]['action'] ) ? 'error' : null ) )
						),
						// Action
						array(
							$this->Form->input( "PersonneReferent.{$index}.referent_id", array( 'label' => false, 'legend' => false, 'type' => 'select', 'options' => $options['referents'], 'empty' => true ) ),
							array( 'class' => ( isset( $this->validationErrors['PersonneReferent'][$index]['referent_id'] ) ? 'error' : null ) )
						),
						$this->Xhtml->viewLink( 'Voir', array( 'controller' => 'personnes_referents', 'action' => 'index', $personne_referent['Personne']['id'] ) ),
						array( $innerTable, array( 'class' => 'innerTableCell noprint' ) )
					),
					array( 'class' => 'odd', 'id' => 'innerTableTrigger'.$index ),
					array( 'class' => 'even', 'id' => 'innerTableTrigger'.$index )
				);
			}
			echo '</tbody>';
			echo '</table>';

			$search = Hash::flatten( $this->request->data['Search'] );
			if( !empty( $search ) ) {
				foreach( $search as $key => $value ) {
					echo $this->Form->input( "Search.{$key}", array( 'type' => 'hidden', 'value' => $value ) );
				}
			}

			echo $this->Xform->submit( 'Validation de la liste' );
			echo $this->Xform->end();

			echo $pagination;
			echo $this->Form->button( 'Tout Activer', array( 'type' => 'button', 'onclick' => "return toutChoisir( $( 'PersonneReferent' ).getInputs( 'radio' ), 'Activer', true );" ) );
			echo $this->Form->button( 'Tout Désactiver', array( 'type' => 'button', 'onclick' => "return toutChoisir( $( 'PersonneReferent' ).getInputs( 'radio' ), 'Desactiver', true );" ) );
		}

		echo $this->Observer->disableFormOnSubmit( 'PersonneReferent' );
	}
?>
<?php if( isset( $personnes_referents ) && !empty( $personnes_referents ) ):?>

<ul class="actionMenu">
	<li><?php
		echo $this->Xhtml->exportLink(
			'Télécharger le tableau',
			array( 'action' => 'exportcsv' ) + Hash::flatten( $this->request->data, '__' ),
			( $this->Permissions->check( 'cohortescers93', 'exportcsv' ) && count( $personnes_referents ) > 0 )
		);
	?></li>
</ul>

<script type="text/javascript">
	document.observe( "dom:loaded", function() {
		// On désactive le select du référent si on ne choisit pas de valider
		// TODO: mettre ou enlever une classe disabled dans le TD ?
		<?php foreach( array_keys( $personnes_referents ) as $index ):?>
		observeDisableFieldsOnRadioValue(
			'PersonneReferent',
			'data[PersonneReferent][<?php echo $index;?>][action]',
			[ 'PersonneReferent<?php echo $index;?>ReferentId' ],
			[ 'Activer' ],
			true
		);
		<?php endforeach;?>
	} );
</script>
<?php endif;?>