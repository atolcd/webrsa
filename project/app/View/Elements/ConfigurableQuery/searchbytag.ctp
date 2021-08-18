<?php
if( !is_array(Configure::read('ConfigurableQuery.' . ucfirst($this->params->controller) . '.' . $this->action . '.filters.skip'))
	|| !in_array( 'ByTag.tag_choice', Configure::read('ConfigurableQuery.' . ucfirst($this->params->controller) . '.' . $this->action . '.filters.skip') ) ) {
	// Conditions d'accès aux tags
	$departement = Configure::read( 'Cg.departement' );
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

	/**
	 * ATTENTION
	 * Uniquement pour la page de tag en cohorte.
	 */
	if ($configurableQueryParams['configurableQueryFieldsKey'] == 'Tags.cohorte') {
		$configuredCohorteParams['cohorteFields']['data[Cohorte][][EntiteTag][modele]']['options'] = array (
			__d('tags', 'Cohorte.EntiteTag.personne') => __d('tags', 'Cohorte.EntiteTag.personne'),
			__d('tags', 'Cohorte.EntiteTag.foyer') => __d('tags', 'Cohorte.EntiteTag.foyer'),
		);
	}

	if ($viewTag) {
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
	//<![CDATA[
	document.observe( 'dom:loaded', function() { try {
		observeDisableFieldsetOnCheckbox( 'SearchByTagChoice', 'SearchByTagFieldset', false, true );
	} catch( e ) {
		console.error( e );
	} } );
	//]]>
	</script>
	<?php
	}

	}
?>