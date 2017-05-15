<h1>
<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	if( $this->action == 'add' ) {
		echo $this->pageTitle = 'Ajout d\'une équipe pluridisciplinaire';
	}
	else {
		echo $this->pageTitle = 'Modification d\'une équipe pluridisciplinaire';
	}
?>
</h1>

<?php
	echo $this->Xform->create( null, array( 'id' => 'EpAddEditForm' ) );

	if (isset($this->request->data['Ep']['id']))
		echo $this->Form->input('Ep.id', array('type'=>'hidden'));


	if( Configure::read( 'Cg.departement' ) == 93 ){
		echo $this->Default->subform(
			array(
				'Ep.name' => array('required' => true),
				'Ep.adressemail',
				'Ep.regroupementep_id' => array('required' => true, 'type' => 'select'),
			),
			array(
				'options' => $options
			)
		);
	}
	else{
		echo $this->Default->subform(
			array(
				'Ep.name' => array('required' => true),
				'Ep.regroupementep_id' => array('required' => true, 'type' => 'select'),
			),
			array(
				'options' => $options
			)
		);
	}
	echo $this->Xhtml->tag(
		'div',
		$this->Default->subform(
			array(
				'Zonegeographique.Zonegeographique' => array( 'required' => true, 'multiple' => 'checkbox', 'empty' => false, 'domain' => 'ep', 'id' => 'listeZonesgeographiques' )
			),
			array(
				'options' => $options
			)
		),
		array(
			'id' => 'listeZonesgeographiques'
		)
	);

	echo $this->Form->button('Tout cocher', array( 'type' => 'button', 'onclick' => "GereChkbox('listeZonesgeographiques','cocher');return false;"));

	echo $this->Form->button('Tout décocher', array( 'type' => 'button', 'onclick' => "GereChkbox('listeZonesgeographiques','decocher');return false;"));

	$i = 0;
	if ( isset( $this->validationErrors['Ep']['Membreep.Membreep'] ) && !empty( $this->validationErrors['Ep']['Membreep.Membreep'] ) ) {
		echo "<p class='error'>".$this->validationErrors['Ep']['Membreep.Membreep'][0]."</p>";
	}
	foreach( $fonctionsParticipants as $fonction ) {
		$i++;
		$params = array(
			'required' => true,
			'fieldset' => false,
			'domain' => 'ep',
			'div' => false,
			'label' => false,
			'type' => 'select',
			'multiple' => 'checkbox',
			'empty' => false,
			'id' => 'listeParticipants',
			'options' => Set::combine( $fonction, 'Membreep.{n}.id', 'Membreep.{n}.name' )
		);

		if( $i != 1 ) {
			$params['hiddenField'] = false;
		}

		echo "<fieldset><legend>{$fonction['Fonctionmembreep']['name']}</legend>";
		echo $this->Xhtml->tag(
			'div',
			$this->Default->subform(
				array(
					'Membreep.Membreep' => $params
				),
				array(
					'options' => $options
				)
			),
			array(
				'id' => 'listeParticipants'
			)
		);

		echo '</fieldset>';
	}

	echo $this->Xform->end( __( 'Save' ) );

	echo $this->Default->button(
		'back',
		array(
			'controller' => 'eps',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);
?>

<script type="text/javascript">
	function GereChkbox(conteneur, a_faire) {
		$( conteneur ).getElementsBySelector( 'input[type="checkbox"]' ).each( function( input ) {
			if (a_faire=='cocher') blnEtat = true;
			else if (a_faire=='decocher') blnEtat = false;

			$(input).checked = blnEtat;
		} );
	}
</script>