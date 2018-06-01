<?php

// Fait par le CG93
// Auteur : Harry ZARKA <hzarka@cg93.fr>, 2010.

	$this->pageTitle = 'Visionneuse';

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );

	echo $this->Xhtml->link(
		'Recalculer les rejets',
		array(
			'controller'=>'visionneuses',
			'action'=>'calculrejetes',
		)
	);

	if( empty( $visionneuses ) ) {
		echo $this->Xhtml->tag( 'p', 'Aucun fichier intégré pour l\'instant.', array( 'class' => 'notice' ) );
	}
	else {
        $pagination = $this->Xpaginator->paginationBlock( 'Visionneuse', $this->passedArgs );

		//----------------------------------------------------------------------

		$headers = array(
			'Flux',
			'Nom',
			'Date début',
			'Date fin',
			'Durée',
			'Dossiers',
			'Rejetés',
			'Nouveaux',
			'MAJ',
			'Pers Créé',
			'Pers MAJ',
			'DSP Créé',
			'DSP MAJ',
		);

		$thead = $this->Xhtml->tag( 'thead', $this->Xhtml->tableHeaders( $headers ) );

		/// Corps du tableau
		$rows = array();

		foreach ($visionneuses as $visionneuse){
			$duree = strtotime(Set::classicExtract( $visionneuse, 'Visionneuse.dtfin' ))-
			strtotime(Set::classicExtract( $visionneuse, 'Visionneuse.dtdeb' ));

			$dossier = Set::classicExtract( $visionneuse, 'Visionneuse.nbrejete' )+
			Set::classicExtract( $visionneuse, 'Visionneuse.nbinser' )+
			Set::classicExtract( $visionneuse, 'Visionneuse.nbmaj' );

			$rejet = Set::classicExtract( $visionneuse, 'Visionneuse.nbrejete' );

			$rows[] = array(
				Set::classicExtract( $visionneuse, 'Visionneuse.flux' ),
				Set::classicExtract( $visionneuse, 'Visionneuse.nomfic' ),
				strftime( '%d/%m/%Y %H:%M:%S' , strtotime( Set::classicExtract( $visionneuse, 'Visionneuse.dtdeb') ) ),
				strftime( '%d/%m/%Y %H:%M:%S' , strtotime( Set::classicExtract( $visionneuse, 'Visionneuse.dtfin') ) ),
				strftime('%H:%M:%S', $duree),
				$dossier,
				(0<$rejet)?$this->Xhtml->Link(
					$rejet,
					array( 'controller' => 'rejet_historique', 'action' => 'affrej',$visionneuse['Visionneuse']['nomfic'] ),
					array( 'enabled' => true )
				):'0',
				Set::classicExtract( $visionneuse, 'Visionneuse.nbinser' ),
				Set::classicExtract( $visionneuse, 'Visionneuse.nbmaj' ),
				Set::classicExtract( $visionneuse, 'Visionneuse.perscree' ),
				Set::classicExtract( $visionneuse, 'Visionneuse.persmaj' ),
				Set::classicExtract( $visionneuse, 'Visionneuse.dspcree' ),
				Set::classicExtract( $visionneuse, 'Visionneuse.dspmaj' ),
			);
		}

		$tbody = $this->Xhtml->tag( 'tbody', $this->Xhtml->tableCells( $rows, array( 'class' => 'odd' ), array( 'class' => 'even' ) ) );

		echo $pagination;
		echo $this->Xhtml->tag( 'table', $thead.$tbody );
		echo $pagination;

		$options = array(
			'INSTRUCTION' => 'INSTRUCTION',
			'BENEFICIAIRE' => 'BENEFICIAIRE',
			'FINANCIER' => 'FINANCIER',
		);

		echo '<fieldset><legend>Recherche par fichier</legend>';

		echo $this->Default->search(
		array(
			'Visionneuse.flux' => array( 'type' => 'select','options' => $options,'empty' => 'Choissisez votre flux'),
			)
		);
	}
?>
</fieldset>