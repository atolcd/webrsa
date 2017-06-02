<?php
	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ), array( 'inline' => false ) );
	}

	echo $this->Default3->actions(
		array(
			'/Nonorientationsproscovs58/cohorte/#toggleform' => array(
				'onclick' => '$(\'Nonorientationsproscovs58CohorteSearchForm\').toggle(); return false;'
			),
		)
	);

	// 1. Moteur de recherche
	echo $this->Xform->create( null, array( 'id' => 'Nonorientationsproscovs58CohorteSearchForm', 'class' => ( ( isset( $results ) ) ? 'folded' : 'unfolded' ) ) );

	echo $this->Html->tag(
		'fieldset',
		$this->Html->tag( 'legend', __d( $this->request->params['controller'], 'Search.Contratinsertion.df_ci' ) )
		.$this->Xform->input( 'Search.Contratinsertion.df_ci', array( 'type' => 'hidden', 'value' => true ) )
		.$this->Xform->input( 'Search.Contratinsertion.df_ci_from', array( 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 3, 'minYear' => 2009, 'domain' => $this->request->params['controller'] ) )
		.$this->Xform->input( 'Search.Contratinsertion.df_ci_to', array( 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 3, 'minYear' => 2009, 'domain' => $this->request->params['controller'] ) )
	);

	echo $this->Allocataires->blocReferentparcours(
		array(
			'options' => $options,
			'prefix' => 'Search'
		)
	);

	echo $this->Allocataires->blocDossier(
		array(
			'options' => $options,
			'prefix' => 'Search',
			'skip' => array(
				'Search.Dossier.dernier',
				'Search.Situationdossierrsa.etatdosrsa'
			)
		)
	);

	echo $this->Allocataires->blocAdresse(
		array(
			'options' => $options,
			'prefix' => 'Search'
		)
	);

	echo $this->Allocataires->blocAllocataire(
		array(
			'options' => $options,
			'prefix' => 'Search',
			'skip' => array(
				'Search.Calculdroitrsa.toppersdrodevorsa',
			)
		)
	);

	echo $this->Allocataires->blocPagination(
		array(
			'options' => $options,
			'prefix' => 'Search'
		)
	);

	echo $this->Xform->end( 'Search' );

	echo $this->Observer->disableFormOnSubmit( 'Nonorientationsproscovs58CohorteSearchForm' );

	// 2. Formulaire de traitement des résultats de la recherche
	if( isset( $results ) ) {
		echo $this->Html->tag( 'h2', 'Résultats de la recherche' );

		if( !empty( $results ) ) {
			echo $this->Default3->DefaultForm->create( null, array( 'id' => 'Nonorientationsproscovs58CohorteForm' ) );
		}

		$this->Default3->DefaultPaginator->options(
			array( 'url' => Hash::flatten( (array)$this->request->data, '__' ) )
		);

		echo $this->Default3->index(
			$results,
			array(
				'Dossier.numdemrsa',
				'Personne.nom_complet',
				'Personne.dtnai',
				'Adresse.codepos',
				'Foyer.enerreur' => array( 'sort' => false ),
				'Orientstruct.date_valid',
				'Contratinsertion.nbjours',
				'Typeorient.lib_type_orient',
				'Structurereferente.lib_struc',
				'Referent.nom_complet',
				'data[Cohorte][Orientstruct][][id]' => array(
					'type' => 'checkbox',
					'label' => false,
					'value' => '#Orientstruct.id#'
				),
				// INFO: début champs cachés (en css)!
				'data[Cohorte][Orientstruct][][personne_id]' => array(
					'type' => 'hidden',
					'label' => false,
					'value' => '#Personne.id#'
				),
				'data[Cohorte][Dossier][][id]' => array(
					'type' => 'hidden',
					'label' => false,
					'value' => '#Dossier.id#'
				),
				// INFO: fin champs cachés (en css)!
				'/Orientsstructs/index/#Personne.id#' => array(
					'class' => 'view'
				)
			),
			array(
				'format' => SearchProgressivePagination::format( !Hash::get( $this->request->data, 'Search.Pagination.nombre_total' ) )
			)
		);
		// TODO: infobulle

		if( !empty( $results ) ) {
			echo $this->Default3->DefaultForm->buttons( array( 'Save' ) );
			echo $this->Default3->DefaultForm->end();

			echo $this->Observer->disableFormOnSubmit( 'Nonorientationsproscovs58CohorteForm' );

			echo $this->Form->button( 'Tout cocher', array( 'type' => 'button', 'onclick' => "toutCocher( '#Nonorientationsproscovs58CohorteForm input[type=checkbox]' ); return false;" ) );
			echo $this->Form->button( 'Tout décocher', array( 'type' => 'button', 'onclick' => "toutDecocher( '#Nonorientationsproscovs58CohorteForm input[type=checkbox]' ); return false;" ) );
		}
	}
?>
<?php if( isset( $results ) ):?>
<ul class="actionMenu">
	<li><?php
		echo $this->Xhtml->exportLink(
			'Télécharger le tableau',
			array( 'action' => 'exportcsv1' ) + Hash::flatten( $this->request->data, '__' ),
			( $this->Permissions->check( $this->request->params['controller'], 'exportcsv1' ) && count( $results ) > 0 )
		);
	?></li>
</ul>
<?php endif;?>