<?php
	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	$foyer_id = $this->request->data['Recourgracieux']['foyer_id'];

	echo $this->Default3->DefaultForm->create( null, array( 'novalidate' => 'novalidate' ) );
	echo $this->Default3->index(
		array($creances),
		$this->Translator->normalize(
			array(
				'Creance.natcre',
				'Creance.rgcre',
				'Creance.dtimplcre',
				'Creance.mtinicre',
				'Creance.perioderegucre',
				'Titrecreancier.etat',
			)
		),
		array(
			'paginate' => false,
			'options' => $options,
			'empty_label' => __m('Recourgracieux::proposer::emptyCreances'),
		)
	);
	echo $this->Default3->subform(
		array(
			'Creancerecoursgracieux.natcre' => array('type' => 'hidden', 'value' => $creances['Creance']['natcre']),
			'Creancerecoursgracieux.rgcre' => array('type' => 'hidden', 'value' => $creances['Creance']['rgcre']),
			'Creancerecoursgracieux.mtinicre' => array('type' => 'hidden', 'value' => $creances['Creance']['mtinicre']),
			'Creancerecoursgracieux.dtimplcre' => array('type' => 'hidden', 'value' => $creances['Creance']['dtimplcre']),
			'Creancerecoursgracieux.ddregucre' => array('type' => 'hidden', 'value' => $creances['Creance']['ddregucre']),
			'Creancerecoursgracieux.dfregucre' => array('type' => 'hidden', 'value' => $creances['Creance']['dfregucre']),
			'Creancerecoursgracieux.etattitre' => array('type' => 'hidden', 'value' => $creances['Titrecreancier']['etat']),
		),
		array('options' => $options)
	);
	echo '<br>';
	echo $this->Default3->subform(
		array(
			//C’est un champ de saisie prérempli au montant de l’indu mais modifiable.
			'Creancerecoursgracieux.mntindus' => array( 'value' => $creances['Creance']['mtinicre']),
			//Le pourcentage de remise accordé par le CD
			'Creancerecoursgracieux.prcentremise',
			//C’est un champ calculé par rapport aux deux champs précédents qui ne sera donc pas éditable. Ce champ sera grisé.
			'Creancerecoursgracieux.mntremiseaffiche' => array ( 'disabled' => true  ),
			'Creancerecoursgracieux.motifproposrecoursgracieux_id' => array(
				'options' => $listMotifs
			),
			'Creancerecoursgracieux.mention' => array('type' => 'textarea')
		),
		array('options' => $options)
	);

	echo $this->Default3->subform(
		array(
			'Creancerecoursgracieux.creances_id' => array('type' => 'hidden', 'value' => $this->request->params['pass'][0]),
			'Creancerecoursgracieux.recours_id' => array('type' => 'hidden', 'value' => $this->request->params['pass'][1]),
			'Creancerecoursgracieux.types_id' => array('type' => 'hidden', 'value' => $this->request->params['pass'][2]),
			'Creancerecoursgracieux.mntremise'=> array('type' => 'hidden' ),
		),
		array('options' => $options)
	);

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();

	echo $this->Observer->disableFormOnSubmit( Inflector::camelize( "recourgracieux_{$this->request->params['action']}_form" ) );
?>

<script type="text/javascript">
	//<![CDATA[
	document.getElementById('CreancerecoursgracieuxPrcentremise').onchange = function () {setMontantRemis();}

	function setMontantRemis(){
		//Get de la vue
		var pourcentageRemise = $('CreancerecoursgracieuxPrcentremise').value;
		var montantIndus = $('CreancerecoursgracieuxMntindus').value;
		var valRemise = 0;

		//Calcul
		if ( pourcentageRemise != '0') {
			pourcentageRemise = parseInt(pourcentageRemise) ;
			valRemise = ( montantIndus * pourcentageRemise ) / 100 ;
			valRemise = Math.round(valRemise * Math.pow(10,2)) / Math.pow(10,2);
		}

		//Set à la vue
		$('CreancerecoursgracieuxMntremise').value = valRemise;
		$('CreancerecoursgracieuxMntremiseaffiche').value = valRemise;

	}
</script>
