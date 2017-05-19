<?php if( !empty( $modeletypecourrierpcg66 ) ): ?>
	<fieldset>
		<legend>Liste des modèles de courrier</legend>
		<table class="wide noborder">
			<tr>
				<td class="wide noborder">
					<?php
						echo '<div>';
						echo $this->Xform->input( "Modeletraitementpcg66.id", array( 'type' => 'hidden' ) );
						echo $this->Xform->input( "Modeletraitementpcg66.traitementpcg66_id", array( 'type' => 'hidden' ) );
						echo $this->Xform->input( 'Modeletraitementpcg66.modeletypecourrierpcg66_id', array( 'type' => 'hidden', 'value' => '', 'id' => 'Modeletraitementpcg66Modeletypecourrierpcg66Id_' ) );
						echo '</div>';

						echo '<div id="Modeletraitementpcg66Modeletypecourrierpcg66Id" class="input radio">';
						foreach( $modeletypecourrierpcg66 as $id => $name ) {
							echo '<div>';
							echo $this->Xform->singleRadioElement( "Modeletraitementpcg66.modeletypecourrierpcg66_id", $id, $name ).'<br/>';
							echo '<fieldset id="detailsmodelelie'.$id.'"><legend>Détails concernant le modèle lié &laquo; '.$name.' &raquo;</legend>';
								// INFO: attention, on peut se le permettre car il n'y a pas de règle de validation sur le commentaire
								$value = '';
								if( !empty( $this->request->data['Modeletraitementpcg66'] ) ) {
									if( $id == $this->request->data['Modeletraitementpcg66']['modeletypecourrierpcg66_id'] ) {
										if( isset( $this->request->data['Modeletraitementpcg66']['commentaire'] ) ) {
											$value = $this->request->data['Modeletraitementpcg66']['commentaire'];
										}
										else if( isset( $this->request->data['Modeletraitementpcg66'][$id]['commentaire'] ) ) {
											$value = $this->request->data['Modeletraitementpcg66'][$id]['commentaire'];
										}
									}
								}

								echo $this->Xform->input( "Modeletraitementpcg66.{$id}.commentaire", array(
									'label' =>  "Commentaire lié au modèle de courrier &laquo; {$name} &raquo;",
									'type' => 'textarea',
									'value' => $value
									)
								);
								
								if( in_array( $id, $modeletypecourrierpcg66avecmontant ) ) {
									echo $this->Xform->input( "Modeletraitementpcg66.montantsaisi", array(
										'label' =>  "Montant calculé",
										'type' => 'text',
										'value' => Hash::get($this->request->data, 'Modeletraitementpcg66.montantsaisi')
										)
									);
								}
								
								if( in_array( $id, $modeletypecourrierpcg66avecDates ) ) {
									echo $this->Xform->input( "Modeletraitementpcg66.montantdatedebut", array(
										'label' =>  "Du",
										'type' => 'date',
										'dateFormat' => 'DMY',
										'value' => Hash::get($this->request->data, 'Modeletraitementpcg66.montantdatedebut')
										)
									);
									echo $this->Xform->input( "Modeletraitementpcg66.montantdatefin", array(
										'label' =>  "Au",
										'type' => 'date',
										'dateFormat' => 'DMY',
										'value' => Hash::get($this->request->data, 'Modeletraitementpcg66.montantdatefin')
										)
									);
								}
								
								echo $this->Autrepiecetraitementpcg66->fieldsetPieces( 'Piecemodeletypecourrierpcg66', $id, $listepieces, $listePiecesWithAutre, 'Modeletraitementpcg66.autrepiecemanquante' );

							echo '</fieldset>';
							echo '</div>';
						}
						echo '</div>';
					?>
				</td>
			</tr>
		</table>
</fieldset>
<?php else:?>
	<?php 
		echo '<p class="notice">Aucune pièce liée à ce type de courrier<p>';
	?>
<?php endif;
	
	if( isset($modeletypecourrierpcg66) && $this->request->params['action'] === 'ajaxpiece' ) :
?>
<script type="text/javascript">
	//<![CDATA[
	<?php foreach( array_keys( $modeletypecourrierpcg66 ) as $id ) :?>
		observeDisableFieldsetOnRadioValue(
			'traitementpcg66form',
			'data[Modeletraitementpcg66][modeletypecourrierpcg66_id]',
			$( 'detailsmodelelie<?php echo $id;?>' ),
			'<?php echo $id;?>',
			false,
			true
		);
	<?php endforeach;?>
	//]]>
</script>
<?php endif;?>