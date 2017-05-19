<?php if( !empty( $actioncandidat ) ): ?>
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
		<?php if( isset( $referent ) && !empty( $referent ) ) :?>
			<tr>
				<td class="wide noborder"><strong>Correspondant de l'action : </strong></td>
				<td class="wide noborder">
					<?php
						echo Set::classicExtract( $referent, 'Referent.nom_complet' );
					?>
				</td>
			</tr>
		<?php endif;?>

		
		<tr>
			<td class="wide noborder"><strong>Fichiers liés: </strong></td>
			<td class="wide noborder">
				<?php
					echo $this->Fileuploader->results( Set::classicExtract( $actioncandidat, 'Fichiermodule' ) );
				?>
			</td>
		</tr>
        <tr>
			<td class="wide noborder"><strong>Email partenaire/prestataire : </strong></td>
			<td class="wide noborder"><?php echo Set::classicExtract( $actioncandidat, 'Actioncandidat.emailprestataire' ); ?></td>
		</tr>  
	</table>
</fieldset>
<script type="text/javascript">
	<!--//--><![CDATA[//><!--
	function setInputValue( input, value ) {
		input = $( input );
		if( ( input != undefined ) && ( $F( input ) == '' ) ) {
			$( input ).setValue( value );
		}
	}
	setInputValue( 'ActioncandidatPersonneLieurdvpartenaire', '<?php echo str_replace( "'", "\\'", Set::classicExtract( $actioncandidat, 'Contactpartenaire.Partenaire.libstruc' ) );?>' );
	setInputValue( 'ActioncandidatPersonnePersonnerdvpartenaire', '<?php echo str_replace( "'", "\\'", Set::classicExtract( $actioncandidat, 'Contactpartenaire.nom_candidat' ) );?>' );
	//--><!]]>
</script> 
<?php endif;?>