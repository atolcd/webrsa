<?php $this->pageTitle = 'Paramétrage des décisions de PDO';?>
<div>
	<h1><?php echo 'Visualisation de la table  ';?></h1>

	<ul class="actionMenu">
		<?php
			echo '<li>'.$this->Xhtml->addLink(
				'Ajouter',
				array( 'controller' => 'decisionspdos', 'action' => 'add' ),
				$this->Permissions->check( 'decisionspdos', 'add' )
			).' </li>';
		?>
	</ul>
    <?php $pagination = $this->Xpaginator->paginationBlock( 'Decisionpdo', $this->passedArgs ); ?>
	
    <div>
		<h2>Table Décision de PDO</h2>
        <?php echo $pagination;?>
		<table>
		<thead>
			<tr>
				<th>Libellé</th>
				<th>Ce type clotûre-t-il le dossier ?</th>
				<?php if( Configure::read( 'Cg.departement' ) == 66  ) :?>
					<th>Cette décision est-elle liée à un CER Particulier ?</th>
				<?php endif;?>
				<?php if( Configure::read( 'Cg.departement' ) == 93  ) :?>
					<th>Modèle de document lié</th>
				<?php endif;?>
                <th>Actif ?</th>
				<th colspan="2" class="action">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach( $decisionspdos as $decisionpdo ):
			
				if( Configure::read( 'Cg.departement' ) == 66  ) {
					$arrayCells = array(
						h( $decisionpdo['Decisionpdo']['libelle'] ),
						( $decisionpdo['Decisionpdo']['clos'] == 'N' ) ? 'Non' : 'Oui',
						( $decisionpdo['Decisionpdo']['cerparticulier'] == 'N' ) ? 'Non' : 'Oui',
						( $decisionpdo['Decisionpdo']['isactif'] == '0' ) ? 'Non' : 'Oui',
						$this->Xhtml->editLink(
							'Éditer la décision de PDO ',
							array( 'controller' => 'decisionspdos', 'action' => 'edit', $decisionpdo['Decisionpdo']['id'] ),
							$this->Permissions->check( 'decisionspdos', 'edit' )
						),
						$this->Xhtml->deleteLink(
							'Supprimer la décision de PDO ',
							array( 'controller' => 'decisionspdos', 'action' => 'delete', $decisionpdo['Decisionpdo']['id'] ),
							$this->Permissions->check( 'decisionspdos', 'delete' ) && ( $decisionpdo['Decisionpdo']['occurences'] == 0 )
						)
					);
				}
				else{
					$arrayCells = array(
						h( $decisionpdo['Decisionpdo']['libelle'] ),
						( $decisionpdo['Decisionpdo']['clos'] == 'N' ) ? 'Non' : 'Oui',
						h( $decisionpdo['Decisionpdo']['modeleodt'] ),
                        ( $decisionpdo['Decisionpdo']['isactif'] == '0' ) ? 'Non' : 'Oui',
						$this->Xhtml->editLink(
							'Éditer la décision de PDO ',
							array( 'controller' => 'decisionspdos', 'action' => 'edit', $decisionpdo['Decisionpdo']['id'] ),
							$this->Permissions->check( 'decisionspdos', 'edit' )
						),
						$this->Xhtml->deleteLink(
							'Supprimer la décision de PDO ',
							array( 'controller' => 'decisionspdos', 'action' => 'delete', $decisionpdo['Decisionpdo']['id'] ),
							$this->Permissions->check( 'decisionspdos', 'delete' ) && ( $decisionpdo['Decisionpdo']['occurences'] == 0 )
						)
					);
				}
			

				
				echo $this->Xhtml->tableCells(
					$arrayCells,
					array( 'class' => 'odd', 'id' => 'innerTableTrigger' ),
					array( 'class' => 'even', 'id' => 'innerTableTrigger' )
				);
			endforeach;?>
		</tbody>
		</table>
        <?php echo $pagination;?>
        
</div>
    <?php
        echo '<div class="aere">';
		echo $this->Default->button(
			'back',
			array(
				'controller' => 'pdos',
				'action'     => 'index'
			),
			array(
				'id' => 'Back'
			)
		);
		echo '</div>';
    ?>