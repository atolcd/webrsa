<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'valprogfichecandidature66', "Valsprogsfichescandidatures66::{$this->action}" )
	);

	// occurences renvoi false si il n'y a pas de modèle liés autrement que en belongsTo, on s'assure qu'un false/null soit à 0
	foreach ($requestgroups as $key => $value) {
		$requestgroups[$key]['Valprogfichecandidature66']['occurences'] = (int)Hash::get($value, 'Valprogfichecandidature66.occurences');
	}
	
	echo $this->Default2->index(
		$requestgroups,
		array(
			'Valprogfichecandidature66.progfichecandidature66_id',
			'Valprogfichecandidature66.name',
			'Valprogfichecandidature66.actif' => array( 'type' => 'boolean' )
		),
		array(
			'cohorte' => false,
			'actions' => array(
				'Valsprogsfichescandidatures66::edit',
				'Valsprogsfichescandidatures66::delete' => array( 'disabled' => '\'#Valprogfichecandidature66.occurences#\'!= "0"' )
			),
			'add' => 'Valsprogsfichescandidatures66::add',
			'options' => $options
		)
	);
?>