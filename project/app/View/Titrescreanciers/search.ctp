<?php
	$departement = (int)Configure::read( 'Cg.departement' );
	$user_type = $this->Session->read( 'Auth.User.type' );

	$actions = array();
?>

<?php $this->start( 'custom_search_filters' );?>

<fieldset>
	<legend><?php echo __m( 'Search.Titrecreancier' ); ?></legend>
	<?php
		echo "<fieldset><legend> ".__m('Titrecreancier::search::moismoucompta')."</legend>";
		echo $this->Xform->input(
			'Search.Titrecreancier.moismoucompta_from',
			array(
				'label' => ' From',
				'type' => 'date',
				'dateFormat'=>'DMY',
				'maxYear'=>date('Y')+1,
				'minYear'=> '2009',
				'empty' => true
			)
		);
		echo $this->Xform->input(
			'Search.Titrecreancier.moismoucompta_to',
			array(
				'label' => 'To ',
				'type' => 'date',
				'dateFormat'=>'DMY',
				'maxYear'=>date('Y')+2,
				'minYear'=> '2009' ,
				'empty' => true
			)
		);
		echo "</fieldset>";

		echo $this->Xform->input(
			'Search.Titrecreancier.etat',
			array(
				'label' => __m('Titrecreancier::search::etat'),
				'type' => 'select',
				'empty' => true,
				'options' => $options['Titrecreancier']['etat']
			)
		);

		echo $this->Xform->input(
			'Search.Titrecreancier.numtitr',
			array(
				'label' => __m('Titrecreancier::search::numtitr'),
				'type' => 'text',
				'empty' => true,
			)
		);

		echo $this->Xform->input(
			'Search.Titrecreancier.numbordereau',
			array(
				'label' => __m('Titrecreancier::search::numbordereau'),
				'type' => 'text',
				'empty' => true,
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