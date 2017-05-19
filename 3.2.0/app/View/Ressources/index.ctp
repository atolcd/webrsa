<?php 
	App::uses('WebrsaAccess', 'Utility');
	WebrsaAccess::init($dossierMenu);
	$this->pageTitle = 'Ressources de la personne';
?>
<h1>Ressources</h1>

<?php if( empty( $ressources ) ):?>
	<p class="notice">aucune information relative aux ressources de cette personne.</p>
<?php endif;?>
	
<ul class="actionMenu">
	<?php
		echo '<li>'.$this->Xhtml->addLink(
			'Déclarer une ressource',
			array( 'controller' => 'ressources', 'action' => 'add', $personne_id ),
			WebrsaAccess::addIsEnabled('/ressources/add', $ajoutPossible)
		).' </li>';
	?>
</ul>

<?php if( !empty( $ressources ) ):?>
<table class="tooltips">
	<thead>
		<tr>
			<th>Percevez-vous des ressources ?</th>
			<th>Montant DTR RSA</th>
			<th>Date de début </th>
			<th>Date de fin</th>
			<th colspan="2" class="action">Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach( $ressources as $ressource ):?>
			<?php

				$title = implode( ' ', array(
					$ressource['Ressource']['topressnotnul'] ,
					$this->Locale->money( $ressource['Ressource']['avg'] ),
					$ressource['Ressource']['ddress'] ,
					$ressource['Ressource']['dfress'] ,
				));

				echo $this->Xhtml->tableCells(
					array(
						h( $ressource['Ressource']['topressnotnul']  ? 'Oui' : 'Non'),
						$this->Locale->money( $ressource['Ressource']['avg'] ),
						h( date_short( $ressource['Ressource']['ddress'] ) ),
						h( date_short( $ressource['Ressource']['dfress'] ) ),
						$this->Xhtml->viewLink(
							'Voir la ressource',
							array( 'controller' => 'ressources', 'action' => 'view', $ressource['Ressource']['id'] ),
							WebrsaAccess::isEnabled($ressource, '/ressources/view')
						),
						$this->Xhtml->editLink(
							'Éditer la ressource ',
							array( 'controller' => 'ressources', 'action' => 'edit', $ressource['Ressource']['id'] ),
							WebrsaAccess::isEnabled($ressource, '/ressources/edit')
						)
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);
			?>
		<?php endforeach;?>
	</tbody>
</table>
<?php  endif;?>