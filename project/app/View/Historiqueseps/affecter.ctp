<?php

	echo $this->Default3->titleForLayout($personne);

    echo "<br/><br/>";

    echo $this->Xform->create( 'Historiqueep', array( 'type' => 'post', 'url' => array( 'action' => $this->action ), 'id' => 'Search', 'novalidate' => true ) );

    echo $this->Default2->subform(
        array(
            'Ep.regroupementep_id' => array('type'=>'select'),
            'Ep.id' => array('type'=>'select', 'label' => __m("Commissionep.intitule_ep")),
            'Commissionep.dateseance' => array('type'=>'select'),
            'Dossierep.themeep' => array('type'=>'select', 'label' => __m("thematique")),
            'Personne.id' => array('type'=>'hidden', 'value' => $personne['Personne']['id']),
            'Dossierep.id' => array('type'=>'hidden', 'value' => $dossier_ep['Dossierep']['id']),
        ),
        array(
            'options' => $options
        )
    );

    ?>

	<div class="submit noprint">
		<?php echo $this->Xform->button( 'Affecter à cette EP', array( 'type' => 'submit', 'id' => 'submit_button' ) );?>
		<?php echo $this->Xform->button( 'Annuler', array('name' => 'Cancel', 'value' => $personne['Personne']['id'] ) );?>
	</div>

<?php echo $this->Xform->end();?>

<script type="text/javascript">
	document.observe("dom:loaded", function() {

		dependantSelect( 'EpId', 'EpRegroupementepId' );
		dependantSelect( 'CommissionepDateseance', 'EpId' );

        //Le bouton enregistrer ne s'active que si tous les champs sont remplis
        $('submit_button').disable();

        //On ne peut sélectionner dans la liste suivante que si la précédente est remplie
        dependances = [
            ['EpRegroupementepId', 'EpId'],
            ['EpId', 'CommissionepDateseance'],
            ['CommissionepDateseance', 'DossierepThemeep'],
            ['DossierepThemeep', 'submit_button'],
        ];

        dependances.forEach(dependance => {
            $(dependance[1]).disable();
            $(dependance[0]).observe( 'change', function( event ) {
				disableFieldsOnValue(
					dependance[0],
					[
						dependance[1],
					],
					'',
					true
				);

                if($(dependance[1]) == DossierepThemeep && $(dependance[1]).disabled){
                    $(dependance[1]).value = '';
                    $('submit_button').disable();

                }

		    });
        });

	});


</script>
