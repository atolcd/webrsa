<?php
	$value = Configure::read( 'ResultatsParPage.nombre_par_defaut' );
	if (isset ($this->request->data['Search']['limit'])) {
		$value = $this->request->data['Search']['limit'];
	}

	echo $this->Xform->input(
		"Search.limit",
		array(
			'label' =>  __d( 'search_plugin', "Search.Pagination.resultats_par_page" ),
			'type' => 'radio',
			'options' => Configure::read( 'ResultatsParPage.nombre_de_resultats' ),
			'value' => $value
		)
	);
?>