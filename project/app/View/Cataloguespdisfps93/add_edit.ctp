<?php
	echo $this->Default3->titleForLayout( array(), array( 'msgid' => "/Cataloguespdisfps93/{$this->request->params['action']}/{$modelName}/:heading" ) );

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ), array( 'inline' => false ) );
	}

	echo $this->Default3->form(
		$fields,
		array(
			'options' => $options
		)
	);

	echo $this->Observer->dependantSelect( $dependantFields	);

	if(isset( $listCat )) {
		echo '<h2>' . __m('Thematiquefp93.listeCategorie') . '</h2>';
		echo $this->Default3->index(
			$listCat,
			array(
				'Categoriefp93.name',
				'Categoriefp93.tableau4_actif',
				'Categoriefp93.tableau5_actif',
			),
			array(
				'options' => array(),
				'paginate' => false
			)
		);
	}
?>
<?php if( $modelName === 'Actionfp93' ):?>
<script type="text/javascript">
	<?php $domId = $this->Html->domId( 'Actionfp93.adresseprestatairefp93_id' );?>
	Element.observe( '<?php echo $domId;?>', 'change', function() {
		new Ajax.AbortableUpdater(
			'CoordonneesPrestataire',
			'<?php echo Router::url( array( 'controller' => 'fichesprescriptions93', 'action' => 'ajax_prestataire' ) );?>',
			{
				parameters: {
					'data[Ficheprescription93][adresseprestatairefp93_id]': $F( '<?php echo $domId;?>' )
				}
			}
		);
	} );

	document.observe( 'dom:loaded', function() {
		// Ajout de la div permettant de connaître les détails de l'adresse
		var div = new Element( 'div', { id: 'CoordonneesPrestataire' } );
		$( 'Actionfp93Adresseprestatairefp93Id' ).up( 'div' ).insert( { 'after' : div } );

		$( '<?php echo $domId;?>' ).simulate( 'change' );
	} );

</script>
<?php endif;?>