<?php if( !empty( $tiersprestataireapre ) ):?>
	<table class="wide noborder">
		<tr>
			<td class="wide noborder"><strong>Adresse</strong></td>
			<td class="wide noborder"><strong>Email</strong></td>
			<td class="wide noborder"><strong>N° téléphone</strong></td>
		</tr>
		<tr>
			<td class="wide noborder"><?php echo Set::classicExtract( $tiersprestataireapre, 'Tiersprestataireapre.numvoie' ).' '.Set::enum( Set::classicExtract( $tiersprestataireapre, 'Tiersprestataireapre.typevoie' ), $typevoie ).' '.Set::classicExtract( $tiersprestataireapre, 'Tiersprestataireapre.nomvoie' ).' '.Set::classicExtract( $tiersprestataireapre, 'Tiersprestataireapre.codepos' ).' '.Set::classicExtract( $tiersprestataireapre, 'Tiersprestataireapre.ville' );?></td>
			<td class="wide noborder"><?php echo Set::classicExtract( $tiersprestataireapre, 'Tiersprestataireapre.adrelec' );?></td>
			<td class="wide noborder"><?php echo Set::classicExtract( $tiersprestataireapre, 'Tiersprestataireapre.numtel' );?></td>
		</tr>
	</table>
<?php endif;?>