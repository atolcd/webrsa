<?php
	echo $this->Default->index(
		$possibles,
		array(
			'Personne.qual',
			'Personne.nom',
			'Personne.prenom',
			'Contratinsertion.dd_ci',
			'Contratinsertion.df_ci'
		),
		array(
			'paginate' => 'Personne',
			'actions' => array(
				'Personne.view'
			)
		)
	);
?>
