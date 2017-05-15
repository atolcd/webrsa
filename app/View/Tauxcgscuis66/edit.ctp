<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'tauxcgscuis66', "Tauxcgscuis66::{$this->action}" )
	);

	echo $this->Xform->create();
	
	echo $this->Default3->subform(
		array(
			'Tauxcgcui66.id' => array( 'type' => 'hidden' ),
			'Tauxcgcui66.typeformulaire',
            'Tauxcgcui66.secteurmarchand',
            'Tauxcgcui66.typecontrat' => array( 'options' => $options['Tauxcgcui66']['typecontrat_actif']),
		),
		array(
			'options' => $options
		)
	);

	echo $this->Default3->subform(
		array(
			'Tauxcgcui66.tauxfixeregion' => array( 'class' => 'percent' ),
            'Tauxcgcui66.priseenchargeeffectif' => array( 'class' => 'percent' ),
            'Tauxcgcui66.tauxcg' => array( 'class' => 'percent' ),
		),
		array(
			'options' => $options
		)
	);


	echo $this->Html->tag(
		'div',
		 $this->Xform->button( 'Enregistrer', array( 'type' => 'submit' ) )
		.$this->Xform->button( 'Annuler', array( 'type' => 'submit', 'name' => 'Cancel' ) ),
		array( 'class' => 'submit noprint' )
	);

	echo $this->Xform->end();
?>