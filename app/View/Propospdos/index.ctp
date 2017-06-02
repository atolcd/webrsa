<?php
	if (Configure::read( 'nom_form_pdo_cg' ) == 'cg66'){
		$this->pageTitle = 'Décision PCG';
	}
	else{
		$this->pageTitle = 'PDO';
	}

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<h1><?php echo $this->pageTitle;?></h1>
<?php echo $this->element( 'ancien_dossier' );?>

<h2>Détails PDO</h2>

<?php
	echo $this->Default3->actions(
		array(
			"/Propospdos/add/{$personne_id}" => array(
				'title' => false
			)
		)
	);

    echo $this->Default3->index(
        $pdos,
		$this->Translator->normalize(
			array(
				'Typepdo.libelle',
				'Decisionpdo.libelle',
				'Propopdo.motifpdo',
				'Decisionpropopdo.datedecisionpdo',
				'Decisionpropopdo.commentairepdo',
				'Propopdo.nb_fichiers_lies' => array( 'class' => 'integer' ),
				'/Propospdos/view/#Propopdo.id#' => array(
					'title' => false
				),
				'/Propospdos/edit/#Propopdo.id#' => array(
					'title' => false
				),
				'/Propospdos/printCourrier/#Propopdo.id#' => array(
					'class' => 'impression',
					'disabled' => 'empty( "#Decisionpdo.modeleodt#" )',
					'title' => false
				)
			)
		),
        array(
            'options' => $options,
			'paginate' => false
        )
    );
?>