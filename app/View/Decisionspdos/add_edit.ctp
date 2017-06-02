<?php
	$departement = Configure::read( 'Cg.departement' );

	$fields = array(
		'Decisionpdo.id',
		'Decisionpdo.libelle',
		'Decisionpdo.clos' => array( 'type' => 'radio' ),
		'Decisionpdo.isactif' => array( 'type' => 'radio' )
	);

	if( 66 === $departement ) {
		$fields = array_merge(
			$fields,
			array(
				'Decisionpdo.cerparticulier' => array( 'type' => 'radio' ),
				'Decisionpdo.decisioncerparticulier' => array( 'type' => 'select', 'empty' => true )
			)
		);
	}
	else {
		$fields = array_merge(
			$fields,
			array( 'Decisionpdo.modeleodt' )
		);
	}

	echo $this->element( 'WebrsaParametrages/add_edit', array( 'fields' => $fields ) );
?>
<?php if( 66 === $departement ): ?>
<script type="text/javascript">
//<![CDATA[
	document.observe("dom:loaded", function() {
		observeDisableFieldsOnRadioValue(
			'<?php echo Inflector::camelize( Inflector::singularize( $this->request->params['controller'] )."_{$this->request->params['action']}" ).'Form';?>',
			'data[Decisionpdo][cerparticulier]',
			[ 'DecisionpdoDecisioncerparticulier' ],
			'O',
			true,
			true
		);
	});
//]]>
</script>
<?php endif; ?>