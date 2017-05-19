<?php if( !empty( $result ) ):?>
<table class="wide noborder">
	<tr>
		<th>Adresse</th>
		<td class="wide noborder">
			<?php echo Hash::get( $result, 'Adresseprestatairefp93.adresse');?><br />
			<?php echo Hash::get( $result, 'Adresseprestatairefp93.codepos');?> <?php echo Hash::get( $result, 'Adresseprestatairefp93.localite');?>
		</td>
	</tr>
	<tr>
		<th>Tél.</th>
		<td class="wide noborder"><?php echo Hash::get( $result, 'Adresseprestatairefp93.tel');?></td>
	</tr>
	<tr>
		<th>Fax</th>
		<td class="wide noborder"><?php echo Hash::get( $result, 'Adresseprestatairefp93.fax');?></td>
	</tr>
	<tr>
		<th>Mail</th>
		<td class="wide noborder"><?php echo Hash::get( $result, 'Adresseprestatairefp93.email');?></td>
	</tr>
</table>
<?php endif;?>