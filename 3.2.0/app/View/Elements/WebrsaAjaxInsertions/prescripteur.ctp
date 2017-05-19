<?php if( !empty( $record ) ): ?>
<fieldset>
	<legend>Prescripteur</legend>
    <table class="wide noborder">
		<tr>
			<td rowspan="3" class="wide noborder" style="width: 400px"><strong>Adresse : </strong></td>
			<td class="wide noborder">
				<?php echo $record['Structurereferente']['lib_struc']; ?>
			</td>
		</tr>
		<tr>
			<td class="wide noborder">
				<?php
					echo $record['Structurereferente']['num_voie'] . ' ';
					echo $record['Structurereferente']['type_voie'] . ' ';
					echo $record['Structurereferente']['nom_voie'] . ' ';
				?>
			</td>
		</tr>
		<tr>
			<td class="wide noborder">
				<?php
					echo $record['Structurereferente']['code_postal'];
					echo $record['Structurereferente']['ville'];
				?>
			</td>
		</tr>
		<tr>
			<td class="wide noborder"><strong>N° poste : </strong></td>
			<td class="wide noborder">
				<?php echo $record['Referent']['numero_poste']; ?>
			</td>
		</tr>
		<tr>
			<td class="wide noborder"><strong>Courriel : </strong></td>
			<td class="wide noborder">
				<?php echo $record['Referent']['email']; ?>
			</td>
		</tr>
	</table>
</fieldset>
<?php endif;?>