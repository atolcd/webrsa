<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'actioncandidat_partenaire', "ActionscandidatsPartenaires::{$this->action}" )
	)
?>

<?php
	echo $this->Default->index(
		$actionscandidats_partenaires,
		array(
			'Actioncandidat.name',
			'Actioncandidat.codeaction' => array('type'=>'text'),
			'Partenaire.libstruc',
			'Partenaire.codepartenaire'
		),
		array(
			'cohorte' => false,
			'actions' => array(
				'ActioncandidatPartenaire.edit',
				'ActioncandidatPartenaire.delete',
			),
			'add' => 'ActioncandidatPartenaire.add',
			'options' => $options
		)
	);

	echo $this->Default->button(
		'back',
		array(
			'controller' => 'actionscandidats_personnes',
			'action'     => 'indexparams'
		),
		array(
			'id' => 'Back'
		)
	);
?>
