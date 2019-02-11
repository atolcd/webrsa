<?php
	$this->Csv->preserveLeadingZerosInExcel = true;
	$this->Csv->addRow(
		 array(
			'N° Dossier',
			 __d( 'dossier', 'Dossier.matricule' ),
			 'Nom/prénom du bénéficiaire',
			 __d( 'creance', 'Creance.dtimplcre' ),
			 __d( 'creance', 'Creance.natcre' ),
			 __d( 'creance', 'Creance.rgcre' ),
			 __d( 'creance', 'Creance.motiindu' ),
			 __d( 'creance', 'Creance.oriindu' ),
			 __d( 'creance', 'Creance.respindu' ),
			 __d( 'creance', 'Creance.mtsolreelcretrans' ),
			 __d( 'creance', 'Creance.mtinicre' )
		)
	);

	foreach( $dossierEntrantsCreanciers as $index => $dossierEntrantCreancier ) {
		$row = array(
			Set::extract( $dossierEntrantCreancier, 'Dossier.numdemrsa' ),
			Set::extract( $dossierEntrantCreancier, 'Dossier.matricule' ),
			implode(
				' ',
				array(
					Set::extract( $dossierEntrantCreancier, 'Personne.qual' ),
					Set::extract( $dossierEntrantCreancier, 'Personne.nom' ),
					Set::extract( $dossierEntrantCreancier, 'Personne.prenom' )
				)
			),
			$this->Locale->date( 'Date::short', $dossierEntrantCreancier['Creance']['dtimplcre'] ),
			$natcre[$dossierEntrantCreancier['Creance']['natcre']],
			$dossierEntrantCreancier['Creance']['rgcre'],
			$motiindu[$dossierEntrantCreancier['Creance']['motiindu']],
			$oriindu[$dossierEntrantCreancier['Creance']['oriindu']],
			$respindu[$dossierEntrantCreancier['Creance']['respindu']],
			$this->Locale->money( $dossierEntrantCreancier['Creance']['mtsolreelcretrans'] ),
			$this->Locale->money( $dossierEntrantCreancier['Creance']['mtinicre'] )
		);
		$this->Csv->addRow( $row );
	}

	echo $this->Csv->render( 'creances-'.date( 'Ymd-His' ).'.csv' );
?>