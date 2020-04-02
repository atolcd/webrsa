<?php
	$defaultParams = array('paginate' => false, 'options' => $options);
	$noData = $this->Xhtml->tag('p', 'Pas de données.', array('class' => 'notice'));

	echo $this->Default3->titleForLayout($this->request->data, compact('domain'));

	echo $this->element('ancien_dossier');

		echo $this->Default3->index(
			$historiques,
			array(
				'Historiquedroit.toppersdrodevorsa',
				'Historiquedroit.etatdosrsa',
				'Historiquedroit.moticlosrsa',
				'Historiquedroit.created' => array ( 'type'=>'date' ),
				'Historiquedroit.modified' => array ( 'type'=>'date' ),
			),
			array('domain' => 'historiquesdroits') + $defaultParams
		);