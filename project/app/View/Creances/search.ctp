<?php
	$departement = (int)Configure::read( 'Cg.departement' );
	$user_type = $this->Session->read( 'Auth.User.type' );

	$actions = array();
?>

<?php $this->start( 'custom_search_filters' );?>

<fieldset>
	<legend><?php echo __m( 'Search.Creances' ); ?></legend>
	<?php
		echo $this->Xform->input(
			'Search.Creance.orgcre',
			array(
				'label' => __d('creances','Creance::search::orgcre'),
				'type' => 'select',
				'empty' => true,
				'options' => $options['Creance']['orgcre']
			)
		);
		echo $this->Xform->input(
			'Search.Creance.motiindu',
			array(
				'label' => __d('creances','Creance::search::motiindu'),
				'type' => 'select',
				'empty' => true,
				'options' => $options['Creance']['motiindu']
			)
		);
		echo "<fieldset><legend> ".__d('creances','Creance::search::dtimplcre')."</legend>";
		echo $this->Xform->input(
			'Search.Creance.dtimplcre_from',
			array(
				'label' => __d('creances','Search.Creance.dtimplcre_from'),
				'type' => 'date',
				'dateFormat'=>'DMY',
				'maxYear'=>date('Y')+1,
				'minYear'=> '2009',
				'empty' => true
			)
		) ;
		echo $this->Xform->input(
			'Search.Creance.dtimplcre_to',
			array(
				'label' => __d('creances','Search.Creance.dtimplcre_to'),
				'type' => 'date',
				'dateFormat'=>'DMY',
				'maxYear'=>date('Y')+2,
				'minYear'=> '2009' ,
				'empty' => true
			)
		) ;
		echo "</fieldset>";
		echo "<fieldset><legend> ".__d('creances','Creance::search::moismoucompta')."</legend>";
		echo $this->Xform->input(
			'Search.Creance.moismoucompta_from',
			array(
				'label' => __d('creances','Search.Creance.moismoucompta_from'),
				'type' => 'date',
				'dateFormat'=>'DMY',
				'maxYear'=>date('Y')+1,
				'minYear'=> '2009',
				'empty' => true
			)
		) ;
		echo $this->Xform->input(
			'Search.Creance.moismoucompta_to',
			array(
				'label' => __d('creances','Search.Creance.moismoucompta_to'),
				'type' => 'date',
				'dateFormat'=>'DMY',
				'maxYear'=>date('Y')+2,
				'minYear'=> '2009' ,
				'empty' => true
			)
		) ;
		echo "</fieldset>";
		echo $this->Xform->input(
			'Search.Creance.etat',
			array(
				'label' => __d('creances','Creance::search::etat'),
				'type' => 'select',
				'empty' => true,
				'options' => $options['Creance']['etat']
			)
		);

		$options['Creance']['hastitrecreancier'] = array(1 => 'Oui', 0 => 'Non');
		if ( Configure::read( 'Creances.Titrescreanciers.enabled' ) ) {
			//Has Titre créancier
			echo $this->Xform->input(
				'Search.Creance.hastitrecreancier',
				array(
					'label' => __m('Creance::search::hastitrecreancier'),
					'type' => 'select',
					'empty' => true,
					'options' =>  $options['Creance']['hastitrecreancier']
				)
			);
		}

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