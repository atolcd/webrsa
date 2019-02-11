<?php $this->pageTitle = 'Dossier de la personne';?>
<h1><?php echo 'Visualisation des ressources  ';?></h1>

<?php if( empty( $ressource ) ):?>
	<p class="notice">Cette personne ne possède pas encore de ressources.</p>

	<?php if( $this->Permissions->checkDossier( 'ressources', 'add', $dossierMenu ) ):?>
		<ul class="actionMenu">
			<?php
				echo '<li>'.$this->Xhtml->addLink(
					'Déclarer des ressources',
					array( 'controller' => 'ressources', 'action' => 'add', $personne_id )
				).' </li>';
			?>
		</ul>
	<?php endif;?>

<?php else:?>

<div id="ficheRessource">
		<h2>Généralités concernant les ressources du trimestre</h2>

<table>
	<tbody>
		<tr class="odd">
			<th ><?php echo __d( 'ressource', 'Ressource.topressnotnul' );?></th>
			<td><?php echo $ressource['Ressource']['topressnotnul']? 'Oui' : 'Non' ;?></td>
		</tr>
		<tr class="even">
			<th><?php echo __d( 'ressource', 'Ressource.mtpersressmenrsa' );?></th>
			<td><?php echo $this->Locale->money( $ressource['Ressource']['avg'] );?></td>
		</tr>
		<tr class="odd">
			<th><?php echo __d( 'ressource', 'Ressource.ddress' );?></th>
			<td><?php echo date_short( $ressource['Ressource']['ddress'] );?></td>
		</tr>
		<tr class="even">
			<th><?php echo __d( 'ressource', 'Ressource.dfress' );?></th>
			<td><?php echo date_short( $ressource['Ressource']['dfress'] );?></td>
		</tr>
	</tbody>
</table>
		<h2>Ressources mensuelles</h2>
			<?php if( empty( $ressource['Ressourcemensuelle'] ) ):?>
				<p class="notice">Aucune ressource mensuelle déclarée</p>
			<?php else:?>
			<h3>Généralités des ressources mensuelles</h3>
			<table>
				<thead>
					<tr>
						<th><abbr title="<?php echo __d( 'ressource', 'Ressource.moisress' );?>">Mois</abbr></th>
						<th><abbr title="<?php echo __d( 'ressource', 'Ressource.nbheumentra' );?>">Nb heures</abbr></th>
						<th><abbr title="<?php echo __( 'mtabaneu' );?>">Montant A/N</abbr></th>
						<th><abbr title="<?php echo __d( 'ressource', 'Ressource.natress' );?>">Nature</abbr></th>
						<th><abbr title="<?php echo __d( 'ressource', 'Ressource.mtnatressmen' );?>">Montant ressource</abbr></th>
						<th><abbr title="<?php echo __d( 'ressource', 'Ressource.abaneu' );?>">A/N</abbr></th>
						<th><abbr title="<?php echo __d( 'ressource', 'Ressource.dfpercress' );?>">Date fin</abbr></th>
						<th><abbr title="<?php echo __d( 'ressource', 'Ressource.topprevsubsress' );?>">Revenus de substitution?</abbr></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach( $ressource['Ressourcemensuelle'] as $ressourcemensuelle ):?>
						<?php
							echo $this->Xhtml->tableCells(
								array(
									h( strftime( '%B %Y', strtotime( $ressourcemensuelle['moisress'] ) ) ),
									h( $ressourcemensuelle['nbheumentra'] ),
									h( $ressourcemensuelle['mtabaneu'] ),
									'',
									'',
									'',
									'',
									''
								),
								array( 'class' => 'odd parent' ),
								array( 'class' => 'even parent' )
							);

							foreach( $ressourcemensuelle['Detailressourcemensuelle'] as $detailressourcemensuelle){
								$indexNatress = trim( $detailressourcemensuelle['natress'] );
								echo $this->Xhtml->tableCells(
									array(
										'',
										'',
										'',
										h( ( !empty( $indexNatress ) ) ? $natress[$indexNatress] : null ),
										$this->Locale->money( $detailressourcemensuelle['mtnatressmen'] ),
										h( Set::enum( $detailressourcemensuelle['abaneu'], $abaneu ) ),
										h( date_short( $detailressourcemensuelle['dfpercress'] ) ),
										h( $detailressourcemensuelle['topprevsubsress']? 'Oui' : 'Non' )
									),
									array( 'class' => 'odd' ),
									array( 'class' => 'even' )
								);
							}
						?>
					<?php endforeach;?>
				</tbody>
			</table>
			<?php endif;?>

</div>
<?php endif;?>
<?php
	echo $this->Default->button(
		'back',
		array(
			'controller' => 'ressources',
			'action'     => 'index',
			$personne_id
		),
		array(
			'id' => 'Back'
		)
	);
?>