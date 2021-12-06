<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'StatutrdvTyperdv.id' => array( 'empty' => true ),
				'StatutrdvTyperdv.typerdv_id' => array( 'empty' => true ),
				'StatutrdvTyperdv.statutrdv_id' => array( 'empty' => true ),
				'StatutrdvTyperdv.nbabsenceavantpassagecommission',
				'StatutrdvTyperdv.typecommission' => array( 'empty' => true ),
				'StatutrdvTyperdv.motifpassageep' => array( 'type' => 'text' ),
				'StatutrdvTyperdv.actif'
			)
		)
	);
?>
<script type="text/javascript">
	document.observe( "dom:loaded", function() {
		observeDisableFieldsOnValue(
			'StatutrdvTyperdvTypecommission',
			['StatutrdvTyperdvMotifpassageep'],
			['orientation'],
			true,
			false
		);
	});
</script>