<?php
	$departement = (int)Configure::read( 'Cg.departement' );
	$user_type = $this->Session->read( 'Auth.User.type' );

	$actions = array();
?>

<?php $this->start( 'custom_search_filters' );?>

<fieldset>
	<legend><?php echo __m( 'Search.Recoursgracieux' ); ?></legend>
	<?php
	/**
	 * Date d’arrivée du dossier au CD
		C’est un champ de date.
	*/
	echo "<fieldset><legend> ".__m('Recourgracieux::search::dtarrivee')."</legend>";
		echo $this->Xform->input(
			'Search.Recourgracieux.dtarrivee_from',
			array(
				'label' => ' From',
				'type' => 'date',
				'dateFormat'=>'DMY',
				'maxYear'=>date('Y')+1,
				'minYear'=> '2009',
				'empty' => true
			)
		) ;
		echo $this->Xform->input(
			'Search.Recourgracieux.dtarrivee_to',
			array(
				'label' => 'To ',
				'type' => 'date',
				'dateFormat'=>'DMY',
				'maxYear'=>date('Y')+2,
				'minYear'=> '2009' ,
				'empty' => true
			)
		) ;
	echo "</fieldset>";
	/**
	 * Date butoir de réponse
		C’est un champ de date.
	*/
	echo "<fieldset><legend> ".__m('Recourgracieux::search::dtbutoir')."</legend>";
		echo $this->Xform->input(
			'Search.Recourgracieux.dtbutoir_from',
			array(
				'label' => ' From',
				'type' => 'date',
				'dateFormat'=>'DMY',
				'maxYear'=>date('Y')+1,
				'minYear'=> '2009',
				'empty' => true
			)
		) ;
		echo $this->Xform->input(
			'Search.Recourgracieux.dtbutoir_to',
			array(
				'label' => 'To ',
				'type' => 'date',
				'dateFormat'=>'DMY',
				'maxYear'=>date('Y')+2,
				'minYear'=> '2009' ,
				'empty' => true
			)
		) ;
	echo "</fieldset>";
	/**
	 * Date de réception du dossier dans le service
		C’est un champ de date.
	*/
	echo "<fieldset><legend> ".__m('Recourgracieux::search::dtreception')."</legend>";
		echo $this->Xform->input(
			'Search.Recourgracieux.dtreception_from',
			array(
				'label' => ' From',
				'type' => 'date',
				'dateFormat'=>'DMY',
				'maxYear'=>date('Y')+1,
				'minYear'=> '2009',
				'empty' => true
			)
		) ;
		echo $this->Xform->input(
			'Search.Recourgracieux.dtreception_to',
			array(
				'label' => 'To ',
				'type' => 'date',
				'dateFormat'=>'DMY',
				'maxYear'=>date('Y')+2,
				'minYear'=> '2009' ,
				'empty' => true
			)
		) ;
	echo "</fieldset>";

	/**
	 * Origine du dossier
		C’est une liste déroulante.
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
		C’est un champ de date.
	*/
	echo "<fieldset><legend> ".__m('Recourgracieux::search::dtaffectation')."</legend>";
		echo $this->Xform->input(
			'Search.Recourgracieux.dtaffectation_from',
			array(
				'label' => ' From',
				'type' => 'date',
				'dateFormat'=>'DMY',
				'maxYear'=>date('Y')+1,
				'minYear'=> '2009',
				'empty' => true
			)
		) ;
		echo $this->Xform->input(
			'Search.Recourgracieux.dtaffectation_to',
			array(
				'label' => 'To ',
				'type' => 'date',
				'dateFormat'=>'DMY',
				'maxYear'=>date('Y')+2,
				'minYear'=> '2009' ,
				'empty' => true
			)
		) ;
	echo "</fieldset>";
	/**
	 * Gestionnaire du dossier
		C’est une liste déroulante.
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
		C’est une liste déroulante.
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