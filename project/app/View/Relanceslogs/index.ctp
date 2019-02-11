<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Default3->titleForLayout();

	$searchFormId = 'RelanceslogsIndexForm';
	$actions =  array(
		'/Parametrages/index' => array( 'class' => 'back' ),
		'/Relanceslogs/add' => array(),
		'/Relanceslogs/index/#toggleform' => array(
			'title' => 'Visibilité formulaire',
			'text' => 'Formulaire',
			'class' => 'search',
			'onclick' => "$( '{$searchFormId}' ).toggle(); return false;"
		)
	);

	echo $this->Default3->actions( $actions );

	echo $this->Form->create( null, array( 'type' => 'post', 'url' => array( 'controller' => $this->request->params['controller'], 'action' => $this->request->action ), 'id' => $searchFormId ) );

	echo $this->Default3->subform(
		$this->Translator->normalize(
			array(
				'Search.Relanceslogs.daterdv' => array( 'dateFormat'=>'DMY', 'type' => 'date', 'required' => false ),
			)
		),
		array(
			'fieldset' => true,
			'legend' => __m( 'Search.Relanceslogs.search' )
		)
	);

	echo '<div class="submit noprint">
			'.$this->Form->button( 'Rechercher', array( 'type' => 'submit' ) ).'
			'.$this->Form->button( 'Réinitialiser', array( 'type' => 'reset' ) ).'
		</div>';

	echo $this->Form->end();

	if( isset( $results ) ) {
		$this->Default3->DefaultPaginator->options(
			array( 'url' => Hash::flatten( (array)$this->request->data, '__' ) )
		);

		echo $this->Default3->index(
			$results,
			$this->Translator->normalize(
				array(
					'Relancelog.personne' => array(
						'value' => '#Relancelog.personne_id#<br />#Relancelog.nom_complet#<br />#Relancelog.numport#<br />#Relancelog.email#',
					),
					'Relancelog.relance1' => array(
						'value' => '#Relancelog.daterdv#<br />#Relancelog.heurerdv#<br />#Relancelog.lieurdv#',
					),
					'Relancelog.relance2' => array(
						'value' => '#Relancelog.relancetype#<br />#Relancelog.nombredejour# jour(s) avant',
					),
					'Relancelog.contenu',
					'Relancelog.support',
					'Relancelog.statut' => array(
						'value' => '#Relancelog.statut#<br />#Relancelog.created#',
					),
					'/Personnes/view/#Relancelog.personne_id#' => array(
						'target' => '_blank',
					),
				)
			),
			array(
				'paginate' => false,
				'format' => $this->element( 'pagination_format', array( 'modelName' => 'Relancelog' ) )
			)
		);
	}

	echo $this->Default3->actions( array( '/Parametrages/index' => array( 'class' => 'back' ) ) );
?>