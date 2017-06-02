<?php
	// TODO: ailleurs ?
	$default = array(
		'identifiant' => 'B90J',
		'codegestion' => '90JB0',
		'codepaiement' => '22',
		'comptoir' => '0000',
		'codemonnaie' => 'E'
	);

	$lines = array();
	$matriculePcd = null;
	$matriculeCnt = 0;

	foreach( $apres as $apre ) {
		$matricule = Set::classicExtract( $apre, 'Dossier.matricule' );

		if( $matricule != $matriculePcd ) {
			$matriculePcd = $matricule;
			$matriculeCnt = 0;
		}
		$matriculeCnt++;

		$row = array(
			'matricule' => $matricule,
			'codebanque' => Set::classicExtract( $apre, 'Paiementfoyer.etaban' ),
			'codeagence' => Set::classicExtract( $apre, 'Paiementfoyer.guiban' ),
			'nocompte' => Set::classicExtract( $apre, 'Paiementfoyer.numcomptban' ),
			'clerib' => Set::classicExtract( $apre, 'Paiementfoyer.clerib' ),
			'domiciliation' => Set::classicExtract( $apre, 'Domiciliationbancaire.libelledomiciliation' ),
			'etatcivil' => Set::classicExtract( $apre, 'Paiementfoyer.titurib' ),
			'nometprénom' => Set::classicExtract( $apre, 'Paiementfoyer.nomprenomtiturib' ),
			'montant' => Set::classicExtract( $apre, 'Apre.allocation' ),
		);

		$tmp = Set::merge( $default, $row );

		if( validRib( $tmp['codebanque'], $tmp['codeagence'], $tmp['nocompte'], $tmp['clerib'] ) ) {
			$etatcivil = 1;
			if( $tmp['etatcivil'] == 'MLE' ) {
				$etatcivil = 3;
			}
			else if( $tmp['etatcivil'] == 'MME' ) {
				$etatcivil = 2;
			}
			else if( $tmp['etatcivil'] == 'MON' ) {
				$etatcivil = 1;
			}

			$line = array(
				$tmp['identifiant'],
				'000'.( 10000 + $matriculeCnt ).str_pad( substr( $tmp['matricule'], 0, 7 ), 7, '0', STR_PAD_LEFT ),
				$tmp['codegestion'],
				$tmp['codepaiement'],
				$tmp['comptoir'],
				$tmp['codebanque'],
				$tmp['codeagence'],
				$tmp['nocompte'],
				str_pad( $tmp['clerib'], 2, '0', STR_PAD_LEFT ),
				str_pad( '', 2, ' ' ),
				str_pad( trim( $tmp['domiciliation'] ), 24, ' ', STR_PAD_RIGHT ),
				$etatcivil,
				str_pad( $tmp['nometprénom'], 30, ' ', STR_PAD_RIGHT ),
				$tmp['codemonnaie'],
				str_pad( '', 25, ' ' ),
				str_pad( round( $tmp['montant'] * 100 ), 8, '0', STR_PAD_LEFT ),
			);

			$lines[] = implode( '', $line );
		}
		else {
			// FIXME ?
		}
	}
	Configure::write( 'debug', 0 );
	header('Content-Type: application/octet-stream; charset='.Configure::read( 'App.encoding' ) );
	header('Content-Disposition: attachment; filename="APRE-HOPAYRA'.date( 'm', strtotime( $etatliquidatif['Etatliquidatif']['datecloture'] ) ).'"');
	echo implode( "\r\n", $lines )."\r\n";
?>