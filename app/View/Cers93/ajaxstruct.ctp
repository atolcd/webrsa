<?php if( !empty( $struct ) ):?>
<table class="wide noborder">
	<tr>
		<td class="wide noborder"><strong>Type d'orientation</strong></td>
		<td class="wide noborder"><strong>Adresse</strong></td>
	</tr>
	<tr>
		<td class="wide noborder"><?php echo Set::classicExtract( $struct, 'Typeorient.lib_type_orient' );?></td>
		<td class="wide noborder"><?php echo Set::classicExtract( $struct, 'Structurereferente.num_voie').' '.Set::enum( Set::classicExtract( $struct, 'Structurereferente.type_voie'), $options['Structurereferente']['type_voie'] ).' '.Set::classicExtract( $struct, 'Structurereferente.nom_voie').'<br /> '.Set::classicExtract( $struct, 'Structurereferente.code_postal').' '.Set::classicExtract( $struct, 'Structurereferente.ville');?></td>
	</tr>
</table>
<?php endif;?>