<div class="submit">
	<?php echo $this->Form->button( 'Imprimer', array( 'type' => 'submit' ) );?>
</div>

<?php if( isset( $orients ) ):?>
	<h2>Résultats de la recherche</h2>

	<?php if( is_array( $orients ) && count( $orients ) > 0  ):?>
		<table>
			<tbody>
				<tr class="even">
					<th><?php echo __( 'Numéro dossier' );?></th>
					<th><?php echo __( 'Allocataire' );?></th>
					<th><?php echo __( 'numtel' );?></th>
					<th><?php echo __d( 'adresse', 'Adresse.nomcom' );?></th>
					<th><?php echo __( 'Date d\'ouverture droits' );?></th>
					<th><?php echo __( 'Date d\'orientation' );?></th>
					<th><?php echo __( 'Structure référente' );?></th>
				</tr>
				<?php foreach( $orients as $orient ):?>
					<tr>
						<td><?php echo $orient['Dossier']['numdemrsa'];?></td>
						<td><?php echo $orient['Personne']['qual'].' '.$orient['Personne']['nom'].' '.$orient['Personne']['prenom'];?></td>
						<td><?php echo $orient['ModeContact']['numtel'];?></td>
						<td><?php echo $orient['Adresse']['nomcom'];?></td>
						<td><?php echo date_short( $orient['Dossier']['dtdemrsa'] );?></td>
						<td><?php echo date_short( $orient['Orientstruct']['date_propo'] );?></td>
						<td><?php echo isset( $sr[$orient['Orientstruct']['structurereferente_id']] ) ? $sr[$orient['Orientstruct']['structurereferente_id']] : null;?></td>
					</tr>
			<?php endforeach;?>
			</tbody>
		</table>
	<?php endif?>
<?php endif?>
<div class="clearer"><hr /></div>