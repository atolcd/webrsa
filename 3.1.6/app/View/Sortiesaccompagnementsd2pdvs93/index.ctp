<?php
	echo $this->Default3->titleForLayout();

	echo $this->Default3->actions(
		array(
			"/Sortiesaccompagnementsd2pdvs93/add" => array(
				'disabled' => !$this->Permissions->check( 'Sortiesaccompagnementsd2pdvs93', 'add' )
			),
		)
	);

	echo $this->Default3->index(
		$sortiesaccompagnementsd2pdvs93,
		array(
			'Sortieaccompagnementd2pdv93.name',
			'Parent.name',
			'/Sortiesaccompagnementsd2pdvs93/edit/#Sortieaccompagnementd2pdv93.id#' => array(
				'disabled' => !$this->Permissions->check( 'Sortiesaccompagnementsd2pdvs93', 'edit' )
			),
			'/Sortiesaccompagnementsd2pdvs93/delete/#Sortieaccompagnementd2pdv93.id#' => array(
				'disabled' => '( \'#Sortieaccompagnementd2pdv93.occurences#\' || !\''.$this->Permissions->check( 'Sortiesaccompagnementsd2pdvs93', 'delete' ).'\' )',
				'confirm' => true
			),
		)
	);

	echo $this->Default3->actions(
		array(
			"/Parametrages/modulefse93" => array(
				'domain' => 'sortiesaccompagnementsd2pdvs93',
				'class' => 'back',
				'disabled' => !$this->Permissions->check( 'parametrages', 'modulefse93' )
			),
		)
	);
?>