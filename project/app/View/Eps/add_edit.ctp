<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	};

	echo $this->Default3->titleForLayout( $this->request->data );

	$departement = Configure::read( 'Cg.departement' );

	if( 93 == $departement ){
		$fields = array(
			'Ep.id',
			'Ep.name',
			'Ep.adressemail',
			'Ep.regroupementep_id' => array( 'empty' => true ),
			'Ep.actif' => array( 'type' => 'checkbox' ),
			'Membreep.Membreep' => array( 'type' => 'hidden', 'value' => '' )
		);
	}
	else{
		$fields = array(
			'Ep.id',
			'Ep.name',
			'Ep.regroupementep_id' => array( 'empty' => true ),
			'Ep.actif' => array( 'type' => 'checkbox' ),
			'Membreep.Membreep' => array( 'type' => 'hidden', 'value' => '' )
		);
	}

	$fields['Zonegeographique.Zonegeographique'] = array(
		'fieldset' => true,
		'label' => 'Zones géographiques dont s\'occupe l\'EP',
		'multiple' => 'checkbox',
		'class' => 'col3'
	);

	echo $this->Default3->DefaultForm->create();

	echo $this->Default3->subform(
		$this->Translator->normalize( $fields ),
		array( 'options' => $options )
	);

	if ( isset( $this->validationErrors['Ep']['Membreep.Membreep'] ) && !empty( $this->validationErrors['Ep']['Membreep.Membreep'] ) ) {
		echo "<p class='error'>".$this->validationErrors['Ep']['Membreep.Membreep'][0]."</p>";
	}

	foreach( $fonctionsParticipants as $index => $fonctionParticipant ) {
		$fields = array(
			'Membreep.Membreep' => array(
				'label' => false,
				'fieldset' => false,
				'multiple' => 'checkbox',
				'class' => 'col3',
				'options' => Hash::combine( $fonctionParticipant['Membreep'], '{n}.id', '{n}.name' ),
				'hiddenField' => false
			)
		);

		echo $this->Html->tag(
			'fieldset',
				$this->Html->tag( 'legend', $fonctionParticipant['Fonctionmembreep']['name'] )
				.$this->Default3->subform( $this->Translator->normalize( $fields ) ),
			array(
				'id' => 'fieldsetMembreepMembreep'.( $index + 1 )
			)
		);
	}

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();

	echo $this->Observer->disableFormOnSubmit();
?>
<script type="text/javascript">
//<![CDATA[
	document.observe( 'dom:loaded', function() {
		insertButtonsCocherDecocher(
			$$( 'fieldset' )[0],
			"input[name=\"data[Zonegeographique][Zonegeographique][]\"]"
		);
		<?php foreach( array_keys( $fonctionsParticipants ) as $index ) :?>
		insertButtonsCocherDecocher(
			$$( 'fieldset' )[<?php echo ( $index + 1 );?>],
			"fieldset#fieldsetMembreepMembreep<?php echo ( $index + 1 );?> input[type=checkbox]"
		);
		<?php endforeach;?>
	} );
//]]>
</script>