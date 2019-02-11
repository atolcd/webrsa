<?php
	$this->pageTitle = __d( 'offreinsertion', "Offresinsertion::{$this->action}" );

	echo "<h2>Liste des pièces liées à l'action '".Set::classicExtract( $actioncandidat, 'Actioncandidat.name' )."'</h2>";
	echo $this->Fileuploader->element( 'Actioncandidat', $fichiers, $actioncandidat, $options['Actioncandidat']['haspiecejointe'] );

	$urlParams = Hash::flatten( $this->request->params['named'], '__' );

	echo $this->Default->button(
		'back',
		array_merge(
			array(
				'controller' => 'offresinsertion',
				'action'     => 'index'
			),
			$urlParams
		),
		array(
			'id' => 'Back'
		)
	);
?>
<div class="clearer"><hr /></div>