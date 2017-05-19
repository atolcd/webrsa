<?php 
	App::uses('WebrsaAccess', 'Utility');
	WebrsaAccess::init($dossierMenu);
	$this->pageTitle = 'Adresses du foyer';
?>
<h1><?php echo $this->pageTitle;?></h1>

<?php if( $this->Permissions->checkDossier( 'adressesfoyers', 'add', $dossierMenu ) ):?>
	<ul class="actionMenu">
		<?php
			echo '<li>'.$this->Xhtml->addLink(
				'Ajouter une adresse au foyer',
				array( 'controller' => 'adressesfoyers', 'action' => 'add', $foyer_id ),
				WebrsaAccess::addIsEnabled('/adressesfoyers/add', $ajoutPossible)
			).' </li>';
		?>
	</ul>
<?php endif;?>

<?php if( !empty( $adresses ) ):?>
	<table class="tooltips">
		<thead>
			<tr>
				<th><?php echo __d( 'adressefoyer', 'Adressefoyer.rgadr' );?></th>
				<th>Adresse</th>
				<th>Localité</th>
				<th colspan="2" class="action">Actions</th>
				<th class="innerTableHeader">Informations complémentaires</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach( $adresses as $index => $adresse ):?>
				<?php
					$title = implode( ' ', array( $adresse['Adresse']['numvoie'], $adresse['Adresse']['libtypevoie'], $adresse['Adresse']['nomvoie'] ) );

					$innerTable = '<table id="innerTable'.$index.'" class="innerTable">
						<tbody>
							<tr>
								<th>'.__d( 'adressefoyer', 'Adressefoyer.dtemm' ).'</th>
								<td>'.h( date_short( $adresse['Adressefoyer']['dtemm'] ) ).'</td>
							</tr>
							<tr>
								<th>'.__d( 'adressefoyer', 'Adressefoyer.typeadr' ).'</th>
								<td>'.h( @$typeadr[$adresse['Adressefoyer']['typeadr']] ).'</td>
							</tr>
							<tr>
								<th>Pays</th>
								<td>'.h( $pays[$adresse['Adresse']['pays']] ).'</td>
							</tr>
						</tbody>
					</table>';
					echo $this->Xhtml->tableCells(
						array(
							h( !empty( $adresse['Adressefoyer']['rgadr'] ) ? $rgadr[$adresse['Adressefoyer']['rgadr']] : null ),
							h( implode( ' ', array( $adresse['Adresse']['numvoie'], $adresse['Adresse']['libtypevoie'], $adresse['Adresse']['nomvoie'] ) ) ),
							h( implode( ' ', array( $adresse['Adresse']['codepos'], $adresse['Adresse']['nomcom'] ) ) ),
							$this->Xhtml->viewLink(
								'Voir l\'adresse « '.$title.' »',
								array( 'controller' => 'adressesfoyers', 'action' => 'view', $adresse['Adressefoyer']['id'] ),
								WebrsaAccess::isEnabled($adresse, '/adressesfoyers/view')
							),
							$this->Xhtml->editLink(
								'Éditer l\'adresse « '.$title.' »',
								array( 'controller' => 'adressesfoyers', 'action' => 'edit', $adresse['Adressefoyer']['id'] ),
								WebrsaAccess::isEnabled($adresse, '/adressesfoyers/edit')
							),
							array( $innerTable, array( 'class' => 'innerTableCell' ) ),
						),
						array( 'class' => 'odd', 'id' => 'innerTableTrigger'.$index ),
						array( 'class' => 'even', 'id' => 'innerTableTrigger'.$index )
					);
				?>
			<?php endforeach;?>
		</tbody>
	</table>
<?php else:?>
	<p class="notice">Ce foyer ne possède actuellement aucune adresse.</p>
<?php endif;?>