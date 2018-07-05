<?php
	/**
	 * Valeurs par défaut du filtre de recherche de la validationcs des CERs.
	 */
	Configure::write(
	'Filtresdefaut.Cohortescers93_validationcs',
	  	array(
	  		'Search' => array(
				'Contratinsertion' => array(
					'dernier' => true
				),
				'Dossier' => array(
					'dernier' => true
				),
				'Cer93' => array (
					'positioncer_choice' => true,
					'positioncer' => array('04premierelecture'), 
				)
			)
		)
	);
?>