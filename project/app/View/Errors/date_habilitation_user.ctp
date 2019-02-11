<h1><?php echo $this->pageTitle = 'Erreur:  mauvaises dates d\'habilitation de l\'utilisateur';?></h1>
<p>Votre période d'habilitation n'est pas valide.</p>
<p>Veuillez vous reconnecter ultérieurement</p>

<table>
	<tbody>
		<tr>
			<th>Date de début d'habilitation</th>
			<td><?php echo '<b>'.date_short( $error->params['habilitations']['date_deb_hab'] ).'</b>';?></td>
		</tr>
		<tr>
			<th>Date de fin d'habilitation</th>
			<td><?php echo '<b>'.date_short( $error->params['habilitations']['date_fin_hab'] ).'</b>';?></td>
		</tr>
	</tbody>
</table>
<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->element( 'exception_stack_trace' );
	}
?>