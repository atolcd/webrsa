<?php
	$class = Inflector::classify( $theme );
	echo "<div id=\"$class\"><h3 class=\"title\">".__d( 'dossierep',  'ENUM::THEMEEP::'.Inflector::tableize( $theme ) )."</h3>";

	if( in_array( 'dossierseps::choose', $etatsActions[$commissionep['Commissionep']['etatcommissionep']] ) ) {
		echo '<ul class="actionMenu"><li>'.$this->Xhtml->affecteLink(
            'Affecter les dossiers',
            array( 'controller' => 'dossierseps', 'action' => 'choose', Set::classicExtract( $commissionep, 'Commissionep.id' ), '#' => $theme )
        ).' </li></ul>';
    }
    else {
        echo '<li><span class="disabled"> Affecter les dossiers </span></li>';
    }

	if( empty( $dossiers[$theme] ) ) {
		echo '<p class="notice">Il n\'existe aucun dossier de cette thématique associé à cette commission d\'EP.</p>';
	}
	else {
		echo $this->Default2->index(
			$dossiers[$theme],
			array(
				'Personne.qual',
				'Personne.nom',
				'Personne.prenom',
				'Personne.dtnai',
				'Adresse.nomcom',
				'Dossierep.created',
				'Dossierep.themeep',
				'Passagecommissionep.etatdossierep',
				'Passagecommissionep.heureseance',
				'Cov58.datecommission' => array( 'label' => 'Proposition validée en COV le' ),
				'Foyer.enerreur' => array( 'type' => 'string', 'sort' => false, 'class' => 'foyer_enerreur' ),
			),
			array(
				'actions' => array(
					'Dossierseps::view' => array( 'label' => 'Voir', 'url' => array( 'controller' => 'historiqueseps', 'action' => 'index', '#Personne.id#' ), 'class' => 'external' ),
					'Commissionseps::printConvocationBeneficiaire' => array( 'url' => array( 'controller' => 'commissionseps', 'action' => 'printConvocationBeneficiaire', '#Passagecommissionep.id#' ), 'disabled' => empty( $disableConvocationBeneficiaire ))
				),
				'options' => $options,
				'id' => $theme,
				'trClass' => $trClass,
			)
		);
	}
	echo "</div>";
?>