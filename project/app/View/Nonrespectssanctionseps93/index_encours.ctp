<h1> <?php echo $this->pageTitle = __d( 'nonrespectsanctionep93', 'Nonrespectssanctionseps93::index_encours' );?> </h1>
<?php
	require_once  'index.ctp' ;

	echo $this->Default2->index(
		$nonrespectssanctionseps93,
		array(
			'Dossier.matricule',
			'Personne.nom',
			'Personne.prenom',
			'Personne.nir',
			'Nonrespectsanctionep93.origine',
			'Nonrespectsanctionep93.contratinsertion_id' => array( 'type' => 'boolean' ),
			'Contratinsertion.df_ci',
			'Orientstruct.date_valid',
			'Commissionep.dateseance' => array( 'type' => 'date' ),
			'Nonrespectsanctionep93.rgpassage',
		),
		array(
			'paginate' => 'Nonrespectsanctionep93',
			'options' => $options
		)
	);
?>
<ul class="actionMenu">
	<li><?php
		echo $this->Xhtml->exportLink(
			'Télécharger le tableau',
			array( 'action' => 'exportcsv' ) + Hash::flatten( $this->request->data, '__' )
		);
	?></li>
</ul>