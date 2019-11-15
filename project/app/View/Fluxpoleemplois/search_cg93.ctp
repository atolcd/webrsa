<?php
	$departement = (int)Configure::read( 'Cg.departement' );
	$user_type = $this->Session->read( 'Auth.User.type' );

	$controller = $this->params->controller;
	$action = $this->action;
	$formId = ucfirst($controller) . ucfirst($action) . 'Form';
	$availableDomains = WebrsaTranslator::domains();

	$domain = isset( $availableDomains[0] ) ? $availableDomains[0] : $controller;
?>

<?php $this->start( 'custom_search_filters' );?>

<!-- Blocs de recherche spécifique -->
<?php
	$paramDate = array(
		'domain' => $domain,
		'minYear_from' => date( 'Y' ) - 10,
		'maxYear_from' => date( 'Y' ) + 10,
		'minYear_to' => date( 'Y' ) - 10,
		'maxYear_to' => date( 'Y' ) + 10
	);

	echo $this->Html->tag(
		'fieldset',
		$this->Html->tag( 'legend', __m( 'Search.Fluxpoleemploi.legend.inscription' ) )
		.$this->SearchForm->dateRange( 'Search.Fluxpoleemploi.inscription_date_debut_ide', $paramDate )
		.$this->SearchForm->dateRange( 'Search.Fluxpoleemploi.inscription_date_cessation_ide', $paramDate )
		.$this->SearchForm->dateRange( 'Search.Fluxpoleemploi.inscription_date_radiation_ide', $paramDate )
	);

	echo $this->Html->tag(
		'fieldset',
		$this->Html->tag( 'legend', __m( 'Search.Fluxpoleemploi.legend.ppae' ) )
		.$this->SearchForm->dateRange( 'Search.Fluxpoleemploi.ppae_date_signature', $paramDate )
		.$this->SearchForm->dateRange( 'Search.Fluxpoleemploi.ppae_date_notification', $paramDate )
		.$this->SearchForm->dateRange( 'Search.Fluxpoleemploi.ppae_date_dernier_ent', $paramDate )
	);
?>

<?php
	echo '<fieldset><legend>' . __m( 'Orientstruct.search' ) . '</legend>';
	echo $this->Default3->subform(
			array ('Search.Orientstruct.typeorient_id' => array ('empty' => true, 'required' => false)),
			array ('options' => array ('Search' => $options))
		);
	echo '</fieldset>';
?>

	<fieldset>
		<legend><?php echo __d('fluxpoleemplois', 'Search.Fluxpoleemploi.legend.accompagnement') ?></legend>
		<div class="input checkbox">
			<input type="checkbox" name="data[Search][ByAccompagnement][accompagnement_choice]" value="1" id="SearchByAccompagnementChoice" <?php echo isset ($this->request->data['Search']['ByAccompagnement']['accompagnement_choice']) ? 'checked="checked"' : ''  ?> />
			<label for="SearchByAccompagnementChoice"><?php echo __d('fluxpoleemplois', 'Search.Fluxpoleemploi.accompagnement') ?></label>
		</div>
		<div id="SearchByAccompagnementFieldset">
			<?php echo $this->Xform->multipleCheckbox( 'Search.Fluxpoleemploi.accompagnement', $options ); ?>
		</div>
	</fieldset>
	<script type="text/javascript">
	//<![CDATA[
	document.observe( 'dom:loaded', function() { try {
		observeDisableFieldsetOnCheckbox( 'SearchByAccompagnementChoice', 'SearchByAccompagnementFieldset', false, true );
	} catch( e ) {
		console.error( e );
	} } );
	//]]>
	</script>

<?php $this->end();?>

<?php
	$this->start( 'before_search_filters' );

	echo $this->Html->tag(
		'fieldset',
		$this->Html->tag( 'legend', __m( 'Search.Fluxpoleemploi.legend.allocataire' ) )
		.$this->Xform->input( 'Search.Fluxpoleemploi.allocataire_identifiant_pe', array( 'label' => __m( 'Search.Fluxpoleemploi.allocataire_identifiant_pe' ), 'type' => 'text', 'maxlength' => 15 ) )
	);

	$this->end();
?>

<?php
	echo $this->element(
		'ConfigurableQuery/search',
		array(
			//'actions' => $actions,
			'beforeSearch' => $this->fetch( 'before_search_filters' ),
			'customSearch' => $this->fetch( 'custom_search_filters' ),
			'exportcsv' => array( 'action' => 'exportcsv' )
		)
	);
?>