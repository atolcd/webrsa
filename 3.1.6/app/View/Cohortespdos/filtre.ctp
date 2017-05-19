<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>

<?php
	if( !empty( $this->request->data ) ) {
		echo '<ul class="actionMenu"><li>'.$this->Xhtml->link(
			$this->Xhtml->image(
				'icons/application_form_magnify.png',
				array( 'alt' => '' )
			).' Formulaire',
			'#',
			array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "var form = $$( 'form' ); form = form[0]; $( form ).toggle(); return false;" )
		).'</li></ul>';
	}

// 	echo $this->Form->create( null, array( 'id' => 'Search', 'class' => ( !empty( $this->request->data ) ? 'folded' : 'unfolded' ) ) );

	echo $this->Form->create( null, array( 'type' => 'post', 'url' => array( 'controller' => $this->request->params['controller'], 'action' => $this->request->params['action'] ), 'id' => 'Search', 'class' => ( ( is_array( $this->request->data ) && !empty( $this->request->data ) && isset( $this->request->data['Search']['active'] ) ) ? 'folded' : 'unfolded' ) ) );

	echo $this->Form->input( 'Search.active', array( 'type' => 'hidden', 'value' => true ) );


?>

<script type="text/javascript">
	document.observe("dom:loaded", function() {
		observeDisableFieldsetOnCheckbox( 'SearchPropopdoDatedecisionpdo', $( 'SearchPropopdoDatedecisionpdoFromDay' ).up( 'fieldset' ), false );
	});
</script>
	<?php
		echo $this->Search->blocAllocataire( array(), array(), 'Search' );
		echo $this->Search->blocDossier(   $options['etatdosrsa'], 'Search' );
		echo $this->Search->blocAdresse( $options['mesCodesInsee'], $options['cantons'], 'Search' );
	?>

<fieldset class= "noprint">
		<legend>Recherche PDO</legend>

		<?php
			echo $this->Form->input( 'Search.Propopdo.typepdo_id', array( 'label' =>  ( __d( 'propopdo', 'Propopdo.typepdo_id' ) ), 'type' => 'select', 'options' => $typepdo, 'empty' => true ) );
			echo $this->Form->input( 'Search.Propopdo.decisionpdo_id', array( 'label' =>  ( __( 'Décision du Conseil Général' ) ), 'type' => 'select', 'options' => $decisionpdo, 'empty' => true ) );
			echo $this->Form->input( 'Search.Propopdo.motifpdo', array( 'label' => __d( 'propopdo', 'Propopdo.motifpdo' ), 'type' => 'select', 'options' => $motifpdo, 'empty' => true ) );
			echo $this->Form->input( 'Search.Propopdo.user_id', array( 'label' => __d( 'propopdo', 'Propopdo.user_id' ), 'type' => 'select', 'options' => $gestionnaire, 'empty' => true ) );
		?>
			<?php echo $this->Form->input( 'Search.Propopdo.datedecisionpdo', array( 'label' => 'Filtrer par date de décision des PDOs', 'type' => 'checkbox' ) );?>
			<fieldset>
				<legend>Date de saisie de la PDO</legend>
				<?php
					$datedecisionpdo_from = Set::check( $this->request->data, 'Search.Propopdo.datedecisionpdo_from' ) ? Set::extract( $this->request->data, 'Search.Propopdo.datedecisionpdo_from' ) : strtotime( '-1 week' );
					$datedecisionpdo_to = Set::check( $this->request->data, 'Search.Propopdo.datedecisionpdo_to' ) ? Set::extract( $this->request->data, 'Search.Propopdo.datedecisionpdo_to' ) : strtotime( 'now' );
				?>
				<?php echo $this->Form->input( 'Search.Propopdo.datedecisionpdo_from', array( 'label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 120, 'selected' => $datedecisionpdo_from ) );?>
				<?php echo $this->Form->input( 'Search.Propopdo.datedecisionpdo_to', array( 'label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 5, 'minYear' => date( 'Y' ) - 120, 'selected' => $datedecisionpdo_to ) );?>
			</fieldset>
		<?php
			echo $this->Search->referentParcours( $structuresreferentesparcours, $referentsparcours, 'Search' );
			echo $this->Search->paginationNombretotal( 'Search.Pagination.nombre_total' );
			echo $this->Search->observeDisableFormOnSubmit( 'Search' );
		?>

	</fieldset>
	<div class="submit noprint">
		<?php echo $this->Form->button( 'Rechercher', array( 'type' => 'submit' ) );?>
		<?php echo $this->Form->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
	</div>
<?php echo $this->Form->end();?>