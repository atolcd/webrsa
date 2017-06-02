<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'partenaire', "Partenaires::{$this->action}" )
	)
?>

<?php
	echo $this->Xform->create( array( 'id' => 'partenaireform' ) );

	echo $this->Default2->subform(
		array(
			'Partenaire.id' => array( 'type' => 'hidden'),
			'Partenaire.libstruc' => array( 'required' => true ),
			'Partenaire.codepartenaire',
			'Partenaire.numvoie' => array( 'required' => true ),
			'Partenaire.typevoie' => array( 'options' => $options['Partenaire']['typevoie'], 'required' => true ),
			'Partenaire.nomvoie' => array( 'required' => true ),
			'Partenaire.compladr',
			'Partenaire.numtel',
			'Partenaire.numfax',
			'Partenaire.email',
			'Partenaire.codepostal' => array( 'required' => true ),
			'Partenaire.nomresponsable'
		)
	);
	
	if( Configure::read( 'Cg.departement') == 66 ) {
		echo $this->Default2->subform(
			array(
				'Partenaire.canton' => array( 'options' => $cantons, 'empty' => true )
			)
		);
	}
	
	echo $this->Default2->subform(
		array(
			'Partenaire.ville' => array( 'required' => true ),
			'Partenaire.president',
			'Partenaire.adressepresident' => array( 'type' => 'text' ),
			'Partenaire.directeur',
			'Partenaire.adressedirecteur' => array( 'type' => 'text' )
		)
	);
	
	echo '<br />';
		
	echo $this->Default2->subform(
		array(
			'Partenaire.iscui' => array( 'empty' => false, 'type' => 'radio', 'options' => $options['Partenaire']['iscui'], 'required' => true )
		)
	);
	echo '<fieldset id="bloccui" class="noborder">';
		echo $this->Default2->subform(
			array(
				'Partenaire.secteuractivitepartenaire_id' => array( 'empty' => true, 'options' => $secteursactivites ),
				'Partenaire.objet',
				'Partenaire.statut' => array( 'empty' => true, 'options' => $options['Partenaire']['statut'] ),
			)
		);
		
		if( Configure::read( 'Cg.departement') == 66 ) {
			echo $this->Default2->subform(
				array(
					'Partenaire.raisonsocialepartenairecui66_id' => array( 'empty' => true, 'options' => $options['Partenaire']['raisonsocialepartenairecui66_id'] )
				)
			);
		}
		echo $this->Default2->subform(
			array(
				'Partenaire.siret',
				'Partenaire.nomtiturib',
				'Partenaire.codeban',
				'Partenaire.guiban',
				'Partenaire.numcompt',
				'Partenaire.nometaban',
				'Partenaire.clerib',
				'Partenaire.orgrecouvcotis' => array( 'type' => 'radio', 'empty' => false, 'options' => $options['Partenaire']['orgrecouvcotis'] )
			)
		);
	
	echo "</fieldset>";


	
	
	echo $this->Html->tag(
		'div',
		 $this->Xform->button( 'Enregistrer', array( 'type' => 'submit' ) )
		.$this->Xform->button( 'Annuler', array( 'type' => 'submit', 'name' => 'Cancel' ) ),
		array( 'class' => 'submit noprint' )
	);

	echo $this->Xform->end();
?>
<script type="text/javascript">
	document.observe("dom:loaded", function() {
		//Utilisé en cas d'adresse de l'employeur différente pour les doc administratifs
		observeDisableFieldsetOnRadioValue(
			'partenaireform',
			'data[Partenaire][iscui]',
			$( 'bloccui' ),
			'1',
			false,
			true
		);
	});
</script>