<?php
	// Donne le domain du plus haut niveau de précision (prefix, action puis controller)
	$domain = current(WebrsaTranslator::domains());
	$defaultParams = compact('options', 'domain');

	echo $this->Default3->titleForLayout($this->request->data, compact('domain'));

	echo $this->Fileuploader->element( 'Fichedeliaison', $fichiers, $record, array(0 => 'Non', 1 => 'Oui') );

	echo $this->Observer->disableFormOnSubmit( 'fichedeliaisonform' );