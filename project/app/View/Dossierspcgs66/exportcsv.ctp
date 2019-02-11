<?php
	function removeList( $data ) {
		return preg_replace('/\<(\/){0,1}[uli]{2}\>/', '',
				str_replace('</li><li>', "\n", $data)
		);
	}

	foreach($results as $key => $result) {
		if (isset($result['Decisiondossierpcg66']['org_id'])) {
			$results[$key]['Decisiondossierpcg66']['org_id'] = removeList( $result['Decisiondossierpcg66']['org_id'] );
		}
		if (isset($result['Traitementpcg66']['situationpdo_id'])) {
			$results[$key]['Traitementpcg66']['situationpdo_id'] = removeList( $result['Traitementpcg66']['situationpdo_id'] );
		}
		if (isset($result['Traitementpcg66']['statutpdo_id'])) {
			$results[$key]['Traitementpcg66']['statutpdo_id'] = removeList( $result['Traitementpcg66']['statutpdo_id'] );
		}
		if (isset($result['Dossierpcg66']['listetraitements'])) {
			$results[$key]['Dossierpcg66']['listetraitements'] = removeList( $result['Dossierpcg66']['listetraitements'] );
		}
	}

	echo $this->Default3->configuredCsv(
		$results,
		array(
			'options' => $options
		)
	);
?>