<?php if( !empty( $struct ) ):?>
<table class="wide noborder">
	<tr>
		<td class="wide noborder"><strong>Adresse</strong></td>
	</tr>
	<tr>
		<td class="wide noborder"><?php echo Set::classicExtract( $struct, 'Structurereferente.num_voie').' '.Set::classicExtract( $struct, 'Structurereferente.type_voie').' '.Set::classicExtract( $struct, 'Structurereferente.nom_voie').'<br /> '.Set::classicExtract( $struct, 'Structurereferente.code_postal').' '.Set::classicExtract( $struct, 'Structurereferente.ville');?></td>
	</tr>
</table>
<?php endif;?>
