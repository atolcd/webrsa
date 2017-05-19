<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Typeaideapre66.id' => array( 'type' => 'hidden' ),
				'Typeaideapre66.themeapre66_id' => array( 'empty' => true ),
				'Typeaideapre66.name',
				'Typeaideapre66.isincohorte' => array( 'type' => 'radio'),
				'Typeaideapre66.objetaide' => array( 'type' => 'text' ),
				'Typeaideapre66.plafond',
				'Typeaideapre66.plafondadre',
				'Typeaideapre66.typeplafond',
				'Pieceaide66.Pieceaide66' => array(
					'multiple' => 'checkbox',
					'fieldset' => true,
					'class' => 'col2'
				),
				'Piececomptable66.Piececomptable66' => array(
					'multiple' => 'checkbox',
					'fieldset' => true,
					'class' => 'col2'
				)
			)
		)
	);
?>
<script type="text/javascript">
//<![CDATA[
	document.observe( 'dom:loaded', function() {
		insertButtonsCocherDecocher(
			$$( 'fieldset' )[1],
			"input[name=\"data[Pieceaide66][Pieceaide66][]\"]"
		);
		insertButtonsCocherDecocher(
			$$( 'fieldset' )[2],
			"input[name=\"data[Piececomptable66][Piececomptable66][]\"]"
		);
	} );
//]]>
</script>