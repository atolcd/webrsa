<h1><?php echo $this->pageTitle = 'Webrsa indisponible hors plage horaire et le week-end';?></h1>
<p>Veuillez vous connecter dans la plage horaire</p>

<table>
	<tbody>
		<tr>
			<th>Date de début de plage</th>
			<td><?php echo '<b>'.date('H:i:s',$error->params['plagehoraire']['debut'] ).'</b>';?></td>
		</tr>
		<tr>
			<th>Date de fin de plage</th>
			<td><?php echo '<b>'.date('H:i:s',$error->params['plagehoraire']['fin'] ).'</b>';?></td>
		</tr>
	</tbody>
</table>

<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->element( 'exception_stack_trace' );
	}
?>