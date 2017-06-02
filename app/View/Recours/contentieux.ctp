<?php
	$this->pageTitle = 'Recours contentieux';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<h1><?php echo $this->pageTitle;?></h1>

<?php if( empty( $contentieux ) ):?>
	<p class="notice">Ce dossier ne possède pas de recours contentieux.</p>

<?php else:?>
	<?php  echo $this->Form->create( 'Indus', array( 'type' => 'post', 'novalidate' => true ));?>
		<h2>Généralités</h2>
			<?php echo $this->Form->input( 'Recours.type_recours', array( 'label' => false, 'type' => 'radio', 'options' => array( 'G' => 'Gracieux', 'C' => 'Contentieux' ), 'legend' => 'Type de recours' ) ); ?>
			<?php echo $this->Form->input( 'Recours.date_recours', array( 'label' =>  ( __( 'date_recours' ) ), 'type' => 'date', 'dateFormat'=> 'DMY', 'maxYear'=>date('Y')+10, 'minYear'=> date('Y')-10 , 'empty' => true ) );?>
			<?php echo $this->Form->input( 'Recours.comment', array( 'label' => 'Commentaires contentieux', 'type' => 'textarea' ) ); ?>

		<h2>Décision tribunal administratif</h2>
			<?php echo $this->Form->input( 'Recours.date_commission', array( 'label' =>  ( __( 'date_commission' ) ), 'type' => 'date', 'dateFormat'=> 'DMY', 'maxYear'=>date('Y')+10, 'minYear'=>date('Y')-10 , 'empty' => true ) );?>
			<?php echo $this->Form->input( 'Recours.decision', array( 'label' => __( 'decision' ), 'type' => 'select', 'options' => $decisionrecours, 'empty' => true ) );?>
			<?php echo $this->Form->input( 'Recours.motif', array( 'label' => __( 'motif' ), 'type' => 'select', 'options' => $motifrecours, 'empty' => true ) );?>
			<?php echo $this->Form->input( 'Recours.avis', array( 'label' => 'Avis tribunal administratif', 'type' => 'textarea' ) ); ?>
	<?php echo $this->Form->submit( 'Enregistrer' );?>
<?php echo $this->Form->end();?>

<hr />

<h2>Liste des pièces</h2>
<table>
	<thead>
		<tr>
			<th>Type de la pièce</th>
			<th>Date d'enregistrement</th>
			<th class="action">Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php
			echo $this->Xhtml->tableCells(
				array(
					h( $contentieux['Infofinanciere']['id'] ),
					h( $contentieux['Infofinanciere']['id'] ),
					$this->Xhtml->viewLink(
						'Voir le document',
						array( 'controller' => 'recours', 'action' => 'contentieux', $contentieux['Infofinanciere']['id'] )
					),
				)
			);
		?>
	</tbody>
</table>

<?php endif;?>