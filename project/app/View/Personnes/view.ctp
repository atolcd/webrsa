<?php
    if( Configure::read( 'debug' ) > 0 ) {
        echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
    }

	$title = implode(
		' ',
		array(
			$personne['Personne']['qual'],
			$personne['Personne']['nom'],
			( !empty( $personne['Personne']['nomnaiss'] ) ? '( née '.$personne['Personne']['nomnaiss'].' )' : null ),
			$personne['Personne']['prenom'],
			$personne['Personne']['prenom2'],
			$personne['Personne']['prenom3']
		)
	);

	$this->pageTitle = 'Visualisation d\'une personne « '.$title.' »';

	App::uses('WebrsaAccess', 'Utility');
	WebrsaAccess::init($dossierMenu);

	function thead( $pct = 10 ) {
		return '<thead>
				<tr>
					<th colspan="2" style="width: '.$pct.'%;">Grossesse</th>
				</tr>
			</thead>';
	}
?>

<h1><?php echo 'Visualisation d\'une personne « '.$title.' »';?></h1>

<ul class="actions">
<?php
	echo '<li class="action">'.$this->Xhtml->link(
		'Modifier',
		array( 'controller' => 'personnes', 'action' => 'edit', $personne['Personne']['id'] ),
		array(
			'title' => 'Éditer la personne « '.$title.' »',
			'enabled' => WebrsaAccess::isEnabled($personne, '/Personnes/edit'),
			'class' => 'personnes edit link'
		)
	).' </li>';
	echo '<li class="action">'.$this->Xhtml->link(
		__m('coordonnees.voir'),
		array( 'controller' => 'modescontact', 'action' => 'index', $personne['Personne']['foyer_id'], $personne['Prestation']['rolepers'] ),
		array(
			'title' => 'Voir l\'historique des coordonnées de « '.$title.' »',
			'enabled' => WebrsaAccess::isEnabled($personne, '/Personnes/coordonnees'),
			'class' => 'personnes coordonnees infocontact link',
		)
	).' </li>';
?>
</ul>

<div id="fichePers">
	<table>
		<tbody>
			<tr class="even">
				<th><?php echo __d( 'prestation', 'Prestation.rolepers' );?></th>
				<td><?php echo isset( $rolepers[$personne['Prestation']['rolepers']] ) ? $rolepers[$personne['Prestation']['rolepers']] : null ;?></td>
			</tr>
			<tr class="odd">
				<th><?php echo __d( 'personne', 'Personne.qual' );?></th>
				<td><?php echo isset( $qual[$personne['Personne']['qual']] ) ? $qual[$personne['Personne']['qual']] : null ;?></td>
			</tr>
			<tr class="even">
				<th><?php echo __d( 'personne', 'Personne.nom' );?></th>
				<td><?php echo $personne['Personne']['nom'];?></td>
			</tr>
			<tr class="odd">
				<th><?php echo __d( 'personne', 'Personne.prenom' );?></th>
				<td><?php echo $personne['Personne']['prenom'];?></td>
			</tr>
			<tr class="even">
				<th><?php echo __d( 'personne', 'Personne.nomnai' );?></th>
				<td><?php echo $personne['Personne']['nomnai'];?></td>
			</tr>
			<tr class="odd">
				<th><?php echo __d( 'personne', 'Personne.prenom2' );?></th>
				<td><?php echo $personne['Personne']['prenom2'];?></td>
			</tr>
			<tr class="even">
				<th><?php echo __d( 'personne', 'Personne.prenom3' );?></th>
				<td><?php echo $personne['Personne']['prenom3'];?></td>
			</tr>
			<tr class="odd">
				<th><?php echo __d( 'personne', 'Personne.nomcomnai' );?></th>
				<td><?php echo $personne['Personne']['nomcomnai'];?></td>
			</tr>
			<tr class="even">
				<th><?php echo __d( 'personne', 'Personne.dtnai' );?></th>
				<td><?php echo date_short( $personne['Personne']['dtnai'] );?></td>
			</tr>
			<tr class="odd">
				<th><?php echo __d( 'personne', 'Personne.rgnai' );?></th>
				<td><?php echo $personne['Personne']['rgnai'];?></td>
			</tr>
			<tr class="even">
				<th><?php echo __d( 'personne', 'Personne.typedtnai' );?></th>
				<td><?php echo isset( $typedtnai[$personne['Personne']['typedtnai']] ) ? $typedtnai[$personne['Personne']['typedtnai']] : null;?></td>
			</tr>
			<tr class="odd">
				<th><?php echo __d( 'personne', 'Personne.nir' );?></th>
				<td><?php echo $personne['Personne']['nir'];?></td>
			</tr>
			<tr class="even">
				<th><?php echo __d( 'personne', 'Personne.topvalec' );?></th>
				<td><?php echo  $personne['Personne']['topvalec'] ? 'Oui' : 'Non' ;?></td>
			</tr>
			<tr class="odd">
				<th><?php echo __d( 'personne', 'Personne.sexe' );?></th>
				<td><?php echo @$sexe[$personne['Personne']['sexe']];?></td>
			</tr>
			<?php if( Configure::read( 'nom_form_ci_cg' ) == 'cg58'
						&& ($personne['Prestation']['rolepers'] === null || $rolepers[$personne['Prestation']['rolepers']] != 'Enfant') ):?>
				<tr class="even">
					<th><?php echo __d( 'foyer', 'Foyer.sitfam' );?></th>
					<td><?php echo  isset( $sitfam[$personne['Foyer']['sitfam']] ) ?  $sitfam[$personne['Foyer']['sitfam']] : null ;?></td>
				</tr>
			<?php endif;?>
			<tr class="odd">
				<th><?php echo __d( 'personne', 'Personne.numfixe' );?></th>
				<td><?php echo $personne['Personne']['numfixe'];?></td>
			</tr>
			<tr class="even">
				<th><?php echo __d( 'personne', 'Personne.numport' );?></th>
				<td><?php echo $personne['Personne']['numport'];?></td>
			</tr>
			<tr class="odd">
				<th><?php echo __d( 'personne', 'Personne.email' );?></th>
				<td><?php echo $personne['Personne']['email'];?></td>
			</tr>
		</tbody>
	</table>

	<table>
		<tbody>
			<tr class="even">
				<th><?php echo __d( 'personne', 'Personne.nati' );?></th>
				<td><?php echo isset( $nationalite[$personne['Personne']['nati']] ) ? $nationalite[$personne['Personne']['nati']] : null;?></td>
			</tr>
			<tr class="odd">
				<th><?php echo __d( 'personne', 'Personne.dtnati' );?></th>
				<td><?php echo date_short( isset( $personne['Personne']['dtnati'] ) ) ? date_short( $personne['Personne']['dtnati'] ) : null;?></td>
			</tr>
			<tr class="even">
				<th><?php echo __d( 'personne', 'Personne.pieecpres' );?></th>
				<td><?php echo isset( $pieecpres[$personne['Personne']['pieecpres']] ) ? $pieecpres[$personne['Personne']['pieecpres']] : null;?></td>
			</tr>
		</tbody>
	</table>
	<?php if (Hash::get($personne, 'Personne.sexe') == 2 && Hash::get($personne, 'Prestation.rolepers') !== 'ENF' && isset($personne['Grossesse'])):?>
		<table>
			<?php echo thead( 10 );?>
			<tbody>
				<tr class="odd">
					<th><?php echo __d( 'grossesse', 'Grossesse.ddgro' );?></th>
					<td><?php echo date_short( isset( $personne['Grossesse'][0]['ddgro'] ) ) ? date_short( $personne['Grossesse'][0]['ddgro'] ) : null;?></td>
				</tr>
				<tr class="even">
					<th><?php echo __d( 'grossesse', 'Grossesse.dfgro' );?></th>
					<td><?php echo date_short( isset( $personne['Grossesse'][0]['dfgro'] ) ) ? date_short( $personne['Grossesse'][0]['dfgro'] ) : null;?></td>
				</tr>
				<tr class="odd">
					<th><?php echo __d( 'grossesse', 'Grossesse.dtdeclgro' );?></th>
					<td><?php echo date_short( isset( $personne['Grossesse'][0]['dtdeclgro'] ) ) ? date_short( $personne['Grossesse'][0]['dtdeclgro'] ) : null;?></td>
				</tr>
				<tr class="even">
					<th><?php echo __d( 'grossesse', 'Grossesse.natfingro' );?></th>
                    <td><?php echo isset( $personne['Grossesse'][0]['natfingro'] ) ? Set::enum( $personne['Grossesse'][0]['natfingro'], $natfingro ) : null;?></td>
				</tr>
			</tbody>
		</table>
	<?php endif;?>
</div>