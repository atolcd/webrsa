<?php
	if( Configure::read( 'debug' ) ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
	$this->pageTitle = __d( 'relancenonrespectsanctionep93', 'Relancesnonrespectssanctionseps93::impressions' );
?>

<h1><?php echo $this->pageTitle;?></h1>

<?php if( is_array( $this->request->data ) ):?>
	<ul class="actionMenu">
		<?php
			echo '<li>'.$this->Xhtml->link(
				$this->Xhtml->image(
					'icons/application_form_magnify.png',
					array( 'alt' => '' )
				).' Formulaire',
				'#',
				array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "var form = $$( 'form' ); form = form[0]; $( form ).toggle(); return false;" )
			).'</li>';
		?>
	</ul>
<?php endif;?>

<?php
	$paramDate = array(
		'domain' => 'orientsstructs',
		'minYear_from' => '2009',
		'maxYear_from' => date( 'Y' ) + 1,
		'minYear_to' => '2009',
		'maxYear_to' => date( 'Y' ) + 4
	);
	// Formulaire
	echo $this->Xform->create( null, array( 'id' => 'Search' ) );

	echo $this->Xhtml->tag( 'fieldset', $this->Xhtml->tag( 'legend', 'Recherche par bénéficiaire' ).
		$this->Default2->subform(
			array(
				'Personne.nom' => array( 'required' => false ),
				'Personne.nomnai',
				'Personne.prenom' => array( 'required' => false ),
				'Personne.nir',
				'Adresse.numcom' => array( 'required' => false ),
				'Serviceinstructeur.id' => array( 'domain' => 'relancenonrespectsanctionep93' ),
				'Personne.trancheage' => array( 'empty' => true, 'options' => Configure::read('Search.Options.enums.Personne.trancheage')),
				'Personne.trancheagesup' => array( 'empty' => true, 'options' => Configure::read('Search.Options.enums.Personne.trancheagesup')),
				'Personne.trancheageprec' => array( 'empty' => true, 'options' => Configure::read('Search.Options.enums.Personne.trancheageprec')),
			),
			array(
				'options' => $options
			)
		)
	);

	echo $this->Xhtml->tag( 'fieldset', $this->Xhtml->tag( 'legend', __d( 'dossier', 'Dossier.matricule.fieldset' ) ).
		$this->Default2->subform(
			array(
				'Dossier.matricule',
				'Dossiercaf.nomtitulaire',
				'Dossiercaf.prenomtitulaire',
				'Nonrespectsanctionep93.origine' => array( 'label' => 'Présence contrat', 'type' => 'radio', 'options' => array( 'orientstruct' => 'Non', 'contratinsertion' => 'Oui' ), 'required' => false ),
			)
		)
	);
?>

<div class="noprint">
	<?php echo $this->Form->input( 'Relancenonrespectsanctionep93.daterelance', array( 'label' => 'Filtrer par période de relance', 'type' => 'checkbox' ) );?>
</div>
<fieldset class="noprint">
	<legend class="noprint">Date de Relance</legend>
	<?php
		$daterelance_from = Set::check( $this->request->data, 'Relancenonrespectsanctionep93.daterelance_from' ) ? Set::extract( $this->request->data, 'Relancenonrespectsanctionep93.daterelance_from' ) : strtotime( '-1 week' );
		$daterelance_to = Set::check( $this->request->data, 'Relancenonrespectsanctionep93.daterelance_to' ) ? Set::extract( $this->request->data, 'Relancenonrespectsanctionep93.daterelance_to' ) : strtotime( 'now' );
	?>
	<?php echo $this->Form->input( 'Relancenonrespectsanctionep93.daterelance_from', array( 'label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 120, 'selected' => $daterelance_from ) );?>
	<?php echo $this->Form->input( 'Relancenonrespectsanctionep93.daterelance_to', array( 'label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 5, 'minYear' => date( 'Y' ) - 120, 'selected' => $daterelance_to ) );?>
</fieldset>

<?php
	echo '<fieldset><legend>' . __d('orientsstructs', 'Orientstruct.search' ) . '</legend>'
	. $this->Default3->subform(
		array(
			'Orientstruct.derniere' => array( 'type' => 'checkbox' )
		),
		array( 'options' => array( 'Search' => $options ), 'domain' => 'orientsstructs' )
	)
	. $this->Default3->subform(
		array(
			'Orientstruct.dernierevalid' => array( 'type' => 'checkbox' )
		),
		array( 'options' => array( 'Search' => $options ), 'domain' => 'orientsstructs' )
	)
	. $this->SearchForm->dateRange( 'Orientstruct.date_valid', $paramDate );

	echo $this->Default3->subform(
		array(
			'Orientstruct.origine' => array('empty' => true),
		),
		array( 'options' => $options, 'domain' => 'orientsstructs' )
	);

	echo $this->Default3->subform(
			array(
				'Orientstruct.typeorient_id' => array('empty' => true, 'required' => false),
			),
			array( 'options' => $options, 'domain' => 'orientsstructs' )
		);

	echo $this->Allocataires->communautesrSelect( 'Orientstruct', array( 'options' => array( 'Search' => $options ), 'label' => __d('orientsstructs', 'Search.Orientstruct.communautesr_id' ) ) );

	echo $this->Default3->subform(
			array(
				'Orientstruct.structurereferente_id' => array('empty' => true, 'required' => false),
				'Orientstruct.statut_orient' => array('empty' => true, 'required' => false)
			),
			array( 'options' => $options, 'domain' => 'orientsstructs' )
		)
		. '</fieldset>';

	echo $this->Search->referentParcours( $structuresreferentesparcours, $referentsparcours, 'Search' );

	echo $this->Observer->dependantSelect(
		array(
			'Orientstruct.typeorient_id' => 'Orientstruct.structurereferente_id'
		)
	);
?>
<fieldset>
		<legend><?php echo __d('tag', 'Search.Tag.search_title') ?></legend>

		<?php
			echo $this->Default3->subform(
				array(
					'Search.ByTag.tag_choice' => array('type' => 'checkbox', 'label' => __d('tag', 'Search.Tag.filter_title')),
				),
				array( 'options' => array('domain' => 'tag'))
			);

	?>
		<div id="SearchByTagFieldset">

			<?php echo $this->Allocataires->SearchForm->dateRange( 'Search.Tag.created', array('domain' => 'dossiers') ); ?>

			 <?php
			echo $this->Default3->subform(
				array(
					'Search.Tag.exclusionValeur' => array('type' => 'checkbox', 'label' => __d('tag', 'Search.Tag.exclusionValeur')),
				),
				array( 'options' => array('domain' => 'tag' ))
			);

	?>
			<fieldset>
			<legend><?php echo __d('tag', 'Search.Tag.valeurtag_id') ?></legend>
				<?php
				$i = 0;
				foreach($options['Tag']['valeurtag_id'] as $key => $array){
					echo '<fieldset><legend>'.$key.'</legend>';
					foreach ($array as $value => $label){
						echo $this->Default3->subform(
							array(
								'Search.Tag.valeurtag_id.'.$i => array('type' => 'checkbox', 'value' => $value, 'label' => $label),
							),
							array( 'options' => array('domain' => 'tag' ))
						);
						$i++;
					}
					echo '</fieldset>';
				}
			?>
			</fieldset>

			<?php
			echo $this->Default3->subform(
				array(
					'Search.Tag.exclusionEtat' => array('type' => 'checkbox', 'label' => __d('tag', 'Search.Tag.exclusionEtat')),
				),
				array( 'options' => array('domain' => 'tag' ))
			);

			?>
			<fieldset>
			<legend><?php echo __d('tag', 'Search.Tag.etat') ?></legend>
				<?php
				$i = 0;
				foreach($options['Tag']['etat'] as $value => $label){
					echo $this->Default3->subform(
						array(
							'Search.Tag.etat.'.$i => array('type' => 'checkbox', 'value' => $value, 'label' => $label),
						),
						array( 'options' => array('domain' => 'tag' ))
					);
					$i++;
				}
			?>
			</fieldset>
		</div>
	</fieldset>
	<script type="text/javascript">
	document.observe( 'dom:loaded', function() { try {
		observeDisableFieldsetOnCheckbox( 'SearchByTagTagChoice', 'SearchByTagFieldset', false, true );
	} catch( e ) {
		console.error( e );
	} } );
	</script>

<?php
	echo $this->Search->paginationNombretotal( 'Search.Pagination.nombre_total' );
	echo $this->Search->observeDisableFormOnSubmit( 'Search' );

	echo $this->Html->tag( 'div', $this->Form->button( __( 'Search' ) ), array( 'class' => 'submit' ) );
	echo $this->Form->end();
?>

<?php if( isset( $relances ) ):?>
	<?php if( empty( $relances ) ):?>
		<p class="notice">Aucun dossier relancé ne correspond à vos critères.</p>
	<?php else:?>
		<?php
			echo $this->Html->tag( 'h2', 'Résultats de la recherche' );
			$domain_search_plugin = ( Configure::read( 'Cg.departement' ) == 93 ) ? 'search_plugin_93' : 'search_plugin';

			$pagination = $this->Xpaginator->paginationBlock( 'Relancenonrespectsanctionep93', $this->passedArgs );
			echo $pagination;
		?>
		<table  id="searchResults" class="tooltips default2">
			<thead>
				<tr>
					<th><?php echo $this->Xpaginator->sort( __d( 'dossier', 'Dossier.matricule' ), 'Dossier.matricule' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Nom / prénom bénéficiaire', 'Personne.nom' );?></th>
					<th><?php echo $this->Xpaginator->sort( __d( 'personne', 'Personne.nir' ), 'Personne.nir' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Ville', 'Adresse.nomcom' );?></th>
					<th><?php echo $this->Xpaginator->sort( __d( 'foyer', 'Foyer.enerreur' ), 'Foyer.enerreur' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Présence contrat ?', 'Contratinsertion.id' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Date de fin du dernier contrat', 'Contratinsertion.df_ci' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Nbre jours depuis la fin du dernier contrat', 'Contratinsertion.nbjours' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Date d\'orientation', 'Orientstruct.date_impression' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Nbre jours depuis orientation', 'Orientstruct.nbjours' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Statut EP', 'Passagecommissionep.etatdossierep' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Origine', 'Nonrespectsanctionep93.origine' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Date de relance', 'Relancenonrespectsanctionep93.daterelance' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Rang de relance', 'Relancenonrespectsanctionep93.numrelance' );?></th>
					<th colspan="2">Actions</th>
					<th class="innerTableHeader noprint">Informations complémentaires</th>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach( $relances as $index => $relance ) {
						$etatdossierep = $relance['Passagecommissionep']['etatdossierep'];
						if( empty( $etatdossierep ) && !empty( $relance['Dossierep']['id'] ) ) {
							$etatdossierep = 'En attente';
						}
						else {
							$etatdossierep = Set::enum( $relance['Passagecommissionep']['etatdossierep'], $options['Passagecommissionep']['etatdossierep'] );
						}

						$innerTable = '<table id="innerTablesearchResults'.$index.'" class="innerTable">
							<tbody>
								<tr>
									<th>'.__d( $domain_search_plugin, 'Structurereferenteparcours.lib_struc' ).'</th>
									<td>'.Hash::get( $relance, 'Structurereferenteparcours.lib_struc' ).'</td>
								</tr>
								<tr>
									<th>'.__d( $domain_search_plugin, 'Referentparcours.nom_complet' ).'</th>
									<td>'.Hash::get( $relance, 'Referentparcours.nom_complet' ).'</td>
								</tr>
							</tbody>
						</table>';

						echo $this->Xhtml->tableCells(
							array(
								h( $relance['Dossier']['matricule'] ),
								h( "{$relance['Personne']['nom']} {$relance['Personne']['prenom']}" ),
								h( $relance['Personne']['nir'] ),
								h( $relance['Adresse']['nomcom'] ),
								array( h( @$relance['Foyer']['enerreur'] ), array( 'class' => 'foyer_enerreur '.( empty( $relance['Foyer']['enerreur'] ) ? 'empty' : null ) ) ),
								h( empty( $relance['Contratinsertion']['id'] ) ? 'Non' : 'Oui' ),
								$this->Locale->date( 'Locale->date', $relance['Contratinsertion']['df_ci'] ),
								h( $relance['Contratinsertion']['nbjours'] ),
								$this->Locale->date( 'Locale->date', $relance['Orientstruct']['date_impression'] ),
								h( $relance['Orientstruct']['nbjours'] ),
								h( $etatdossierep ),
								h( Set::enum( $relance['Nonrespectsanctionep93']['origine'], $options['Nonrespectsanctionep93']['origine'] ) ),
								$this->Locale->date( 'Locale->date', $relance['Relancenonrespectsanctionep93']['daterelance'] ),
								( ( $relance['Relancenonrespectsanctionep93']['numrelance'] < 2 ) ? '1ère relance' : "{$relance['Relancenonrespectsanctionep93']['numrelance']}ème relance" ),
								$this->Default2->button( 'view', array( 'controller' => 'relancesnonrespectssanctionseps93', 'action' => 'index', $relance['Personne']['id'] ), array( 'label' => 'Voir', 'enabled' => $this->Permissions->check( 'relancesnonrespectssanctionseps93', 'index' ), 'target' => 'external' ) ),
								$this->Default2->button( 'print', array( 'controller' => 'relancesnonrespectssanctionseps93', 'action' => 'impression', $relance['Relancenonrespectsanctionep93']['id'] ), array( 'enabled' => ( !empty( $relance['Pdf']['id'] ) && $this->Permissions->check( 'relancesnonrespectssanctionseps93', 'index' ) ), 'label' => 'Imprimer' ) ),
								array( $innerTable, array( 'class' => 'innerTableCell noprint' ) ),
							),
							array( 'class' => 'odd' ),
							array( 'class' => 'even' )
						);
					}
				?>
			</tbody>
		</table>
		<?php echo $pagination;?>
	<?php endif;?>

	<ul class="actionMenu">
		<li><?php
			echo $this->Xhtml->printLinkJs(
				'Imprimer le tableau',
				array( 'onclick' => 'printit(); return false;' )
			);
		?></li>
		<li><?php
			echo $this->Xhtml->exportLink(
				'Télécharger',
				Hash::merge( array( 'controller' => $this->request->params['controller'], 'action' => 'exportcsv' ), Hash::flatten( $this->request->data, '__' ) ),
				$this->Permissions->check( $this->request->params['controller'], 'exportcsv' )
			);
		?></li>
		<li><?php
		echo $this->Xhtml->printCohorteLink(
			'Imprimer la cohorte',
			Set::merge(
				array(
					'controller' => $this->request->params['controller'],
					'action'     => 'impression_cohorte'
				),
				Hash::flatten( $this->request->data, '__' )
			),
			$this->Permissions->check( $this->request->params['controller'], 'impression_cohorte' )
		);
		?></li>
	</ul>
<?php endif;?>

<script type="text/javascript">
	document.observe("dom:loaded", function() {
		observeDisableFieldsetOnCheckbox( 'Relancenonrespectsanctionep93Daterelance', $( 'Relancenonrespectsanctionep93DaterelanceFromDay' ).up( 'fieldset' ), false );

		var form = $$( 'form' );
		form = form[0];
		<?php if( isset( $relances ) ):?>$( form ).hide();<?php endif;?>
	});
    observeDisableFieldsOnCheckbox(
        'OrientstructDerniere',
        [
            'OrientstructDernierevalid',
        ],
        true
    );
    observeDisableFieldsOnCheckbox(
        'OrientstructDernierevalid',
        [
            'OrientstructDerniere',
        ],
        true
    );
</script>