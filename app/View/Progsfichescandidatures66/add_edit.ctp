<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Progfichecandidature66.id',
				'Progfichecandidature66.name',
				'Progfichecandidature66.isactif' => array( 'empty' => true )
			)
		)
	);
?>