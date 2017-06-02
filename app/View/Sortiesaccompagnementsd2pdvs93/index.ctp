<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Sortieaccompagnementd2pdv93.name',
				'Parent.name',
				'/Sortiesaccompagnementsd2pdvs93/edit/#Sortieaccompagnementd2pdv93.id#' => array(
					'title' => true
				),
				'/Sortiesaccompagnementsd2pdvs93/delete/#Sortieaccompagnementd2pdv93.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Sortieaccompagnementd2pdv93.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#modulefse93'
		)
	);
?>