<?php
	if (Configure::read( 'nom_form_pdo_cg' ) == 'cg66'){
		$this->pageTitle = 'Décision PCG';
	}
	else{
		$this->pageTitle = 'PDO';
	}

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<h1><?php echo $this->pageTitle;?></h1>
<?php echo $this->element( 'ancien_dossier' );?>
<h2>Détails PDO</h2>

<?php if( $this->Permissions->checkDossier( 'propospdos', 'add', $dossierMenu ) ):?>
	<ul class="actionMenu">
		<?php
			echo '<li>'.$this->Xhtml->addLink(
				'Ajouter un dossier',
				array( 'controller' => 'propospdos', 'action' => 'add', $personne_id )
			).' </li>';
		?>
	</ul>
<?php endif;?>
<?php if( empty( $pdos ) ):?>
	<p class="notice">Cette personne ne possède pas encore de Proposition de Décision d'Opportunité.</p>
<?php endif;?>

<?php if( !empty( $pdos ) ):?>
	<table class="tooltips">
		<thead>
			<tr>
				<th>Type de PDO</th>
				<th>Décision du Conseil Général</th>
				<th>Motif de la décision</th>
				<th>Date de la décision CG</th>
				<th>Commentaire PDO</th>
				<th colspan="4" class="action">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach( $pdos as $pdo ):?>
				<?php
					$authPrintcourrier = false;
					$modeleodt = Set::classicExtract( $pdo, 'Decisionpdo.modeleodt' );
					if( !empty( $modeleodt ) ) {
						$authPrintcourrier = true;
					}
					else{
						$authPrintcourrier = false;
					}

					echo $this->Xhtml->tableCells(
						array(
							h( Set::enum( Set::classicExtract( $pdo, 'Propopdo.typepdo_id' ), $typepdo ) ),
							h( Set::enum( Set::classicExtract( $pdo, 'Decisionpropopdo.decisionpdo_id' ), $decisionpdo ) ),
							h( Set::enum( Set::classicExtract( $pdo, 'Propopdo.motifpdo' ), $motifpdo ) ),
							h( date_short( Set::classicExtract( $pdo, 'Decisionpropopdo.datedecisionpdo' ) ) ),
							h( Set::classicExtract( $pdo, 'Decisionpropopdo.commentairepdo' ) ),
							$this->Xhtml->viewLink(
								'Voir le dossier PDO',
								array( 'controller' => 'propospdos', 'action' => 'view', $pdo['Propopdo']['id']),
								$this->Permissions->checkDossier( 'propospdos', 'view', $dossierMenu )
							),
							$this->Xhtml->editLink(
								'Éditer le dossier PDO',
								array( 'controller' => 'propospdos', 'action' => 'edit', $pdo['Propopdo']['id'] ),
								$this->Permissions->checkDossier( 'propospdos', 'edit', $dossierMenu )
							),
							$this->Xhtml->printLink(
								'Imprimer',
								array( 'controller' => 'propospdos', 'action' => 'printCourrier', $pdo['Propopdo']['id'] ),
								( $authPrintcourrier && $this->Permissions->checkDossier( 'propospdos', 'printCourrier', $dossierMenu ) )
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