

<?php
	echo $this->Default3->titleForLayout();
	//Visualisation des Rapportstalendscreances

if( empty( $Rapportstalendscreances ) ) {
	echo '<p class="notice">Cette personne ne possède pas de Rapportstalendscreances.</p>';
}else{

	echo "<br/><h2>Rapport</h2>";
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
				'Rapporttalendcreance.message'
			)
		),
		array(
			'paginate' => false,
			'options' => $optionsRapport
		)
	);

	echo "<br/><h2>Rejets</h2>";
	echo $this->Default3->index(
		$Rejetstalendscreances,
		$this->Translator->normalize(
			array(
				'/Creances/fluxadd/#Rejettalendcreance.id#' => array(
					'class' => 'view',
				),
				'Rejettalendcreance.fusion',
				'Rejettalendcreance.matricule',
				'Rejettalendcreance.numdemrsa',
				'Rejettalendcreance.dtdemrsa',
				'Rejettalendcreance.ddratdos',
				'Rejettalendcreance.dfratdos',
				'Rejettalendcreance.toprespdos',
				'Rejettalendcreance.nir',
				'Rejettalendcreance.qual',
				'Rejettalendcreance.nom',
				'Rejettalendcreance.nomnai',
				'Rejettalendcreance.prenom',
				'Rejettalendcreance.dtnai',
				'Rejettalendcreance.nomcomnai',
				'Rejettalendcreance.typedtnai',
				'Rejettalendcreance.typeparte',
				'Rejettalendcreance.ideparte',
				'Rejettalendcreance.topvalec',
				'Rejettalendcreance.sexe',
				'Rejettalendcreance.rgadr',
				'Rejettalendcreance.dtemm',
				'Rejettalendcreance.typeadr',
				'Rejettalendcreance.numvoie',
				'Rejettalendcreance.libtypevoie',
				'Rejettalendcreance.nomvoie',
				'Rejettalendcreance.complideadr','Rejettalendcreance.compladr',
				'Rejettalendcreance.lieudist',
				'Rejettalendcreance.numcom',
				'Rejettalendcreance.codepos',
				'Rejettalendcreance.dtimplcre',
				'Rejettalendcreance.natcre','Rejettalendcreance.rgcre',
				'Rejettalendcreance.motiindu','Rejettalendcreance.oriindu',
				'Rejettalendcreance.respindu',
				'Rejettalendcreance.ddregucre','Rejettalendcreance.dfregucre',
				'Rejettalendcreance.dtdercredcretrans',
				'Rejettalendcreance.mtsolreelcretrans',
				'Rejettalendcreance.mtinicre',
				'Rejettalendcreance.moismoucompta',
				'Rejettalendcreance.liblig2adr','Rejettalendcreance.liblig3adr',
				'Rejettalendcreance.liblig5adr','Rejettalendcreance.liblig6adr','Rejettalendcreance.liblig7adr',
			)
		),
		array(
			'paginate' => false,
			'options' => $options
		)
	);

}
?>