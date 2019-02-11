<?php
	$index = 0;
	$fields = array();
	foreach( $options['Suiviaideapretypeaide']['typeaide'] as $typeaide => $label ) {
		$fields = array_merge(
			$fields,
			array(
				"Suiviaideapretypeaide.{$index}.id",
				"Suiviaideapretypeaide.{$index}.suiviaideapre_id" => array(
					'label' => $label,
					'options' => $options['Suiviaideapretypeaide']['suiviaideapre_id'],
					'empty' => true
				),
				"Suiviaideapretypeaide.{$index}.typeaide" => array(
					'type' => 'hidden',
					'value' => $typeaide
				)
			)
		);
		$index++;
	}

	echo $this->element( 'WebrsaParametrages/add_edit', array( 'fields' => $fields ) );
?>