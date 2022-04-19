<?php
	echo '<h1>'.sprintf(__d('structuresreferentes_typesorients_zonesgeographiques', 'edit.titre'), $nomVille).'</h1>';
	echo '<br><br>';
	echo $this->Default3->DefaultForm->create();
		foreach($typesorients as $keytypeorient => $typeorient){
			if($keytypeorient == Configure::read('Typeorient.emploi_id') || $keytypeorient == Configure::read('Typeorient.service_social_id')){
				echo $this->Default3->DefaultForm->input( 'StructurereferenteTypeorientZonegeographique.'.$keytypeorient, ['label' => $typeorient, 'type' => 'select' , 'options' => $listeStructuresreferentes, 'value'=> $structuresreferentes[$keytypeorient], 'required' => true] );
			} else {
				echo $this->Default3->DefaultForm->input( 'StructurereferenteTypeorientZonegeographique.'.$keytypeorient, ['label' => $typeorient, 'type' => 'select' , 'options' => $listeStructuresreferentes, 'empty' => true, 'value'=> $structuresreferentes[$keytypeorient] ] );
			}
		}

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();