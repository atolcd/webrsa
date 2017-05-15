<h1><?php echo $this->pageTitle = __d( 'contratcomplexeep93', "{$this->name}::{$this->action}" );?></h1>

<?php
	echo $this->Default2->index(
		$contratsinsertion,
		array(
			'Personne.nom',
			'Personne.prenom',
			'Personne.dtnai',
			'Personne.Foyer.Adressefoyer.0.Adresse.nomcom',
			'Structurereferente.lib_struc',
			'Contratinsertion.dd_ci',
			'Contratinsertion.df_ci',
			'Personne.Foyer.enerreur' => array( 'type' => 'string', 'class' => 'foyer_enerreur' ),
			'Contratinsertion.chosen' => array( 'input' => 'checkbox', 'type' => 'boolean', 'domain' => 'contratcomplexeep93', 'sort' => false ),
		),
		array(
			'cohorte' => true,
			'hidden' => array(
				'Personne.id',
				'Contratinsertion.id'
			),
			'paginate' => 'Contratinsertion',
			'domain' => 'contratcomplexeep93',
			'labelcohorte' => 'Enregistrer'
		)
	);
?>
<?php if( !empty( $contratsinsertion) ):?>
    <?php echo $this->Form->button( 'Tout cocher', array( 'type' => 'button', 'onclick' => 'return toutCocher();' ) );?>
    <?php echo $this->Form->button( 'Tout décocher', array( 'type' => 'button', 'onclick' => 'return toutDecocher();' ) );?>
<?php endif;?>