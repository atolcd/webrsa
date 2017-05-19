<?php if( !empty( $referent ) ):?>
<table class="wide noborder">
	<tr>
		<td class="wide noborder"><strong>Fonction</strong></td>
		<td class="wide noborder"><strong>Email</strong></td>
		<td class="wide noborder"><strong>N° téléphone</strong></td>
	</tr>
	<tr>
		<td class="wide noborder"><?php echo Set::classicExtract( $referent, 'Referent.fonction' );?></td>
		<td class="wide noborder"><?php echo Set::classicExtract( $referent, 'Referent.email' );?></td>
		<td class="wide noborder"><?php echo Set::classicExtract( $referent, 'Referent.numero_poste' );?></td>
	</tr>
</table>
<?php endif;?>