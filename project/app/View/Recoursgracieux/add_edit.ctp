<?php

	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	if( $this->action == 'edit' ) {
		$foyer_id = $this->request->data['Recourgracieux']['foyer_id'];
	}

	//echo $this->Default3->DefaultForm->create( null, array(  ));
	echo $this->Default3->DefaultForm->create( null, array( 'novalidate' => 'novalidate' ) );

	if( $this->action == 'add' ) {
		echo $this->Default->subform(
			array(
				'Recourgracieux.etat' => array('type' => 'hidden', 'value' => 'ATTAFECT' )
			)
		);
	}elseif($this->action == 'edit' ){
		echo $this->Default3->subform(
			array(
				'Recourgracieux.etat' => array('type' => 'hidden' ),
				'Recourgracieux.id' => array('type' => 'hidden')
				),
			array('options' => $options)
		);
	}
	echo $this->Default3->subform(
		array(
			'Recourgracieux.foyer_id' => array( 'type' => 'hidden', 'value' => $foyer_id),
			'Recourgracieux.dtarrivee' => array('type' => 'date', 'dateFormat' => 'DMY'),
			'Recourgracieux.dtbutoire' => array('type' => 'date', 'dateFormat' => 'DMY', 'disabled' => true ),
			'Recourgracieux.dtreception' => array('type' => 'date', 'dateFormat' => 'DMY'),
			'Recourgracieux.originerecoursgracieux_id' => array( 'type' => 'select', 'options' => $options['Originerecoursgracieux']['origine_actif'] ),			
		),
		array(
			'options' => $options
		)
	);
	echo $this->Default3->subform(
		array(
			'Recourgracieux.dtbutoire' => array('type' => 'hidden', 'dateFormat' => 'DMY')
			),
		array('options' => $options)
	);

	echo "
	<fieldset>
	<legend>".$this->Default2->label( 'Recourgracieux.haspiecejointe' )."</legend>
	<div style='display: none;'>";
	echo $this->Form->input( 'Recourgracieux.haspiecejointe', array( 'type' => 'radio', 'options' => $options['Recourgracieux']['haspiecejointe'], 'legend' => false, 'fieldset' => false, 'value' => 1 ) );
	echo '</div>
		<fieldset id="filecontainer-piece" class="noborder invisible">';
			echo $this->Fileuploader->create(
				isset($fichiers) ? $fichiers : array(),
				array( 'action' => 'ajaxfileupload' )
			);
			if (!isset ($fichiersEnBase)) {
				$fichiersEnBase = array ();
			}
			echo $this->Fileuploader->results(
				$fichiersEnBase
			);
	echo "</fieldset>".
	$this->Fileuploader->validation( Inflector::camelize( "recourgracieux_{$this->request->params['action']}_form" ), 'Recourgracieux', 'Pièce jointe' ).
	"</fieldset>";

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();

	echo $this->Observer->disableFormOnSubmit( Inflector::camelize( "recourgracieux_{$this->request->params['action']}_form" ) );

	?>

<script type="text/javascript">
	document.getElementById('RecourgracieuxDtarriveeDay').onchange = function () {setDatebutoire();}
	document.getElementById('RecourgracieuxDtarriveeMonth').onchange = function () {setDatebutoire();}
	document.getElementById('RecourgracieuxDtarriveeYear').onchange = function () {	setDatebutoire();}

	function setDatebutoire(){
		var DtDay = $('RecourgracieuxDtarriveeDay').value;
		var DtMonth =$('RecourgracieuxDtarriveeMonth').value;
		var DtYear =$('RecourgracieuxDtarriveeYear').value;

		//Transformation du jours et moi en nombres et ajout d'un mois
		DtDay = parseInt(DtDay);
		DtMonth = parseInt(DtMonth) + 1;

		// Detection des dépassements de jours
		if ( DtMonth == 2 && DtDay > 28 ){
			DtMonth = DtMonth +1;
			DtDay = DtDay-28;
		}
		if (
			 ( DtMonth == 4 || DtMonth == 6 || DtMonth == 9 || DtMonth == 11 )
			 && DtDay > 30
		){
			DtMonth = DtMonth +1;
			DtDay = DtDay - 30;
		}

		//Detection des dépassements d'années
		if ( DtMonth == 13 ) {
			DtYear = parseInt(DtYear) +1;
			DtMonth = DtMonth - 12 ;
		}

		//Remise en version ID
		if ( DtDay < 10 ){
			DtDay = "0" + DtDay;
		}
		if ( DtMonth < 10 ){
			DtMonth = "0" + DtMonth;
		}

		//Set à la vue
		$('RecourgracieuxDtbutoireDay').value = DtDay;
		$('RecourgracieuxDtbutoireMonth').value = DtMonth;
		$('RecourgracieuxDtbutoireYear').value = DtYear;
		$('RecourgracieuxDtbutoire').value = DtYear+"-"+DtMonth+"-"+DtDay;
	}
</script>