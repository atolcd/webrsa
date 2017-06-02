<?php
	echo $this->Default3->titleForLayout( $memo );

	// Formatage du texte du mémo
	$memo['Memo']['name'] = nl2br( Hash::get( $memo, 'Memo.name' ) );

	echo $this->Default3->view(
		$memo,
		array(
			'Personne.nom_complet',
			'Dossier.matricule',
			'Memo.name',
			'Memo.created',
			'Memo.modified',
		)
	);

	echo $this->DefaultDefault->actions(
		$this->Default3->DefaultAction->back()
	);
?>