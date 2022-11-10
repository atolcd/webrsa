<fieldset>
	<legend>État civil</legend>
		<?php
			echo $this->Form->input( 'Personne.qual', array( 'label' => required( __d( 'personne', 'Personne.qual' ) ), 'type' => 'select', 'options' => $qual, 'empty' => true ) );
			echo $this->Form->input( 'Personne.nom', array( 'label' => required( __d( 'personne', 'Personne.nom' ) ) ) );
			echo $this->Form->input( 'Personne.nomnai', array( 'label' => __d( 'personne', 'Personne.nomnai' ) ) );
			echo $this->Form->input( 'Personne.prenom', array( 'label' => required( __d( 'personne', 'Personne.prenom' ) ) ) );
			echo $this->Form->input( 'Personne.prenom2', array( 'label' => __d( 'personne', 'Personne.prenom2' ) ) );
			echo $this->Form->input( 'Personne.prenom3', array( 'label' => __d( 'personne', 'Personne.prenom3' ) ) );
			echo $this->Form->input( 'Personne.typedtnai', array( 'label' => __d( 'personne', 'Personne.typedtnai' ), 'type' => 'select', 'options' => $typedtnai, 'empty' => true ) );
			echo $this->Form->input( 'Personne.dtnai', array( 'label' => required( __d( 'personne', 'Personne.dtnai' ) ), 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 1, 'minYear' => ( date( 'Y' ) - 100 ), 'empty' => true ) );
			echo $this->Form->input( 'Personne.nomcomnai', array( 'label' => __d( 'personne', 'Personne.nomcomnai' ) ) );
			echo $this->Form->input( 'Personne.rgnai', array( 'label' => __d( 'personne', 'Personne.rgnai' ), 'maxlength' => 2) );
			echo $this->Form->input( 'Personne.nir', array( 'label' =>  __d( 'personne', 'Personne.nir' ) ) );
			if( $this->action != 'wizard' ){
				echo $this->Default->view(
					$personne,
					array(
						'Foyer.sitfam' => array( 'options' => $sitfam ),
					),
					array(
						'widget' => 'table',
						'id' => 'dossierInfosOrganisme'
					)
				);
			}
			echo $this->Form->input( 'Personne.topvalec', array( 'label' => __d( 'personne', 'Personne.topvalec' ) ) );

			if($this->action == 'edit' ) {
				echo $this->Form->input( 'Personne.numfixe', array('disabled' => true, 'label' => __d( 'personne', 'Personne.numfixe' ) ) );
				echo $this->Form->input( 'Personne.numport', array('disabled' => true, 'label' => __d( 'personne', 'Personne.numport' ) ) );
				echo $this->Form->input( 'Personne.email', array('disabled' => true, 'label' => __d( 'personne', 'Personne.email' ) ) );
			}
		?>
</fieldset>

<fieldset>
	<legend>Nationalité</legend>
	<?php echo $this->Form->input( 'Personne.nati', array( 'label' => __d( 'personne', 'Personne.nati' ), 'type' => 'select', 'options' => $nationalite, 'empty' => true ) );?>
	<?php echo $this->Form->input( 'Personne.dtnati', array( 'label' => __d( 'personne', 'Personne.dtnati' ), 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => ( date( 'Y' ) - 100 ), 'empty' => true ) );?>
	<?php echo $this->Form->input( 'Personne.pieecpres', array( 'label' => __d( 'personne', 'Personne.pieecpres' ), 'type' => 'select', 'options' => $pieecpres, 'empty' => true ) );?>
</fieldset>

<script type="text/javascript">
	document.observe( "dom:loaded", function() {
		observeDisableFieldsOnValue( 'PersonneQual', [ 'PersonneNomnai' ], 'MME', false );
		observeDisableFieldsOnValue( 'PrestationRolepers', [ 'PersonneQual' ], 'ENF', true );

		$( 'PersonneTypedtnai' ).observe( 'change', function( event ) {
			var type = $F( 'PersonneTypedtnai' );
			if( type == 'J' ) {
				$( 'PersonneDtnaiDay' ).value = '01';
			}
			else if( type == 'O' ) {
				$( 'PersonneDtnaiDay' ).value = '31';
				$( 'PersonneDtnaiMonth' ).value = '12';
			}
		});
	} );
</script>