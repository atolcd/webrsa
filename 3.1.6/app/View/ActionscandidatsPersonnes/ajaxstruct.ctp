<?php if( !empty( $referent ) ):?>
	<table class="wide noborder">
		<tr>
			<td class="wide noborder"><strong>Organisme prescripteur</strong></td>
			<td class="wide noborder"><strong>Adresse</strong></td>
			<td class="wide noborder"><strong>Fonction</strong></td>
		</tr>
		<tr>
			<td class="wide noborder">
				<?php
					echo Set::classicExtract( $structs, 'Structurereferente.lib_struc' );
				?>
			</td>
			<td class="wide noborder">
				<?php
					echo Set::classicExtract( $structs, 'Structurereferente.num_voie' ).' '.Set::enum( Set::classicExtract( $structs, 'Structurereferente.type_voie' ), $typevoie ).' '.Set::classicExtract( $structs, 'Structurereferente.nom_voie' ).' <br /> '.Set::classicExtract( $structs, 'Structurereferente.code_postal' ).' '.Set::classicExtract( $structs, 'Structurereferente.ville' );
				?>
			</td>
			<td class="wide noborder">
				<?php
					echo Set::classicExtract( $referent, 'Referent.fonction' ).'<br /> N° tel: '.Set::classicExtract( $referent, 'Referent.numero_poste' );
				?>
			</td>
		</tr>

	</table>
<?php endif;?>