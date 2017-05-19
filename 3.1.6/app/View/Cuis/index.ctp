<?php
	echo $this->element('default_index');
	
	echo $this->Default3->index(
		$results,
		$this->Translator->normalize(
			array(
				'Cui.faitle',
				'Cui.secteurmarchand' => array('type' => 'select'),
				'Partenairecui.raisonsociale',
				'Cui.effetpriseencharge',
				'Cui.finpriseencharge',
			) + WebrsaAccess::links(
				array(
					'/Cuis/view/#Cui.id#',
					'/Cuis/edit/#Cui.id#',
					'/Cuis/delete/#Cui.id#' => array('confirm' => true),
					'/Cuis/filelink/#Cui.id#',
				)
			)
		),
		array(
			'options' => $options,
			'paginate' => false,
		)
	);
