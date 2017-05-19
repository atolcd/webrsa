<?php if( !empty( $referent ) ):?>
	<table class="wide noborder">
		<tr>
			<td class="mediumSize noborder"><strong>Fonction</strong></td>
			<td class="mediumSize noborder"><?php echo Set::classicExtract( $referent, 'Referent.fonction' );?></td>
		</tr>
	</table>
<?php endif;?>