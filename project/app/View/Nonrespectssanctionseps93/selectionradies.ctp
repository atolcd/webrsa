<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>
<h1><?php echo $this->pageTitle = __d( 'nonrespectsanctionep93', "{$this->name}::{$this->action}" );?></h1>
<?php
	$paramDate = array(
		'domain' => 'orientsstructs',
		'minYear_from' => '2009',
		'maxYear_from' => date( 'Y' ) + 1,
		'minYear_to' => '2009',
		'maxYear_to' => date( 'Y' ) + 4
	);

	echo '<ul class="actionMenu"><li>'.$this->Xhtml->link(
		$this->Xhtml->image(
			'icons/application_form_magnify.png',
			array( 'alt' => '' )
		).' Formulaire',
		'#',
		array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "var form = $$( 'form' ); form = form[0]; $( form ).toggle(); return false;" )
	).'</li></ul>';
?>

<?php echo $this->Xform->create( 'Nonrespectsanctionep93', array( 'type' => 'post', 'url' => array( 'action' => $this->action ), 'id' => 'Search', 'class' => ( ( is_array( $this->request->data ) && !empty( $this->request->data ) ) ? 'folded' : 'unfolded' ) ) );?>
	<fieldset>
			<?php echo $this->Xform->input( 'Search.active', array( 'type' => 'hidden', 'value' => true ) );?>

			<legend>Filtrer par Personne</legend>
			<?php
				echo $this->Default2->subform(
					array(
						'Search.Personne.nom' => array( 'label' => __d( 'personne', 'Personne.nom' ) ),
						'Search.Personne.prenom' => array( 'label' => __d( 'personne', 'Personne.prenom' ) ),
						'Search.Personne.nomnai' => array( 'label' => __d( 'personne', 'Personne.nomnai' ) ),
						'Search.Personne.nir' => array( 'label' => __d( 'personne', 'Personne.nir' ), 'maxlength' => 15 ),
						'Search.Dossier.matricule' => array( 'label' => __d( 'dossier', 'Dossier.matricule' ), 'maxlength' => 15 ),
						'Search.Dossier.numdemrsa' => array( 'label' => __d( 'dossier', 'Dossier.numdemrsa' ), 'maxlength' => 15 ),
						'Search.Historiqueetatpe.identifiantpe' => array( 'label' => __d( 'historiqueetatpe', 'Historiqueetatpe.identifiantpe' ), 'maxlength' => 11 ),
						'Search.Adresse.nomcom' => array( 'label' => 'Commune de l\'allocataire ' ),
						'Search.Adresse.numcom' => array( 'label' => 'Numéro de commune au sens INSEE ', 'type' => 'select', 'options' => $mesCodesInsee, 'empty' => true ),
						'Search.Personne.trancheage' => array('label' => __d( 'personne', 'Personne.trancheage'), 'empty' => true, 'options' => Configure::read('Search.Options.enums.Personne.trancheage')),
						'Search.Personne.trancheagesup' => array('label' => __d( 'personne', 'Personne.trancheagesup'), 'empty' => true, 'options' => Configure::read('Search.Options.enums.Personne.trancheagesup')),
						'Search.Personne.trancheageprec' => array('label' => __d( 'personne', 'Personne.trancheageprec'), 'empty' => true, 'options' => Configure::read('Search.Options.enums.Personne.trancheageprec')),
					)
				);
			?>
		</fieldset>
<?php
		echo '<fieldset><legend>' . __d('orientsstructs', 'Orientstruct.search' ) . '</legend>'
	. $this->SearchForm->dateRange( 'Search.Orientstruct.date_valid', $paramDate );

	echo $this->Default3->subform(
		array(
			'Search.Orientstruct.origine' => array('empty' => true),
		),
		array( 'options' => $options, 'domain' => 'orientsstructs' )
	);

	echo $this->Allocataires->communautesrSelect( 'Orientstruct', array( 'options' => array( 'Search' => $options ), 'label' => __d('orientsstructs', 'Search.Orientstruct.communautesr_id' ) ) );

	echo $this->Default3->subform(
			array(
				'Search.Orientstruct.structurereferente_id' => array('empty' => true, 'required' => false),
				'Search.Orientstruct.statut_orient' => array('empty' => true, 'required' => false)
			),
			array( 'options' => $options, 'domain' => 'orientsstructs' )
		)
		. '</fieldset>';

	echo $this->Search->referentParcours( $structuresreferentesparcours, $referentsparcours, 'Search' );
?>
	<fieldset>
		<legend><?php echo __d('tag', 'Search.Tag.search_title') ?></legend>
		<div class="input checkbox">
			<input type="checkbox" name="data[Search][ByTag][tag_choice]" value="1" id="SearchByTagChoice" <?php echo isset ($this->request->data['Search']['ByTag']['tag_choice']) ? 'checked="checked"' : ''  ?> />
			<label for="SearchByTagChoice"><?php echo __d('tag', 'Search.Tag.filter_title') ?></label>
		</div>
		<div id="SearchByTagFieldset">

			<?php echo $this->Allocataires->SearchForm->dateRange( 'Search.Tag.created', array('domain' => 'dossiers') ); ?>

			<div class="checkbox">
				<input name="data[Search][Tag][exclusionValeur][]" value="1" id="SearchTagValeurtagExclusion" type="checkbox" <?php echo isset ($this->request->data['Search']['Tag']['exclusionValeur']) ? 'checked="checked"' : ''  ?> />
				<label for="SearchTagValeurtagExclusion">Exclusion des valeurs</label>
			</div>

			<?php echo $this->Xform->multipleCheckbox('Search.Tag.valeurtag_id', $options); ?>

			<div class="checkbox">
				<input name="data[Search][Tag][exclusionEtat][]" value="1" id="SearchTagValeurtagEtat" type="checkbox" <?php echo isset ($this->request->data['Search']['Tag']['exclusionEtat']) ? 'checked="checked"' : ''  ?> />
				<label for="SearchTagValeurtagEtat">Exclusion des états</label>
			</div>

			<?php echo $this->Xform->multipleCheckbox('Search.Tag.etat', $options); ?>

		</div>
	</fieldset>
	<script type="text/javascript">
	document.observe( 'dom:loaded', function() { try {
		observeDisableFieldsetOnCheckbox( 'SearchByTagChoice', 'SearchByTagFieldset', false, true );
	} catch( e ) {
		console.error( e );
	} } );
	</script>

		<?php
			echo $this->Search->referentParcours( $structuresreferentesparcours, $referentsparcours, 'Search' );
			echo $this->Search->paginationNombretotal( 'Search.Pagination.nombre_total' );
			echo $this->Search->observeDisableFormOnSubmit( 'Search' );
		?>

	<div class="submit noprint">
		<?php echo $this->Form->button( 'Rechercher', array( 'type' => 'submit' ) );?>
		<?php echo $this->Form->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
	</div>
<?php echo $this->Form->end();?>

<?php if( isset( $radiespe ) ):?>
<?php
	echo $this->Html->tag( 'h2', 'Résultats de la recherche' );

	if ( is_array( $radiespe ) && count( $radiespe ) > 0 ) {
		$domain_search_plugin = ( Configure::read( 'Cg.departement' ) == 93 ) ? 'search_plugin_93' : 'search_plugin';

		echo $this->Default2->index(
			$radiespe,
			array(
				'Personne.nom',
				'Personne.prenom',
				'Personne.dtnai',
				'Adresse.nomcom',
				'Orientstruct.date_valid',
				'Typeorient.lib_type_orient',
				'Historiqueetatpe.date',
				'Contratinsertion.present' => array( 'type' => 'boolean' ),
				'Foyer.enerreur' => array( 'type' => 'string', 'class' => 'foyer_enerreur' ),
				'Historiqueetatpe.chosen' => array( 'input' => 'checkbox', 'type' => 'boolean', 'domain' => 'nonrespectsanctionep93', 'sort' => false ),
			),
			array(
				'cohorte' => true,
				'hidden' => array(
					'Personne.id',
					'Historiqueetatpe.id'
				),
				'paginate' => 'Personne',
				'domain' => 'nonrespectsanctionep93',
				'labelcohorte' => 'Enregistrer',
				'tooltip' => array(
					'Structurereferenteparcours.lib_struc' => array( 'type' => 'text', 'domain' => $domain_search_plugin, 'model' => 'Structurereferente' ),
					'Referentparcours.nom_complet' => array( 'type' => 'text', 'domain' => $domain_search_plugin, 'model' => 'Referent' )
				)
			)
		);
	}
	else {
		echo $this->Xhtml->tag( 'p', 'Aucun résultat ne correspond aux critères choisis.', array( 'class' => 'notice' ) );
	}
?>
<?php endif;?>
<?php if( !empty( $radiespe ) ):?>
	<?php echo $this->Form->button( 'Tout cocher', array( 'type' => 'button', 'onclick' => 'return toutCocher();' ) );?>
	<?php echo $this->Form->button( 'Tout décocher', array( 'type' => 'button', 'onclick' => 'return toutDecocher();' ) );?>
<?php endif;?>