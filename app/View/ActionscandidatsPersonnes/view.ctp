<?php
    $domain = "actioncandidat_personne_".Configure::read( 'ActioncandidatPersonne.suffixe' );

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( $domain, "ActionscandidatsPersonnes::{$this->action}" )
	);
?>
<?php
	echo $this->Xform->create( 'ActioncandidatPersonne', array( 'id' => 'viewForm' ) );


		if( ( $actionscandidatspersonne['ActioncandidatPersonne']['positionfiche'] == 'annule' ) ){

			echo $this->Xhtml->tag('div', $this->Xhtml->tag('strong', 'Raison de l\'annulation'));
			echo $this->Default->view(
				$actionscandidatspersonne,
				array(
					'ActioncandidatPersonne.motifannulation' => array( 'type' => 'text' )
				),
				array(
					'widget' => 'table',
					'class' => 'aere'
				)
			);

		}
		echo $this->Xhtml->tag('div', $this->Xhtml->tag('strong', 'Action engagée'));
        echo $this->Default->view(
        	$actionscandidatspersonne,
            array(
                'Actioncandidat.name',
				'Actioncandidat.contractualisation',
				'Actioncandidat.lieuaction',
				'Actioncandidat.cantonaction',
				'Actioncandidat.ddaction',
				'Actioncandidat.dfaction',
				'Actioncandidat.nbpostedispo'=> array( 'type'=>'text' ),
				'Actioncandidat.referent_id' => array('type'=>'text' ),
				'Actioncandidat.nbposterestant'=> array( 'type'=>'text' ),
				'Actioncandidat.codeaction' => array('type'=>'text')
			),
            array(
                'widget' => 'table',
                'class' => 'aere'
            )
		);

        
        if( !empty( $actionscandidatspersonne['ActioncandidatPersonne']['progfichecandidature66_id'] ) ) {
            echo $this->Xhtml->tag('div', $this->Xhtml->tag('strong', 'Informations pour les actions Région'));

            echo $this->Default2->view(
                $actionscandidatspersonne,
                array(
                    'Progfichecandidature66.name' => array( 'label' => 'Nom du (des) programme(s)'),
                    'Valprogfichecandidature66.name' => array( 'label' => 'Valeur du programme'),
                    'ActioncandidatPersonne.formationregion',
                    'ActioncandidatPersonne.nomprestataire' => array( 'label' => 'Nom du prestataire')
                ),
                array(
                    'widget' => 'table',
                    'class' => 'aere'
                )
            );
        }
        
		echo $this->Xhtml->tag('div', $this->Xhtml->tag('strong', 'Nom du prescripteur de la fiche' ) );
        echo $this->Default->view(
        	$actionscandidatspersonne,
            array(
                'Referent.qual',
                'Referent.nom',
                'Referent.prenom',
                'Referent.numero_poste',
                'Referent.email',
                'Referent.fonction',
			),
			array(
                'widget' => 'table',
                'class' => 'aere'
			)
		);


		$naturemobile = Set::enum( $actionscandidatspersonne['ActioncandidatPersonne']['naturemobile'], $options['ActioncandidatPersonne']['naturemobile'] );

		echo $this->Xhtml->tag('div', $this->Xhtml->tag('strong', 'Fiche descriptive de la demande'));
        echo $this->Default2->view(
        	$actionscandidatspersonne,
            array(
				'ActioncandidatPersonne.motifdemande',
				'ActioncandidatPersonne.mobile' => array( 'type' => 'boolean' ),
				'ActioncandidatPersonne.naturemobile' => array( 'type'=>'text', 'value' => $naturemobile ),
				'ActioncandidatPersonne.typemobile',
				'ActioncandidatPersonne.rendezvouspartenaire' => array( 'type' => 'boolean' ),
				'ActioncandidatPersonne.horairerdvpartenaire'
			),
            array(
                'widget' => 'table',
                'domain' => $domain,
                'class' => 'aere'
            )
		);

		$venu = Set::enum( $actionscandidatspersonne['ActioncandidatPersonne']['bilanvenu'], $options['ActioncandidatPersonne']['bilanvenu'] );
		$retenu = Set::enum( $actionscandidatspersonne['ActioncandidatPersonne']['bilanretenu'], $options['ActioncandidatPersonne']['bilanretenu'] );

		echo $this->Xhtml->tag('div', $this->Xhtml->tag('strong', 'Bilan du rendez-vous'));
        echo $this->Default2->view(
        	$actionscandidatspersonne,
            array(
                'ActioncandidatPersonne.bilanvenu' => array( 'type'=>'text', 'value' => $venu ),
                'ActioncandidatPersonne.bilanretenu' => array( 'type'=>'text', 'value' => $retenu ),
                'ActioncandidatPersonne.infocomplementaire',
				'ActioncandidatPersonne.datebilan'
			),
            array(
                'widget' => 'table',
                'class' => 'aere'
            )
		);

		echo $this->Xhtml->tag('div', $this->Xhtml->tag('strong', 'Sortie'));
        echo $this->Default->view(
        	$actionscandidatspersonne,
            array(
                'ActioncandidatPersonne.sortiele',
				'Motifsortie.name',
			),
            array(
                'widget' => 'table',
                'class' => 'aere'
            )
		);

?>
<?php
		echo "<h2>Pièces déjà présentes</h2>";
		echo $this->Fileuploader->results( Set::classicExtract( $actionscandidatspersonne, 'Fichiermodule' ) );
	?>
</div>
<div class="submit">
	<?php

		echo $this->Form->submit( 'Retour', array( 'name' => 'Cancel', 'div' => false ) );
	?>
</div>
<?php echo $this->Xform->end();?>