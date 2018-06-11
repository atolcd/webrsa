

<?php
	echo $this->Default3->titleForLayout();
	//Visualisation des Créances Alimentaires

if( empty( $creancesalimentaires ) ) {

	echo '<p class="notice">Cette personne ne possède pas de Créances Alimentaires.</p>';
}else{

	echo $this->Default3->index(
		$creancesalimentaires,
		$this->Translator->normalize(
			array(
				'Creancealimentaire.etatcrealim',
				'Creancealimentaire.ddcrealim',
				'Creancealimentaire.dfcrealim',
				'Creancealimentaire.orioblalim',
				'Creancealimentaire.motidiscrealim',
				'Creancealimentaire.commcrealim',
				'Creancealimentaire.mtsancrealim',
				'Creancealimentaire.topdemdisproccrealim',
				'Creancealimentaire.engproccrealim',
				'Creancealimentaire.verspa',
				'Creancealimentaire.topjugpa',
			)
		),
		array(
			'paginate' => false,
			'options' => $options
		)
	);

}
?>