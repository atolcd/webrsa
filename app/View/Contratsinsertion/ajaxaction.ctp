<?php if( !empty( $actioncandidat ) && !empty( $actioncandidat['Contactpartenaire']['id'] ) ): ?>
<fieldset>
	<legend>Partenaire / Prestataire</legend>
	<table class="wide noborder">
		<tr>
			<td class="wide noborder" style="width: 400px"><strong>Nom Partenaire / Prestataire : </strong></td>
			<td class="wide noborder">
				<?php echo Set::classicExtract( $actioncandidat, 'Contactpartenaire.Partenaire.libstruc' ); ?>
			</td>
		</tr>
		<tr>
			<td class="wide noborder"><strong>Nom du contact : </strong></td>
			<td class="wide noborder"><?php echo Set::classicExtract( $actioncandidat, 'Contactpartenaire.nom_candidat' ); ?></td>
		</tr>  
		<tr>
			<td class="wide noborder"><strong>Tél. : </strong></td>
			<td class="wide noborder"><?php echo Set::classicExtract( $actioncandidat, 'Contactpartenaire.numtel' ); ?></td>
		</tr>  
		<tr>
			<td class="wide noborder"><strong>Fax : </strong></td>
			<td class="wide noborder"><?php echo Set::classicExtract( $actioncandidat, 'Contactpartenaire.numfax' ); ?></td>
		</tr>
		<tr>
			<td class="wide noborder"><strong>Email : </strong></td>
			<td class="wide noborder"><?php echo Set::classicExtract( $actioncandidat, 'Contactpartenaire.Partenaire.email' ); ?></td>
		</tr>
		<tr>
			<td class="wide noborder"><strong>Code action : </strong></td>
			<td class="wide noborder"><?php echo Set::classicExtract( $actioncandidat, 'Actioncandidat.codeaction' ); ?></td>
		</tr>  
		<tr>
			<td class="wide noborder"><strong>Correspondant de l'action : </strong></td>
			<td class="wide noborder">
				<?php
					echo Set::classicExtract( $actioncandidat, 'Referent.nom_complet' );
				?>
			</td>
		</tr>

		
		<tr>
			<td class="wide noborder"><strong>Fichiers liés: </strong></td>
			<td class="wide noborder">
				<?php
					echo $this->Fileuploader->results( Set::classicExtract( $actioncandidat, 'Fichiermodule' ) );
				?>
			</td>
		</tr>
	</table>
</fieldset>
<?php endif;?>