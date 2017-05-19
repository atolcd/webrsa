<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<h1>
<?php
	if( $this->action == 'add' ) {
		echo $this->pageTitle = 'Ajout d\'un regroupement d\'E.P.';
	}
	else {
		echo $this->pageTitle = 'Modification d\'un regroupement d\'E.P.';
	}
?>
</h1>

<?php
	$EpDepartement = Configure::read( 'Cg.departement' );
	if( empty( $EpDepartement ) || !in_array( $EpDepartement, array( 58, 66, 93 ) ) ) {
		echo $this->Xhtml->tag( 'p', 'Veuillez contacter votre adminitrateur afin qu\'il ajoute le paramètre de configuration Cg.departement dans le fichier webrsa.inc', array( 'class' => 'error' ) );
	}

	echo $this->Xform->create( null );
	if( $this->action == 'edit' ) {
		echo $this->Xform->input( 'Regroupementep.id', array( 'type' => 'hidden' ) );
	}
	echo $this->Xform->input( 'Regroupementep.name', array( 'domain' => 'regroupementep' ) );

	// Le CG 93 ne souhaite pas voir ces choix: pour eux, tout se décide
	// au niveau cg, et toutes les eps traitent potentiellement de tous
	// les thèmes
	if( Configure::read( 'Cg.departement' ) == 93 ) {
		echo $this->Default->subform(
			array(
				'Regroupementep.reorientationep93' => array( 'type' => 'hidden', 'value' => 'decisioncg' ),
				'Regroupementep.nonrespectsanctionep93' => array( 'type' => 'hidden', 'value' => 'decisioncg' ),
				'Regroupementep.radiepoleemploiep93' => array( 'type' => 'hidden', 'value' => 'decisioncg' ),
				'Regroupementep.nonorientationproep93' => array( 'type' => 'hidden', 'value' => 'decisioncg' ),
				'Regroupementep.signalementep93' => array( 'type' => 'hidden', 'value' => 'decisioncg' ),
				'Regroupementep.contratcomplexeep93' => array( 'type' => 'hidden', 'value' => 'decisioncg' ),
				'Regroupementep.nbminmembre' => array( 'type' => 'hidden', 'value' => 0 ),
				'Regroupementep.nbmaxmembre' => array( 'type' => 'hidden', 'value' => 0 ),
			)
		);
	}
	// On laisse la possibilité de choisir comme avant pour le CG 58
	elseif( Configure::read( 'Cg.departement' ) == 58 ) {
		echo $this->Default->subform(
			array(
				'Regroupementep.nonorientationproep58' => array( 'type' => 'hidden', 'value' => 'decisionep' ),
				'Regroupementep.regressionorientationep58' => array( 'type' => 'hidden', 'value' => 'decisionep' ),
				'Regroupementep.sanctionep58' => array( 'type' => 'hidden', 'value' => 'decisionep' ),
				'Regroupementep.sanctionrendezvousep58' => array( 'type' => 'hidden', 'value' => 'decisionep' ),
				'Regroupementep.nbminmembre' => array( 'type' => 'hidden', 'value' => 0 ),
				'Regroupementep.nbmaxmembre' => array( 'type' => 'hidden', 'value' => 0 ),
			)
		);
	}
	// Le choix est également possible pour le CG 66
	elseif( Configure::read( 'Cg.departement' ) == 66 ) {
		echo $this->Xhtml->tag(
			'fieldset',
			$this->Xhtml->tag(
				'legend',
				'Thématiques 66'
			).
			$this->Default->subform(
				array(
					'Regroupementep.saisinebilanparcoursep66' => array( 'required' => true ),
					'Regroupementep.saisinepdoep66' => array( 'required' => true ),
					'Regroupementep.defautinsertionep66' => array( 'required' => true ),
				),
				array(
					'options' => $options
				)
			),
			array(
				'label'=>'Thématiques 66'
			)
		);

		echo $this->Xhtml->tag(
			'fieldset',
			$this->Xhtml->tag(
				'legend',
				'Présence des membres'
			).
			$this->Default->subform(
				array(
					'Regroupementep.nbminmembre' => array( 'label' => 'Nombre minimum de membre pour une commission (0 si pas de minimum)', 'required' => true ),
					'Regroupementep.nbmaxmembre' => array( 'label' => 'Nombre maximum de membre pour une commission (0 si pas de maximum)', 'required' => true )
				),
				array(
					'options' => $options
				)
			),
			array(
				'label'=>'Présence des membres'
			)
		);
	}

	echo $this->Xform->end( __( 'Save' ) );

	echo $this->Default->button(
		'back',
		array(
			'controller' => 'regroupementseps',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);
?>