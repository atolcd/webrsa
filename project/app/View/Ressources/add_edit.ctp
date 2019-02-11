<?php
	if( $this->action == 'add' ) {
		$this->pageTitle = 'Déclaration de ressources';
	}
	else {
		$this->pageTitle = 'Édition de ressources';
	}

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<h1><?php echo $this->pageTitle;?></h1>

<?php
	if( $this->action == 'add' ) {
		echo $this->Form->create( 'Ressource', array( 'type' => 'post', 'novalidate' => true  ));
	}
	else {
		echo $this->Form->create( 'Ressource', array( 'type' => 'post', 'novalidate' => true  ));
		echo '<div>';
		echo $this->Form->input( 'Ressource.id', array( 'type' => 'hidden' ) );
		echo '</div>';

		for( $i = 0 ; $i < 3 ; $i ++ ) {
			if( Set::extract( $this->request->data, 'Ressourcemensuelle.'.$i.'.id' ) !== null ) {
				echo '<div>';
				echo $this->Form->input( 'Ressourcemensuelle.'.$i.'.id', array( 'type' => 'hidden' ) );
				echo '</div>';
				echo $this->Form->input( 'Ressourcemensuelle.'.$i.'.ressource_id', array( 'type' => 'hidden' ) );
				if( Set::extract( $this->request->data, 'Detailressourcemensuelle.'.$i.'.id' ) !== null ) {
					echo $this->Form->input( 'Detailressourcemensuelle.'.$i.'.id', array( 'type' => 'hidden' ) );
					echo $this->Form->input( 'Detailressourcemensuelle.'.$i.'.ressource_id', array( 'type' => 'hidden' ) );
				}
			}
		}
	}
	echo '<div>';
	echo $this->Form->input( 'Ressource.personne_id', array( 'type' => 'hidden', 'value' => $personne_id ) );
	echo '</div>';
?>

<script type="text/javascript">
	function checkDatesToRefresh() {
		if( ( $F( 'RessourceDdressMonth' ) ) && ( $F( 'RessourceDdressYear' ) ) ) {
			setDateInterval( 'RessourceDdress', 'RessourceDfress', 3, false );

			for( var i = 0 ; i <= 2 ; i++ ) {
				setDateInterval( 'RessourceDdress', 'Ressourcemensuelle' + i + 'Moisress', i, true );
				setDateInterval( 'RessourceDdress', 'Detailressourcemensuelle' + i + 'Dfpercress', i + 1, false );
			}
		}
	}

	document.observe("dom:loaded", function() {
		for( var i = 0 ; i < 3 ; i++ ) {
			observeDisableFieldsetOnCheckbox(
				'RessourceTopressnotnul',
				$( 'Ressourcemensuelle' + i + 'MoisressMonth' ).up( 'fieldset' ),
				false
			);
		}

		Event.observe( $( 'RessourceDdressMonth' ), 'change', function() {
			checkDatesToRefresh();
		} );
		Event.observe( $( 'RessourceDdressYear' ), 'change', function() {
			checkDatesToRefresh();
		} );

	});
</script>

<fieldset>
	<legend>Généralités des ressources du trimestre</legend>
	<?php echo $this->Form->input( 'Ressource.ddress', array( 'label' => required( __d( 'ressource', 'Ressource.ddress' ) ), 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear'=>date('Y')+1, 'minYear'=>date('Y')-20, 'empty' => true));?>
	<?php echo $this->Form->input( 'Ressource.dfress', array( 'label' => required( __d( 'ressource', 'Ressource.dfress' ) ), 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear'=>date('Y')+1, 'minYear'=>date('Y')-20, 'empty' => true));?>
</fieldset>

<div><?php echo $this->Form->input( 'Ressource.topressnotnul', array( 'label' => __d( 'ressource', 'Ressource.topressnotnul' ), 'type' => 'checkbox' ) );?></div>

<?php for( $i = 0 ; $i < 3 ; $i++ ):?>
	<fieldset>
		<legend>Ressources mensuelles</legend>
		<?php echo $this->Form->input( 'Ressourcemensuelle.'.$i.'.moisress', array( 'label' => __d( 'ressource', 'Ressource.moisress' ), 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear'=>date('Y')+1, 'minYear'=>date('Y')-20, 'empty' => true) );?><!-- FIXME: la date ne doit pas comporter de jour à l'affichage -->
		<?php echo $this->Form->input( 'Ressourcemensuelle.'.$i.'.nbheumentra', array( 'label' => __d( 'ressource', 'Ressource.nbheumentra' ), 'type' => 'text', 'maxlength' => 3 ) );?>
		<?php echo $this->Form->input( 'Ressourcemensuelle.'.$i.'.mtabaneu', array( 'label' => __d( 'ressource', 'Ressource.mtabaneu' ), 'type' => 'text', 'maxlength' => 11 ) );?>

		<?php echo $this->Form->input( 'Detailressourcemensuelle.'.$i.'.natress', array( 'label' => __d( 'ressource', 'Ressource.natress' ), 'type' => 'select', 'options' => $natress, 'empty' => true ) );?>
		<?php echo $this->Form->input( 'Detailressourcemensuelle.'.$i.'.mtnatressmen', array( 'label' => __d( 'ressource', 'Ressource.mtnatressmen' ), 'type' => 'text', 'maxlength' => 11 ) );?>
		<?php echo $this->Form->input( 'Detailressourcemensuelle.'.$i.'.abaneu', array( 'label' => __d( 'ressource', 'Ressource.abaneu' ), 'type' => 'select', 'options' => $abaneu, 'empty' => true ) );?>
		<?php echo $this->Form->input( 'Detailressourcemensuelle.'.$i.'.dfpercress', array( 'label' => __d( 'ressource', 'Ressource.dfpercress' ), 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear'=>date('Y')+1, 'minYear'=>date('Y')-20, 'empty' => true) );?>
		<?php echo $this->Form->input( 'Detailressourcemensuelle.'.$i.'.topprevsubsress', array( 'label' => __d( 'ressource', 'Ressource.topprevsubsress' ), 'type' => 'checkbox' ) );?>
	</fieldset>
<?php endfor;?>

<?php echo $this->Form->submit( 'Enregistrer' );?>
<?php echo $this->Form->end();?>