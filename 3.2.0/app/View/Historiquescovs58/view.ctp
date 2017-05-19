<?php
	echo $this->Default3->titleForLayout();

	echo $this->Default3->view(
		$record,
		array_merge(
			$fields['common'],
			$fields[$record['Dossiercov58']['themecov58']]
		),
		array(
			'options' => $options,
			'th' => true
		)
	);

	echo $this->Html->tag( 'h2', 'Décision' );
	echo $this->Default3->view(
		$record,
		$fields['decisions'.$record['Dossiercov58']['themecov58']],
		array(
			'options' => $options,
			'th' => true
		)
	);
?>