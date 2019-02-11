<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	$isRecours = Set::classicExtract( $this->request->params, 'named.recours' );

	if( $isRecours ) {
		$this->pageTitle = 'Modification de la liste des APREs en Recours pour le comité d\'examen';
	}
	else {
		$this->pageTitle = 'Modification de la liste des APREs pour le comité d\'examen';
	}
?>


	<h1><?php echo $this->pageTitle;?></h1>
	<?php
		echo $this->Xhtml->tag(
			'ul',
			implode(
				'',
				array(
					$this->Xhtml->tag( 'li', $this->Xhtml->link( 'Tout sélectionner', '#', array( 'onclick' => 'allCheckboxes( true ); return false;' ) ) ),
					$this->Xhtml->tag( 'li', $this->Xhtml->link( 'Tout désélectionner', '#', array( 'onclick' => 'allCheckboxes( false ); return false;' ) ) ),
				)
			)
		);
	?>
	<?php if( empty( $apres ) ):?>
		<p class="notice">Aucune demande d'APRE <?php echo  Set::classicExtract( $this->request->params, 'named.recours' ) ? 'en Recours ' : '' ;?>présente.</p>
	<?php else:?>
	<?php echo $this->Xform->create( 'ApreComiteapre', array( 'type' => 'post' ) ); ?>
		<div class="aere">
			<fieldset>
				<legend>APREs à traiter durant le comité</legend>
					<?php echo $this->Xform->input( 'Comiteapre.id', array( 'label' => false, 'type' => 'hidden' ) ) ;?>
				<table>
					<thead>
						<tr>
							<th>N° APRE</th>
							<th>Nom/Prénom</th>
							<th>Date demande APRE</th>
							<th>Sélectionner</th>
						</tr>
					</thead>

					<tbody>
						<?php
							foreach( $apres as $i => $apre ) {
								$apreApre = Set::extract( $this->request->data, 'Apre.Apre' );
								if( empty( $apreApre ) ) {
									$apreApre = array();
								}

								echo $this->Xhtml->tableCells(
									array(
										h( Set::classicExtract( $apre, 'Apre.numeroapre' ) ),
										h( Set::classicExtract( $apre, 'Personne.qual' ).' '.Set::classicExtract( $apre, 'Personne.nom' ).' '.Set::classicExtract( $apre, 'Personne.prenom' ) ),
										h( $this->Locale->date( 'Date::short', Set::classicExtract( $apre, 'Apre.datedemandeapre' ) ) ),

										$this->Xform->checkbox( 'Apre.Apre.'.$i, array( 'value' => Set::classicExtract( $apre, 'Apre.id' ), 'id' => 'ApreApre'.Set::classicExtract( $apre, 'Apre.id' ), 'checked' => in_array( Set::classicExtract( $apre, 'Apre.id' ), $apreApre ), 'class' => 'checkbox' ) ),
									),
									array( 'class' => 'odd' ),
									array( 'class' => 'even' )
								);
							}

						?>
					</tbody>
				</table>
			</fieldset>
		</div>

		<div class="submit">
			<?php echo $this->Form->submit( 'Enregistrer', array( 'div' => false ) );?>
			<?php echo $this->Form->submit( 'Annuler', array( 'name' => 'Cancel', 'div' => false ) );?>
		</div>
		<?php echo $this->Xform->end();?>
	<?php endif;?>
<script type="text/javascript">
//<![CDATA[
	function allCheckboxes( checked ) {
		$$('input.checkbox').each( function ( checkbox ) {
			$( checkbox ).checked = checked;
		} );
		return false;
	}
//]]>
</script>
<div class="clearer"><hr /></div>