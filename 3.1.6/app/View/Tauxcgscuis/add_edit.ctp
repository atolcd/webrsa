<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'tauxcgcui', "Tauxcgscuis::{$this->action}" )
	);

	echo $this->Xform->create();

	echo $this->Default2->subform(
		array(
			'Tauxcgcui.id' => array( 'type' => 'hidden' ),
			'Tauxcgcui.typecui',
			'Tauxcgcui.secteurcui_id'
		),
		array(
			'options' => $options
		)
	);

	echo '<fieldset id="isaci" class="noborder">';
		echo $this->Default2->subform(
			array(
				'Tauxcgcui.isaci',
			),
			array(
				'options' => $options
			)
		);
	echo '</fieldset>';

	echo $this->Default2->subform(
		array(
			'Tauxcgcui.tauxmin',
			'Tauxcgcui.tauxmax',
			'Tauxcgcui.tauxnominal'
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
<script type="text/javascript">
//<![CDATA[
	document.observe( "dom:loaded", function() {
		observeDisableFieldsetOnValue(
			'TauxcgcuiSecteurcuiId',
			$( 'isaci' ),
			['<?php echo implode( "', '", $secteur_isnonmarchand_id );?>'],
			false,
			true
		);
	});
//]]>
</script>