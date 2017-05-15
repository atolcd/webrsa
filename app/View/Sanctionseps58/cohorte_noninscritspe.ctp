<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}

	echo $this->Default3->titleForLayout();

	echo $this->Default3->actions(
		array(
			'/Sanctionseps58/cohorte_radiespe/#toggleform' => array(
				'onclick' => '$(\'Sanctionep58CohorteNoninscritspeSearchForm\').toggle(); return false;',
				'class' => 'search'
			)
		)
	);
?>

<?php echo $this->Form->create( null, array( 'type' => 'post', 'url' => array( 'action' => $this->request->action ), 'id' => 'Sanctionep58CohorteNoninscritspeSearchForm', 'class' => ( !empty( $this->request->params['named'] ) ? 'folded' : 'unfolded' ) ) );?>
	<?php
		echo $this->Allocataires->blocDossier( array( 'prefix' => 'Search', 'options' => $options ) );
		echo $this->Allocataires->blocAdresse( array( 'prefix' => 'Search', 'options' => $options ) );
		echo $this->Allocataires->blocAllocataire( array( 'prefix' => 'Search', 'options' => $options ) );
		echo $this->Allocataires->blocReferentparcours( array( 'prefix' => 'Search', 'options' => $options ) );
		echo $this->Allocataires->blocPagination( array( 'prefix' => 'Search', 'options' => $options ) );
		echo $this->Allocataires->blocScript( array( 'prefix' => 'Search', 'options' => $options ) );
	?>
	<div class="submit noprint">
		<?php echo $this->Form->button( 'Rechercher', array( 'type' => 'submit' ) );?>
		<?php echo $this->Form->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
	</div>
<?php
	echo $this->Form->end();
	echo $this->Observer->disableFormOnSubmit('Sanctionep58CohorteNoninscritspeSearchForm');
?>

<?php
	if( isset( $results ) ) {
		echo $this->Html->tag( 'h2', 'Résultats de la recherche' );

		echo $this->Xform->create( null,
			array(
				'id' => 'Sanctionep58CohorteNoninscritspeCohorte',
//				'url' => Router::url( array( 'controller' => $controller, 'action' => $action ), true )
			)
		);

		echo $this->Default3->configuredCohorte( $results, $configuredCohorteParams	);

		echo $this->Xform->end( 'Save' );
		echo $this->Observer->disableFormOnSubmit('Sanctionep58CohorteNoninscritspeCohorte');

		echo $this->element( 'search_footer', array( 'modelName' => 'Personne', 'url' => array( 'action' => 'exportcsv_noninscritspe' ) ) );

		echo $this->Form->button( 'Tout cocher', array( 'type' => 'button', 'onclick' => 'return toutCocher();' ) );
		echo $this->Form->button( 'Tout décocher', array( 'type' => 'button', 'onclick' => 'return toutDecocher();' ) );
	}
?>