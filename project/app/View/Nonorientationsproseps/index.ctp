<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>

<h1><?php echo $this->pageTitle = __d( 'nonorientationproep', 'Nonorientationsproseps'.Configure::read( 'Cg.departement' ).'::index' ); ?></h1>

<?php

	if( !empty( $this->request->data ) ) {
		echo '<ul class="actionMenu"><li>'.$this->Xhtml->link(
			$this->Xhtml->image(
				'icons/application_form_magnify.png',
				array( 'alt' => '' )
			).' Formulaire',
			'#',
			array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "$( 'Filtre' ).toggle(); return false;" )
		).'</li></ul>';
	}
?>

<?php echo $this->Form->create( 'Filtre', array( 'id' => 'Filtre', 'class' => ( !empty( $this->request->data ) ? 'folded' : 'unfolded' ), 'novalidate' => true ) );?>
	<fieldset>
		<legend><?php  echo __d( 'nonorientationproep', 'Nonorientationsproseps'.Configure::read( 'Cg.departement' ).'::legend' );?></legend>
		<?php echo $this->Xform->input( 'Filtre.index', array( 'label' => false, 'type' => 'hidden', 'value' => true ) );?>

		<?php
			if( Configure::read( 'Cg.departement' ) == 58 ) {
				$df_ci_from = Set::check( $this->request->data, 'Filtre.df_ci_from' ) ? Set::extract( $this->request->data, 'Filtre.df_ci_from' ) : strtotime( '-1 week' );
				$df_ci_to = Set::check( $this->request->data, 'Filtre.df_ci_to' ) ? Set::extract( $this->request->data, 'Filtre.df_ci_to' ) : strtotime( 'now' );

				echo $this->Form->input( 'Filtre.df_ci_from', array( 'label' => 'Le (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 120, 'selected' => $df_ci_from ) );
				echo $this->Form->input( 'Filtre.df_ci_to', array( 'label' => 'Et le (exclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 120,  'maxYear' => date( 'Y' ) + 5, 'selected' => $df_ci_to ) );
			}
		?>

		<?php echo $this->Form->input( 'Filtre.Dossier.dernier', array( 'label' => false, 'type' => 'hidden', 'value' => 1 ) );?>

		<?php
			$nbmoispardefaut = array();
			if( Configure::read( 'Cg.departement' ) == 66 ) {
				echo $this->Form->input( 'Canton.canton', array( 'label' => 'Canton', 'type' => 'select', 'options' => $cantons, 'empty' => true ) );
				echo $this->Form->input( 'Adresse.nomcom', array( 'label' => 'Commune', 'type' => 'text' ) );
				echo $this->Form->input( 'Filtre.structurereferente_id', array( 'label' => 'Structure référente', 'type' => 'select', 'options' => $structs, 'empty' => true ) );
				echo $this->Form->input( 'Filtre.referent_id', array( 'label' => 'Référent', 'type' => 'select', 'options' => $referents, 'empty' => true ) );

			}
			else if( Configure::read( 'Cg.departement' ) == 93 ) {
				echo $this->Form->input( 'Filtre.dureenonreorientation', array( 'label' => 'Parcours social sans réorientation emploi depuis ', 'type' => 'select', 'options' => $nbmoisnonreorientation ) );
			}
		?>
	</fieldset>

	<?php
		echo $this->Search->referentParcours( $structuresreferentesparcours, $referentsparcours, 'Filtre' );

		if( Configure::read( 'Cg.departement' ) == 58 ) {
			echo $this->Allocataires->blocDossier(
				array(
					'options' => $options,
					'prefix' => 'Filtre',
					'skip' => array(
						'Filtre.Situationdossierrsa.etatdosrsa',
						'Filtre.Dossier.dernier'
					)
				)
			);
			echo $this->Allocataires->blocAdresse( array( 'options' => $options, 'prefix' => 'Filtre' ) );
			echo $this->Allocataires->blocAllocataire(
				array(
					'options' => $options,
					'prefix' => 'Filtre',
					'skip' => array(
						'Filtre.Calculdroitrsa.toppersdrodevorsa',
					)
				)
			);
		}

		echo $this->Search->paginationNombretotal();
	?>

	<div class="submit">
		<?php echo $this->Form->button( __( 'Search' ), array( 'type' => 'submit' ) );?>
		<?php echo $this->Form->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
	</div>
<?php echo $this->Form->end();?>
<?php $pagination = $this->Xpaginator->paginationBlock( 'Orientstruct', $this->passedArgs ); ?>

<?php if( !empty( $this->request->data ) ):?>
	<?php if( empty( $cohorte ) ):?>
		<p class="notice"><?php echo 'Aucun allocataire ne correspond à vos critères de recherche.';?>
	<?php else: ?>
		<?php
			echo $this->Html->tag( 'h2', 'Résultats de la recherche' );
			$domain_search_plugin = ( Configure::read( 'Cg.departement' ) == 93 ) ? 'search_plugin_93' : 'search_plugin';
			echo $this->Form->create( null, array( 'novalidate' => true ) );
		?>
		<?php echo $pagination;?>
		<table id="searchResults" class="tooltips">
			<thead>
				<tr>
					<th><?php echo $this->Xpaginator->sort( 'N° de dossier', 'Dossier.numdemrsa' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Allocataire', 'Personne.nom' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Date de naissance', 'Personne.dtnai' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Code postal', 'Adresse.codepos' );?></th>
					<th><?php echo __d( 'foyer', 'Foyer.enerreur' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Date de validation de l\'orientation', 'Orientstruct.date_valid' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Nombre de jours depuis la fin du contrat lié', 'Contratinsertion.nbjours' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Type d\'orientation', 'Typeorient.lib_type_orient' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Structure référente', 'Structurereferente.lib_struc' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Référent', 'Referent.nom' );?></th>
					<?php if( Configure::read( 'Cg.departement' ) == 58 ):?>
						<th>Passage en COV ?</th>
					<?php endif;?>
					<?php if( Configure::read( 'Cg.departement' ) == 93 ):?>
						<th>Passage en EP ?</th>
					<?php endif;?>
					<?php if( Configure::read( 'Cg.departement' ) == 66 ):?>
						<th>Action</th>
					<?php endif;?>
					<th class="innerTableHeader noprint">Informations complémentaires</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach( $cohorte as $key => $orientstruct ):?>
					<?php
						$innerTable = '<table id="innerTablesearchResults'.$key.'" class="innerTable">
							<tbody>
								<tr>
									<th>'.__d( $domain_search_plugin, 'Structurereferenteparcours.lib_struc' ).'</th>
									<td>'.Hash::get( $orientstruct, 'Structurereferenteparcours.lib_struc' ).'</td>
								</tr>
								<tr>
									<th>'.__d( $domain_search_plugin, 'Referentparcours.nom_complet' ).'</th>
									<td>'.Hash::get( $orientstruct, 'Referentparcours.nom_complet' ).'</td>
								</tr>
							</tbody>
						</table>';

						// FIXME: date ouverture de droits -> voir flux instruction
						echo "<tr>";
							echo $this->Xhtml->tag( 'td', $orientstruct['Dossier']['numdemrsa'] );
							echo $this->Xhtml->tag( 'td', implode( ' ', array( $orientstruct['Personne']['qual'], $orientstruct['Personne']['nom'], $orientstruct['Personne']['prenom'] ) ) );
							echo $this->Xhtml->tag( 'td', $this->Locale->date( __( 'Locale->date' ), $orientstruct['Personne']['dtnai'] ) );
							echo $this->Xhtml->tag( 'td', $orientstruct['Adresse']['codepos'] );
							echo $this->Xhtml->tag( 'td', $orientstruct['Foyer']['enerreur'], array( 'class' => 'foyer_enerreur '.( empty( $orientstruct['Foyer']['enerreur'] ) ? 'empty' : null ) ) );
							echo $this->Xhtml->tag( 'td', $this->Locale->date( __( 'Locale->date' ), $orientstruct['Orientstruct']['date_valid'] ) );
							echo $this->Xhtml->tag( 'td', $orientstruct['Contratinsertion']['nbjours'] );
							echo $this->Xhtml->tag( 'td', $orientstruct['Typeorient']['lib_type_orient'] );
							echo $this->Xhtml->tag( 'td', $orientstruct['Structurereferente']['lib_struc'] );
							echo $this->Xhtml->tag( 'td', implode( ' ', array( $orientstruct['Referent']['qual'], $orientstruct['Referent']['nom'], $orientstruct['Referent']['prenom'] ) ) );
							if( Configure::read( 'Cg.departement' ) == 58 ){
								echo $this->Xhtml->tag(
									'td',
									$this->Form->input( 'Nonorientationproep.'.$key.'.orientstruct_id', array( 'type' => 'hidden', 'value' => $orientstruct['Orientstruct']['id'] ) ).
									$this->Form->input( 'Nonorientationproep.'.$key.'.typeorient_id', array( 'type' => 'hidden', 'value' => $orientstruct['Typeorient']['id'] ) ).
									$this->Form->input( 'Nonorientationproep.'.$key.'.structurereferente_id', array( 'type' => 'hidden', 'value' => $orientstruct['Structurereferente']['id'] ) ).
									$this->Form->input( 'Nonorientationproep.'.$key.'.personne_id', array( 'type' => 'hidden', 'value' => $orientstruct['Personne']['id'] ) ).
									$this->Form->input( 'Nonorientationproep.'.$key.'.user_id', array( 'type' => 'hidden', 'value' => $orientstruct['Orientstruct']['user_id'] ) ).
									$this->Form->input( 'Nonorientationproep.'.$key.'.passagecov', array( 'class' => 'enabled passagecov', 'type' => 'checkbox', 'label' => false ) )
								);
							}
							if( /*Configure::read( 'Cg.departement' ) == 58 || */Configure::read( 'Cg.departement' ) == 93 ){
								echo $this->Xhtml->tag(
									'td',
									$this->Form->input( 'Nonorientationproep.'.$key.'.orientstruct_id', array( 'type' => 'hidden', 'value' => $orientstruct['Orientstruct']['id'] ) ).
									$this->Form->input( 'Nonorientationproep.'.$key.'.personne_id', array( 'type' => 'hidden', 'value' => $orientstruct['Personne']['id'] ) ).
									$this->Form->input( 'Nonorientationproep.'.$key.'.user_id', array( 'type' => 'hidden', 'value' => $orientstruct['Orientstruct']['user_id'] ) ).
									$this->Form->input( 'Nonorientationproep.'.$key.'.passageep', array( 'class' => 'enabled passageep', 'type' => 'checkbox', 'label' => false ) )
								);
							}

							if( Configure::read( 'Cg.departement' ) == 66 ){
								echo $this->Xhtml->tag(
									'td',
									$this->Xhtml->viewLink(
										'Voir le dossier',
										array( 'controller' => 'rendezvous', 'action' => 'index', $orientstruct['Personne']['id'] ),
										true,
										true
									)
								);
							}

							echo $this->Xhtml->tag( 'td', $innerTable, array( 'class' => 'innerTableCell noprint' ) );
						echo "</tr>";
					?>
				<?php endforeach;?>
			</tbody>
		</table>
		<?php echo $pagination;?>
		<?php if( Configure::read( 'Cg.departement' ) != 66 ):?>
			<?php
			// Passage des champs du filtre lorsqu'on renvoie le formulaire du bas
			if( isset( $this->request->data['Filtre'] ) && is_array( $this->request->data['Filtre'] ) ) {
				foreach( Hash::flatten( $this->request->data['Filtre'] ) as $hiddenfield => $hiddenvalue ) {
					echo '<div>'.$this->Xform->input( "Filtre.$hiddenfield", array( 'type' => 'hidden', 'value' => $hiddenvalue, 'id' => 'FiltreBasDureenonreorientation' ) ).'</div>';
				}
			}
		?>
		<?php echo $this->Form->end( 'Enregistrer' );?>
		<ul class="actionMenu">
			<li><?php
				echo $this->Xhtml->exportLink(
					'Télécharger le tableau',
					array( 'controller' => 'nonorientationsproseps', 'action' => 'exportcsv' ) + Hash::flatten( $this->request->data, '__' ),
					$this->Permissions->check( 'nonorientationsproseps', 'exportcsv' )
				);
			?></li>
		</ul>

		<?php
			if( Configure::read( 'Cg.departement' ) == 58 ){
				echo $this->Form->button( 'Tout cocher COV', array( 'type' => 'button', 'onclick' => "return toutCocherCov('input[type=checkbox].passagecov.enabled', 'passagecov', 'passageep');" ) );
				echo $this->Form->button( 'Tout décocher COV', array( 'type' => 'button', 'onclick' => "return toutDecocherCov('input[type=checkbox].passagecov.enabled', 'passagecov', 'passageep');" ) );
			}
		?>
		<?php
			if( Configure::read( 'Cg.departement' ) == 93 ){
				echo $this->Form->button( 'Tout cocher EP', array( 'type' => 'button', 'onclick' => "return toutCocherCov('input[type=checkbox].passageep.enabled', 'passageep', 'passagecov');" ) );
				echo $this->Form->button( 'Tout décocher EP', array( 'type' => 'button', 'onclick' => "return toutDecocherCov('input[type=checkbox].passageep.enabled', 'passageep', 'passagecov');" ) );
			}
		?>
	<?php endif;?>
<?php endif;?>

<?php endif;?>

<script type="text/javascript">
	function togglePassageCovEp( checkbox, cbClass, otherCbClass ) {
		var otherCbName = $( checkbox ).readAttribute( 'name' ).replace( cbClass, otherCbClass );
		var otherInputSelector = 'input[name="' + otherCbName + '"]';
		if( $( checkbox ).checked ) {
			$$( otherInputSelector ).each( function ( elmt ) { $( elmt ).removeClassName( 'enabled' ); $( elmt ).disable(); } );
		}
		else {
			$$( otherInputSelector ).each( function ( elmt ) { $( elmt ).addClassName( 'enabled' ); $( elmt ).enable(); } );
		}
	}

	function toutCocherCov( selecteur, cbClass, otherCbClass ) {
		if( selecteur == undefined ) {
			selecteur = 'input[type="checkbox"]';
		}

		$$( selecteur ).each( function( checkbox ) {
			$( checkbox ).checked = true;
			togglePassageCovEp( checkbox, cbClass, otherCbClass );
		} );

		return false;
	}

	function toutDecocherCov( selecteur, cbClass, otherCbClass ) {
		if( selecteur == undefined ) {
			selecteur = 'input[type="checkbox"]';
		}

		$$( selecteur ).each( function( checkbox ) {
			$( checkbox ).checked = false;
			togglePassageCovEp( checkbox, cbClass, otherCbClass );
		} );

		return false;
	}

	$$( 'input[type="checkbox"].passagecov' ).each( function( checkbox ) {
		$( checkbox ).observe( 'change', function() {
			togglePassageCovEp( $(this), 'passagecov', 'passageep' );
		} );
	} );

	$$( 'input[type="checkbox"].passageep' ).each( function( checkbox ) {
		$( checkbox ).observe( 'change', function() {
			togglePassageCovEp( $(this), 'passageep', 'passagecov' );
		} );
	} );
</script>

<?php if( Configure::read( 'Cg.departement' ) == 66 ):?>
<script type="text/javascript">
	document.observe("dom:loaded", function() {
            dependantSelect( 'FiltreReferentId', 'FiltreStructurereferenteId' );
            try { $( 'FiltreStructurereferenteId' ).onchange(); } catch(id) { }
	} );
</script>
<?php endif;?>

<?php echo $this->Search->observeDisableFormOnSubmit( 'Filtre' ); ?>