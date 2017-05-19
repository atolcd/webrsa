<?php
	$this->pageTitle = 'Services instructeurs';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	};
?>

<h1><?php echo $this->pageTitle;?></h1>

<?php
	if( $this->action == 'add' ) {
		echo $this->Form->create( 'Serviceinstructeur', array( 'type' => 'post' ) );
		echo '<div>';
		echo $this->Form->input( 'Serviceinstructeur.id', array( 'type' => 'hidden' ) );
		echo '</div>';
	}
	else {
		echo $this->Form->create( 'Serviceinstructeur', array( 'type' => 'post' ) );
		echo '<div>';
		echo $this->Form->input( 'Serviceinstructeur.id', array( 'type' => 'hidden' ) );
		echo '</div>';
	}
?>

	<fieldset>
		<?php
			echo $this->Default->subform(
				array(
					'Serviceinstructeur.lib_service' => array( 'label' => ( __( 'lib_service' ) ) ),
					'Serviceinstructeur.num_rue' => array( 'label' =>  __( 'num_rue' ) ),
					'Serviceinstructeur.type_voie' => array( 'label' =>  ( __( 'type_voie' ) ), 'options' => $typevoie ),
					'Serviceinstructeur.nom_rue' => array( 'label' =>  __( 'nom_rue' ) ),
					'Serviceinstructeur.complement_adr' => array( 'label' =>  __( 'complement_adr' ) ),
					'Serviceinstructeur.code_insee' => array( 'label' =>  ( __( 'code_insee' ) ) ),
					'Serviceinstructeur.code_postal' => array( 'label' =>  __( 'code_postal' ) ),
					'Serviceinstructeur.ville' => array( 'label' =>  __( 'ville' ) ),
                    'Serviceinstructeur.email' => array( 'label' =>  __( 'email' ) )
				)
			);
		?>
	</fieldset>
	<fieldset>
		<?php
			echo $this->Default->subform(
				array(
					'Serviceinstructeur.numdepins' => array( 'label' => ( __d( 'suiviinstruction', 'Suiviinstruction.numdepins' ) ) ),
					'Serviceinstructeur.typeserins' => array( 'options' => $typeserins, 'type' => 'select', 'label' => ( __d( 'suiviinstruction', 'Suiviinstruction.typeserins' ) ), 'empty' => true ),
					'Serviceinstructeur.numcomins' => array( 'label' => ( __d( 'suiviinstruction', 'Suiviinstruction.numcomins' ) ) ),
					'Serviceinstructeur.numagrins' => array( 'label' => ( __d( 'suiviinstruction', 'Suiviinstruction.numagrins' ) ), 'maxlength' => 2 ),
				),
				array(

				)
			);
		?>
	</fieldset>
	<?php if( Configure::read( 'Recherche.qdFilters.Serviceinstructeur' ) ):?>
	<fieldset>
		<?php
			echo $this->Default->subform(
				array(
					'Serviceinstructeur.sqrecherche' => array( 'rows' => 40 ),
				)
			);
		?>
	</fieldset>
	<?php endif;?>

	<div class="submit">
		<?php
			echo $this->Xform->submit( 'Enregistrer', array( 'div' => false ) );
			echo $this->Xform->submit( 'Annuler', array( 'name' => 'Cancel', 'div' => false ) );
		?>
	</div>
<?php echo $this->Form->end();?>

<?php
	if( isset( $sqlErrors ) && !empty( $sqlErrors ) ) {
		echo '<h2>Erreurs SQL dans les moteurs de recherche</h2>';
		foreach( $sqlErrors as $key => $error ) {
			echo "<div class=\"query\">";
			echo "<h3>".__d( Inflector::underscore( $key ), sprintf( "%s::index", Inflector::pluralize( $key ) ) )."</h3>";
			echo "<div class=\"errormessage\">".nl2br( $error['error'] )."</div>";
			echo "<div class=\"sql\">".nl2br( str_replace( "\t", "&nbsp;&nbsp;&nbsp;&nbsp;", $error['sql'] ) )."</div>";
			echo "</div>";
		}
	}
?>