<?php
	$departement = Configure::read( 'Cg.departement' );
	$user_type = $this->Session->read( 'Auth.User.type' );

	$actions = array();
?>

<?php $this->start( 'custom_search_filters' );?>

<fieldset>
	<legend><?php echo __m( 'Search.Recoursgracieux' ); ?></legend>
	<?php
	/**
	 * Date d’arrivée du dossier au CD
	*/
	echo $this->SearchForm->dateRange('Search.Recourgracieux.dtarrivee', array('domain' => 'recoursgracieux'));

	/**
	 * Date butoir de réponse
	*/
	echo $this->SearchForm->dateRange('Search.Recourgracieux.dtbutoir', array('domain' => 'recoursgracieux'));

	/**
	 * Date de réception du dossier dans le service
	*/
	echo $this->SearchForm->dateRange('Search.Recourgracieux.dtreception', array('domain' => 'recoursgracieux'));

	/**
	 * Origine du dossier
	*/
	echo $this->Xform->input(
		'Search.Recourgracieux.originerecoursgracieux_id',
		array(
			'label' => __m('Recourgracieux::search::originerecoursgracieux_id'),
			'type' => 'select',
			'empty' => true,
			'options' => $options['Originerecoursgracieux']['origine']
		)
	);

	/**
	 * Date d’affectation du dossier
	*/
	echo $this->SearchForm->dateRange('Search.Recourgracieux.dtaffectation', array('domain' => 'recoursgracieux'));

	/**
	 * Gestionnaire du dossier
	*/
	echo $this->Xform->input(
		'Search.Recourgracieux.poledossierpcg66_id',
		array(
			'label' => __m('Recourgracieux::search::poledossierpcg66_id'),
			'type' => 'select',
			'empty' => true,
			'options' => $options['Poledossierpcg66']['name']
		)
	);
	echo $this->Xform->input(
		'Search.Recourgracieux.user_id',
		array(
			'label' => __m('Recourgracieux::search::user_id'),
			'type' => 'select',
			'empty' => true,
			'options' => $options['Dossierpcg66']['prefix_user_id']
		)
	);

	/**
	 * État du dossier
	 */
	echo $this->Xform->input(
		'Search.Recourgracieux.etat',
		array(
			'label' => __m('Recourgracieux::search::etat'),
			'type' => 'select',
			'empty' => true,
			'options' => $options['Recourgracieux']['etat']
		)
	);

	?>
</fieldset>
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

<script type="text/javascript">
document.observe( "dom:loaded", function() {
    dependantSelect( 'SearchRecourgracieuxUserId', 'SearchRecourgracieuxPoledossierpcg66Id' );
} );
</script>