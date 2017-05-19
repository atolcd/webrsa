<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Sitecov58.id',
				'Sitecov58.name',
				'Zonegeographique.Zonegeographique' => array(
					'fieldset' => true,
					'label' => 'Zones géographiques',
					'multiple' => 'checkbox',
					'class' => 'col3'
				)
			)
		)
	);
?>
<script type="text/javascript">
//<![CDATA[
	document.observe( 'dom:loaded', function() {
		insertButtonsCocherDecocher(
			$$( 'fieldset' )[0],
			"input[name=\"data[Zonegeographique][Zonegeographique][]\"]"
		);
	} );
//]]>
</script>