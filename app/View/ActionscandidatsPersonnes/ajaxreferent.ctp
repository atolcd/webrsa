<?php if( !empty( $prescripteur ) ): ?>
<fieldset>
	<legend>Prescripteur</legend>
    <table class="wide noborder">
		<tr>
			<td rowspan="3" class="wide noborder" style="width: 400px"><strong>Adresse : </strong></td>
			<td class="wide noborder">
				<?php echo $prescripteur['Structurereferente']['lib_struc']; ?>
			</td>
		</tr>        
		<tr>
			<td class="wide noborder">
				<?php 
					echo $prescripteur['Structurereferente']['num_voie'] . ' ';
					echo value( $typevoie, Set::classicExtract( $prescripteur, 'Structurereferente.type_voie' ) ) .  ' ';
					echo $prescripteur['Structurereferente']['nom_voie'] . ' ';
				?>
			</td>
		</tr>
		<tr>
			<td class="wide noborder">
				<?php 
					echo $prescripteur['Structurereferente']['code_postal'];
					echo $prescripteur['Structurereferente']['ville'];
				?>
			</td>
		</tr>	
		<tr>
			<td class="wide noborder"><strong>N° poste : </strong></td>
			<td class="wide noborder">
				<?php echo $prescripteur['Referent']['numero_poste']; ?>
			</td>
		</tr>
		<tr>
			<td class="wide noborder"><strong>Courriel : </strong></td>
			<td class="wide noborder">
				<?php echo $prescripteur['Referent']['email']; ?>
			</td>
		</tr>
	</table>
</fieldset>		
<?php endif;?>