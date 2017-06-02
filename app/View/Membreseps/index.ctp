<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	$searchFormId = 'MembreepIndexForm';

	echo $this->Default3->titleForLayout();

	echo $this->Default3->messages( $messages );

	echo $this->Default3->actions(
		array(
			'/Membreseps/add' => array(
				'disabled' => false !== array_search( 'error', $messages )
			),
			'/Membreseps/index/#toggleform' => array(
				'title' => 'Visibilité formulaire',
				'text' => 'Formulaire',
				'class' => 'search',
				'onclick' => "$( '{$searchFormId}' ).toggle(); return false;"
			)
		)
	);

	// Formulaire de recherche
	echo $this->Default3->form(
		$this->Translator->normalize(
			array(
				'Membreep.nom' => array( 'required' => false ),
				'Membreep.prenom' => array( 'required' => false ),
				'Membreep.ville' => array( 'required' => false ),
				'Membreep.organisme' => array( 'required' => false ),
				'Membreep.fonctionmembreep_id' => array( 'empty' => true, 'required' => false )
			)
		),
		array(
			'id' => $searchFormId,
			'options' => $options,
			'class' => isset( $results ) ? 'folded' : 'unfolded',
			'buttons' => array( 'Search', 'Reset' => array( 'type' => 'reset' ) )
		)
	);

	echo $this->Observer->disableFormOnSubmit( $searchFormId );

	// Résultats
	if( true === isset( $results ) ) {
		echo $this->Html->tag( 'h2', 'Résultats de la recherche' );

		echo $this->Default3->index(
			$results,
			$this->Translator->normalize(
				array(
					'Membreep.nomcomplet' => array( 'type' => 'text' ),
					'Fonctionmembreep.name',
					'Membreep.organisme',
					'Membreep.tel',
					'Membreep.adresse' => array( 'type' => 'text' ),
					'Membreep.mail',
					'/Membreseps/edit/#Membreep.id#' => array(
						'title' => true
					),
					'/Membreseps/delete/#Membreep.id#' => array(
						'title' => true,
						'confirm' => true,
						'disabled' => 'true == "#Membreep.has_linkedrecords#"'
					)
				)
			),
			array(
				'format' => $this->element( 'pagination_format', array( 'modelName' => 'Membreep' ) ),
				'options' => $options
			)
		);
	}
?>