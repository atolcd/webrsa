<?php
	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	$foyer_id = $this->request->data['Recourgracieux']['foyer_id'];

	echo $this->Default3->DefaultForm->create( null, array( 'novalidate' => 'novalidate' ) );
	echo $this->Default3->index(
		array($indus),
		$this->Translator->normalize(
			array(
				'Infofinanciere.natpfcre',
				'Infofinanciere.rgcre',
				'Infofinanciere.dttraimoucompta',
				'Infofinanciere.mtmoucompta',
			)
		),
		array(
			'paginate' => false,
			'options' => $options,
			'empty_label' => __m('Recourgracieux::proposer::emptyIndus'),
		)
	);
	echo $this->Default3->subform(
		array(
			'Indurecoursgracieux.natpfcre' => array('type' => 'hidden', 'value' => $indus['Infofinanciere']['natpfcre']),
			'Indurecoursgracieux.rgcre' => array('type' => 'hidden', 'value' => $indus['Infofinanciere']['rgcre']),
			'Indurecoursgracieux.mtmoucompta' => array('type' => 'hidden', 'value' => $indus['Infofinanciere']['mtmoucompta']),
			'Indurecoursgracieux.dttraimoucompta' => array('type' => 'hidden', 'value' => $indus['Infofinanciere']['dttraimoucompta']),
		),
		array('options' => $options)
	);
	echo '<br>';
	echo $this->Default3->subform(
		array(
			//C’est un champ de saisie prérempli au montant de l’indu mais modifiable.
			'Indurecoursgracieux.mntindus' => array( 'value' => $indus['Infofinanciere']['mtmoucompta']),
			//Le pourcentage de remise accordé par le CD
			'Indurecoursgracieux.prcentremise',
			//C’est un champ calculé par rapport aux deux champs précédents qui ne sera donc pas éditable. Ce champ sera grisé.
			'Indurecoursgracieux.mntremiseaffiche' => array ( 'disabled' => true  ),
			'Indurecoursgracieux.motifproposrecoursgracieux_id' => array(
				'options' => $listMotifs
			),
			'Indurecoursgracieux.mention' => array('type' => 'textarea')
		),
		array('options' => $options)
	);

	echo $this->Default3->subform(
		array(
			'Indurecoursgracieux.indus_id' => array('type' => 'hidden', 'value' => $this->request->params['pass'][0]),
			'Indurecoursgracieux.recours_id' => array('type' => 'hidden', 'value' => $this->request->params['pass'][1]),
			'Indurecoursgracieux.types_id' => array('type' => 'hidden', 'value' => $this->request->params['pass'][2]),
			'Indurecoursgracieux.mntremise'=> array('type' => 'hidden' ),
		),
		array('options' => $options)
	);

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();

	echo $this->Observer->disableFormOnSubmit( Inflector::camelize( "recourgracieux_{$this->request->params['action']}_form" ) );

?>

<script type="text/javascript">
	//<![CDATA[
	document.getElementById('IndurecoursgracieuxPrcentremise').onchange = function () {setMontantRemis();}

	function setMontantRemis(){
		//Get de la vue
		var pourcentageRemise = $('IndurecoursgracieuxPrcentremise').value;
		var montantIndus = $('IndurecoursgracieuxMntindus').value;
		var valRemise = 0;

		//Calcul
		if ( pourcentageRemise != '0') {
			pourcentageRemise = parseInt(pourcentageRemise) ;
			valRemise = ( montantIndus * pourcentageRemise ) / 100 ;
			valRemise = Math.round(valRemise * Math.pow(10,2)) / Math.pow(10,2);
		}

		//Set à la vue
		$('IndurecoursgracieuxMntremise').value = valRemise;
		$('IndurecoursgracieuxMntremiseaffiche').value = valRemise;

	}
</script>
