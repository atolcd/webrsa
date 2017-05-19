<?php
    $this->pageTitle = __d( 'tauxcgscuis66', "Tauxcgscuis66::{$this->action}" );
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
	
	echo $this->Default3->titleForLayout();
	
	echo $this->Default3->actions(
		array(
			"/Tauxcgscuis66/add" => array(
				'disabled' => !$this->Permissions->check( 'Tauxcgscuis66', 'add' )
			),
		)
	);

	// A-t'on des messages à afficher à l'utilisateur ?
	if( !empty( $messages ) ) {
		foreach( $messages as $message => $class ) {
			echo $this->Html->tag( 'p', __d( $this->request->params['controller'], $message ), array( 'class' => "message {$class}" ) );
		}
	}
	
	echo $this->Default3->index(
		$results,
		array(
			'Tauxcgcui66.typeformulaire' => array( 'type' => 'select' ),
            'Tauxcgcui66.secteurmarchand',
            'Tauxcgcui66.typecontrat',
            'Tauxcgcui66.tauxfixeregion',
            'Tauxcgcui66.priseenchargeeffectif',
            'Tauxcgcui66.tauxcg',
			'/Tauxcgscuis66/edit/#Tauxcgcui66.id#' => array(
				'title' => __d('tauxcgscuis66', '/Tauxcgscuis66/edit'),
				'disabled' => !$this->Permissions->check( 'Tauxcgscuis66', 'edit' )
			),
			'/Tauxcgscuis66/delete/#Tauxcgcui66.id#' => array(
				'title' => __d('tauxcgscuis66', '/Tauxcgscuis66/delete'),
				'disabled' => !$this->Permissions->check( 'Tauxcgscuis66', 'delete' )
			),
		),
		array(
			'options' => $options,
			'paginate' => false
		)
	);
	
    echo $this->Default->button(
        'back',
        array(
            'controller' => 'cuis66',
            'action'     => 'indexparams'
        ),
        array(
            'id' => 'Back'
        )
    );
?>
