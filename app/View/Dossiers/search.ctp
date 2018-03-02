<?php
	// Conditions d'accès aux tags
	$departement = (int)Configure::read( 'Cg.departement' );
	$user_type = $this->Session->read( 'Auth.User.type' );
	$utilisateursAutorises = (array)Configure::read( 'acces.recherche.tag' );
	$viewTag = false;

	foreach ($utilisateursAutorises as $utilisateurAutorise) {
		if ($utilisateurAutorise == $user_type) {
			$viewTag = true;
			break;
		}
	}

	if ($departement != 93) {
		$viewTag = true;
	}
	// Conditions d'accès aux tags

	$actions = array();
	if( $departement == 66 ) {
		if( $this->Permissions->check( 'ajoutdossierscomplets', 'add' ) ) {
			$actions['/Ajoutdossierscomplets/add'] = array( 'class' => 'add', 'domain' => 'dossiers' );
		}
	}
	else {
		if( $this->Permissions->check( 'ajoutdossiers', 'wizard' ) ) {
			$actions['/Ajoutdossiers/wizard'] = array( 'class' => 'add', 'domain' => 'dossiers' );
		}
	}

	if( $this->Permissions->check( 'dossierssimplifies', 'add' ) ) {
		if( $departement != 58 ) {
			$actions['/Dossierssimplifies/add'] = array( 'class' => 'add', 'domain' => 'dossiers' );
		}
	}
?>

<?php $this->start( 'custom_search_filters' );?>
<fieldset>
	<legend>Recherche par parcours de l'allocataire</legend>
	<?php
		echo $this->Xform->input( 'Search.Dsp.natlog', array( 'label' => 'Conditions de logement', 'type' => 'select', 'empty' => true, 'options' => $options['Dsp']['natlog'] ) );
		if( $departement == 58 ) {
			echo $this->Xform->input( 'Search.Activite.act', array( 'label' => 'Code activité', 'type' => 'select', 'empty' => true, 'options' => $options['Activite']['act'] ) );
			echo $this->Form->input( 'Search.Propoorientationcov58.referentorientant_id', array( 'label' => 'Travailleur social chargé de l\'évaluation', 'type' => 'select', 'options' => $options['Propoorientationcov58']['referentorientant_id'], 'empty' => true ) );
			echo $this->Form->input( 'Search.Personne.etat_dossier_orientation', array( 'label' => __d( 'personne', 'Personne.etat_dossier_orientation' ), 'type' => 'select', 'options' => $options['Personne']['etat_dossier_orientation'], 'empty' => true ) );
		}
	?>
</fieldset>

<?php if ($viewTag) { ?>
	<fieldset>
		<legend><?php echo __d('dossiers', 'Search.Tag.search_title') ?></legend>
		<div class="input checkbox">
			<input type="checkbox" name="data[Search][ByTag][tag_choice]" value="1" id="SearchByTagChoice" <?php echo isset ($this->request->data['Search']['ByTag']['tag_choice']) ? 'checked="checked"' : ''  ?> />
			<label for="SearchByTagChoice"><?php echo __d('dossiers', 'Search.Tag.filter_title') ?></label>
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
	//<![CDATA[
	document.observe( 'dom:loaded', function() { try {
		observeDisableFieldsetOnCheckbox( 'SearchByTagChoice', 'SearchByTagFieldset', false, true );
	} catch( e ) {
		console.error( e );
	} } );
	//]]>
	</script>
<?php } ?>

<?php
	if ($departement === 66) {
?>
<fieldset>
	<legend><?php echo __d( 'dossierspcgs66', 'Dossierpcg66.search' ); ?></legend>
	<?php
		echo $this->Xform->input(
			'Search.Dossierpcg66.has_dossierpcg66',
			array(
				'label' => __d( 'dossierspcgs66', 'Search.Dossierpcg66.has_dossierpcg66' ),
				'type' => 'select',
				'empty' => true,
				'options' => array ('Non', 'Oui')
			)
		);
	?>
</fieldset>
<?php
	}
?>
<?php $this->end();?>

<?php
	echo $this->element(
		'ConfigurableQuery/search',
		array(
			'actions' => $actions,
			'customSearch' => $this->fetch( 'custom_search_filters' ),
			'exportcsv' => array( 'action' => 'exportcsv' )
		)
	);
?>