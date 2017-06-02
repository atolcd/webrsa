<?php
	App::uses('WebrsaAccess', 'Utility');
	WebrsaAccess::init($dossierMenu);
	
	echo $this->Default3->titleForLayout($this->request->data);
		
	echo $this->Default3->actions(
		WebrsaAccess::actionAdd('/modescontact/add/'.$foyer_id, $ajoutPossible)
	);
	
	echo $this->Default3->index(
		$modescontact,
		array(
			'Modecontact.numtel',
			'Modecontact.numposte',
			'Modecontact.nattel',
			'Modecontact.matetel',
			'Modecontact.autorutitel',
			'Modecontact.adrelec',
			'Modecontact.autorutiadrelec'
		) + WebrsaAccess::links(
			array(
				'/Modescontact/view/#Modecontact.id#',
				'/Modescontact/edit/#Modecontact.id#',
			)
		),
		array(
			'options' => $options,
			'paginate' => false,
		)
	);
?>