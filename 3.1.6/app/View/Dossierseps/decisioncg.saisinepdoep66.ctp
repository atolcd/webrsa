<?php
	$domain = 'pdo';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<fieldset id="Decision" class="invisible">
	<?php
		echo $this->Form->create('Dossierep', array('url'=>'/dossierseps/decisioncg/'.$dossierep_id, 'id'=>'DossierepDecisioncg'));

		if (isset($this->request->data['Decisionsaisinepdoep66']['id']))
			echo $this->Form->input('Decisionsaisinepdoep66.id', array('type'=>'hidden'));

		echo $this->Form->input('Decisionsaisinepdoep66.passagecommissionep_id', array('type'=>'hidden'));
		echo $this->Form->input('Decisionsaisinepdoep66.etape', array('type'=>'hidden', 'value'=>'cg'));
		echo $this->Form->input('Saisinepdoep66.dossierep_id', array('type'=>'hidden', 'value' => $dossierep_id ));

		echo $this->Default->subform(
			array(
				'Decisionsaisinepdoep66.decision' => array( 'label' =>  ( __( 'État du dossier' ) ), 'type' => 'select', 'empty' => true ),
				'Decisionsaisinepdoep66.datedecisionpdo' => array( 'label' =>  ( __( 'Date de décision de la PDO' ) ), 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear'=>date('Y')+5, 'minYear'=>date('Y')-1, 'empty' => false ),
				'Decisionsaisinepdoep66.decisionpdo_id' => array( 'label' =>  ( __( 'Décision du Conseil Général' ) ), 'type' => 'select', 'options' => $decisionpdo, 'empty' => true )
			),
			array(
				'domain' => $domain,
				'options' => $options
			)
		);

		echo $this->Default->subform(
			array(
				'Decisionsaisinepdoep66.commentaire' => array( 'label' =>  'Observation : ', 'type' => 'textarea' ),
			),
			array(
				'domain' => $domain,
				'options' => $options
			)
		);

		echo $this->Form->end('Enregistrer');
	?>
</fieldset>

<script type="text/javascript">
	document.observe("dom:loaded", function() {
		$( 'Decisionsaisinepdoep66Decision' ).observe( 'change', function() {
			afficheRaisonpassage();
		} );
		afficheRaisonpassage();
	});

	function afficheRaisonpassage() {
		if ( $F( 'Decisionsaisinepdoep66Decision' ) == 'annule' || $F( 'Decisionsaisinepdoep66Decision' ) == 'reporte' ) {
			$( 'Decisionsaisinepdoep66DatedecisionpdoDay' ).disable();
			$( 'Decisionsaisinepdoep66DatedecisionpdoMonth' ).disable();
			$( 'Decisionsaisinepdoep66DatedecisionpdoYear' ).disable();
			$( 'Decisionsaisinepdoep66DecisionpdoId' ).disable();
		}
		else if ( $F( 'Decisionsaisinepdoep66Decision' ) == '' ) {
			$( 'Decisionsaisinepdoep66DatedecisionpdoDay' ).disable();
			$( 'Decisionsaisinepdoep66DatedecisionpdoMonth' ).disable();
			$( 'Decisionsaisinepdoep66DatedecisionpdoYear' ).disable();
			$( 'Decisionsaisinepdoep66DecisionpdoId' ).disable();
		}
		else {
			$( 'Decisionsaisinepdoep66DatedecisionpdoDay' ).enable();
			$( 'Decisionsaisinepdoep66DatedecisionpdoMonth' ).enable();
			$( 'Decisionsaisinepdoep66DatedecisionpdoYear' ).enable();
			$( 'Decisionsaisinepdoep66DecisionpdoId' ).enable();
		}
	}
</script>