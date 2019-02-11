

<?php
	echo $this->Default3->titleForLayout();
	//Visualisation des Rapportstalendscreances

if( empty( $Rapportstalendscreances ) ) {
	echo '<p class="notice">Cette personne ne possède pas de Rapportstalendscreances.</p>';
}else{

	$pagination = $this->Xpaginator->paginationBlock( 'Rapporttalendcreance', $this->passedArgs );
	echo $pagination;

	echo $this->Default3->index(
		$Rapportstalendscreances,
		$this->Translator->normalize(
			array(
				'Rapporttalendcreance.flux',
				'Rapporttalendcreance.typeflux',
				'Rapporttalendcreance.natflux',
				'Rapporttalendcreance.dtflux',
				'Rapporttalendcreance.dtref',
				'Rapporttalendcreance.dtexec',
				'Rapporttalendcreance.fichierflux',
				'Rapporttalendcreance.nbtotdosrsatransm',
				'Rapporttalendcreance.nbtotdosrsatransmano',
				'Rapporttalendcreance.nbrejete',
				'Rapporttalendcreance.fichierrejet',
				'Rapporttalendcreance.nbinser',
				'Rapporttalendcreance.nbmaj',
				'Rapporttalendcreance.message',
				'/Rejetstalendscreances/index/#Rapporttalendcreance.id#' => array(
					'class' => 'view',
					'condition' => "'#Rapporttalendcreance.nbrejete#' > 0"
				),
			)
		),
		array(
			'paginate' => false,
			'options' => $options
		)
	);
	echo $pagination;

}
?>