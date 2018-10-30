

<?php
	echo $this->Default3->titleForLayout();
	//Visualisation des RapportsTalendsCreances

if( empty( $RapportsTalendsCreances ) ) {

	echo '<p class="notice">Cette personne ne possède pas de RapportsTalendsCreances.</p>';
}else{

	echo $this->Default3->index(
		$creances,
		$this->Translator->normalize(
			array(
				'RapportTalendCreance.flux',
				'RapportTalendCreance.dtexec',
				'RapportTalendCreance.fichierflux',
				'RapportTalendCreance.nbrejete',
				'RapportTalendCreance.fichierrejet',
				'RapportTalendCreance.nbinser',
				'RapportTalendCreance.nbmaj',
				'RapportTalendCreance.message'
			)
		),
		array(
			'paginate' => false,
			'options' => $options
		)
	);

}
?>