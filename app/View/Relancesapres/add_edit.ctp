<?php
	if( $this->action == 'add' ) {
		$this->pageTitle = 'Ajout relance';
	}
	else {
		$this->pageTitle = 'Édition relance';
	}

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<h1><?php echo $this->pageTitle;?></h1>
<?php
	if( $this->action == 'add' ) {
		echo $this->Form->create( 'Relanceapre', array( 'type' => 'post' ) );
		echo '<div>';
		echo $this->Form->input( 'Relanceapre.apre_id', array( 'type' => 'hidden', 'value' => Set::classicExtract( $apre, 'Apre.id' ) ) );
		echo '</div>';
	}
	else {
		echo $this->Form->create( 'Relanceapre', array( 'type' => 'post' ) );
		echo '<div>';
		echo $this->Form->input( 'Relanceapre.id', array( 'type' => 'hidden' ) );
		echo $this->Form->input( 'Relanceapre.apre_id', array( 'type' => 'hidden' ) );
		echo '</div>';
	}
?>

<div class="aere">
	<fieldset>
		<?php
			echo $this->Xform->input( 'Relanceapre.daterelance', array( 'domain' => 'apre', 'dateFormat' => 'DMY' ) );
			echo $this->Xform->input( 'Relanceapre.commentairerelance', array( 'domain' => 'apre' ) );
		?>
	</fieldset>
	<fieldset>
		<legend>Pièces jointes manquantes</legend>
		<?php
			$piecesManquantesAides = Set::classicExtract( $apre, "Apre.Piece.Manquante" );
			$listeParAides = '';
			foreach( $piecesManquantesAides as $model => $pieces ) {
				if( !empty( $pieces ) ) {
					echo $this->Xhtml->tag( 'h2', __d( 'apre', $model ) ).'<ul><li>'.implode( '</li><li>', $pieces ).'</li></ul>';
					$listeParAides .= $this->Xhtml->tag( 'h2', __d( 'apre', $model ) ).'<ul><li>'.implode( '</li><li>', $pieces ).'</li></ul>';
				}
			}

			echo $this->Xform->input( 'Relanceapre.listepiecemanquante', array( 'domain' => 'apre', 'type' => 'hidden', 'value' => $listeParAides ) );
		?>
	</fieldset>
</div>

<div class="submit">
	<?php echo $this->Form->submit( 'Enregistrer', array( 'div' => false ) );?>
	<?php echo $this->Form->submit('Annuler', array( 'name' => 'Cancel', 'div' => false ) );?>
</div>
<?php echo $this->Form->end();?>