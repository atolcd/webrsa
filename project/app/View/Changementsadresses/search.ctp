<?php
	echo $this->element(
		'ConfigurableQuery/search',
		array(
			'customSearch' => $this->fetch('custom_search_filters'),
			'exportcsv' => array('action' => 'exportcsv'),
			'modelName' => 'Dossier'
		)
	);