<?php if( !empty( $result ) ):?>
<table class="wide noborder">
	<tr>
		<th>Fonction</th>
		<td class="wide noborder"><?php echo Hash::get( $result, 'Referent.fonction');?></td>
	</tr>
	<tr>
		<th>Adresse</th>
		<td class="wide noborder"><?php echo Hash::get( $result, 'Structurereferente.num_voie').' '.Hash::get( $result, 'Structurereferente.type_voie').' '.Hash::get( $result, 'Structurereferente.nom_voie').'<br /> '.Hash::get( $result, 'Structurereferente.code_postal').' '.Hash::get( $result, 'Structurereferente.ville');?></td>
	</tr>
	<tr>
		<th>Tél.</th>
		<td class="wide noborder"><?php echo Hash::get( $result, 'Structurereferente.numtel');?></td>
	</tr>
	<tr>
		<th>Fax</th>
		<td class="wide noborder"><?php echo Hash::get( $result, 'Structurereferente.numfax');?></td>
	</tr>
	<tr>
		<th>Mail</th>
		<td class="wide noborder"><?php echo Hash::get( $result, 'Referent.email');?></td>
	</tr>
</table>
<?php endif;?>