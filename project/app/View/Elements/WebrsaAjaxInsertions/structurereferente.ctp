<?php
	// On n'affiche pas le type d'orientation pour les APRE/ADRE
	$showTypeorient = 0 !== strpos( $this->request->params['controller'], 'apres' );
?>
<?php if( !empty( $record ) ):?>
<table class="wide noborder">
	<tr>
		<?php if( $showTypeorient ):?><td class="wide noborder"><strong>Type d'orientation</strong></td><?php endif;?>
		<td class="wide noborder"><strong>Adresse</strong></td>
	</tr>
	<tr>
		<?php if( $showTypeorient ):?><td class="wide noborder"><?php echo Hash::get( $record, 'Typeorient.lib_type_orient');?></td><?php endif;?>
		<td class="wide noborder"><?php echo Hash::get( $record, 'Structurereferente.num_voie').' '.Hash::get( $record, 'Structurereferente.type_voie').' '.Hash::get( $record, 'Structurereferente.nom_voie').'<br /> '.Hash::get( $record, 'Structurereferente.code_postal').' '.Hash::get( $record, 'Structurereferente.ville');?></td>
	</tr>
</table>
<?php endif;?>