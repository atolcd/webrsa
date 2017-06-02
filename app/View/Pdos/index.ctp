<?php $this->pageTitle = 'Paramétrages des PDOs';?>
<h1>Paramétrage des PDOs</h1>

<?php echo $this->Form->create( 'NouvellesPDOs', array() );?>
	<table >
		<thead>
			<tr>
				<th>Nom de Table</th>
				<th colspan="2" class="action">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php

				echo $this->Xhtml->tableCells(
					array(
						h( 'Décision PDOs' ),
						$this->Xhtml->viewLink(
							'Voir la table',
							array( 'controller' => 'decisionspdos', 'action' => 'index' ),
							$this->Permissions->check( 'decisionspdos', 'index' )
						)
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);

				echo $this->Xhtml->tableCells(
					array(
						h( 'Description pour traitements PDOs' ),
						$this->Xhtml->viewLink(
							'Voir la table',
							array( 'controller' => 'descriptionspdos', 'action' => 'index' ),
							$this->Permissions->check( 'descriptionspdos', 'index' )
						)
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);
				/*echo $this->Xhtml->tableCells(
					array(
						h( 'Liste des courriers pour un traitement de PDOs' ),
						$this->Xhtml->viewLink(
							'Voir la table',
							array( 'controller' => 'courrierspdos', 'action' => 'index' )
						)
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);*/
                                
				echo $this->Xhtml->tableCells(
					array(
						h( 'Origine PDOs' ),
						$this->Xhtml->viewLink(
							'Voir la table',
							array( 'controller' => 'originespdos', 'action' => 'index' ),
							$this->Permissions->check( 'originespdos', 'index' )
						)
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);

				if ( Configure::read( 'Cg.departement' ) == 66 ) {
					echo $this->Xhtml->tableCells(
						array(
							h( 'Module courriers lié aux traitements PCGs' ),
							$this->Xhtml->viewLink(
								'Voir la table',
								array( 'controller' => 'courrierspcgs66', 'action' => 'index' ),
								$this->Permissions->check( 'courrierspcgs66', 'index' )
							)
						),
						array( 'class' => 'odd' ),
						array( 'class' => 'even' )
					);
					echo $this->Xhtml->tableCells(
						array(
							h( 'Organismes auxquels seront transmis les dossiers PCGs' ),
							$this->Xhtml->viewLink(
								'Voir la table',
								array( 'controller' => 'orgstransmisdossierspcgs66', 'action' => 'index' ),
								$this->Permissions->check( 'orgstransmisdossierspcgs66', 'index' )
							)
						),
						array( 'class' => 'odd' ),
						array( 'class' => 'even' )
					);
					echo $this->Xhtml->tableCells(
						array(
							h( 'Paramétrage pour les décisions de dossiers PCG' ),
							$this->Xhtml->viewLink(
								'Voir la table',
								array( 'controller' => 'decisionsdossierspcgs66', 'action' => 'index' ),
								$this->Permissions->check( 'decisionsdossierspcgs66', 'index' )
							)
						),
						array( 'class' => 'odd' ),
						array( 'class' => 'even' )
					);
                    echo $this->Xhtml->tableCells(
						array(
							h( 'Pôles chargés des dossiers' ),
							$this->Xhtml->viewLink(
								'Voir la table',
								array( 'controller' => 'polesdossierspcgs66', 'action' => 'index' ),
								$this->Permissions->check( 'polesdossierspcgs66', 'index' )
							)
						),
						array( 'class' => 'odd' ),
						array( 'class' => 'even' )
					);
				}
				
				echo $this->Xhtml->tableCells(
					array(
						h( 'Situation PDOs' ),
						$this->Xhtml->viewLink(
							'Voir la table',
							array( 'controller' => 'situationspdos', 'action' => 'index' ),
							$this->Permissions->check( 'situationspdos', 'index' )
						)
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);
				echo $this->Xhtml->tableCells(
					array(
						h( 'Statut PDOs' ),
						$this->Xhtml->viewLink(
							'Voir la table',
							array( 'controller' => 'statutspdos', 'action' => 'index' ),
							$this->Permissions->check( 'statutspdos', 'index' )
						)
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);
				echo $this->Xhtml->tableCells(
					array(
						h( 'Type de notification' ),
						$this->Xhtml->viewLink(
							'Voir la table',
							array( 'controller' => 'typesnotifspdos', 'action' => 'index' ),
							$this->Permissions->check( 'typesnotifspdos', 'index' )
						)
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);
				if ( Configure::read( 'Cg.departement' ) != 66 ) {
					echo $this->Xhtml->tableCells(
						array(
							h( 'Types de traitements PDOs' ),
							$this->Xhtml->viewLink(
								'Voir la table',
								array( 'controller' => 'traitementstypespdos', 'action' => 'index' ),
								$this->Permissions->check( 'traitementstypespdos', 'index' )
							)
						),
						array( 'class' => 'odd' ),
						array( 'class' => 'even' )
					);
				}
				echo $this->Xhtml->tableCells(
					array(
						h( 'Type de PDOs' ),
						$this->Xhtml->viewLink(
							'Voir la table',
							array( 'controller' => 'typespdos', 'action' => 'index' ),
							$this->Permissions->check( 'typespdos', 'index' )
						)
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);
				
				if ( Configure::read( 'Cg.departement' ) == 66 ) {
					echo $this->Xhtml->tableCells(
						array(
							h( 'Types de RSA' ),
							$this->Xhtml->viewLink(
								'Voir la table',
								array( 'controller' => 'typesrsapcgs66', 'action' => 'index' ),
								$this->Permissions->check( 'typesrsapcgs66', 'index' )
							)
						),
						array( 'class' => 'odd' ),
						array( 'class' => 'even' )
					);
				}
				
				echo $this->Xhtml->tableCells(
					array(
						h( 'Zones supplémentaires pour les courriers de traitements PDOs' ),
						$this->Xhtml->viewLink(
							'Voir la table',
							array( 'controller' => 'textareascourrierspdos', 'action' => 'index' ),
							( $this->Permissions->check( 'textareascourrierspdos', 'index' ) && ( $compteurs['Courrierpdo'] > 0 ) )
						)
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);

			?>
		</tbody>
	</table>
	<div class="submit">
		<?php
			echo $this->Form->submit( 'Retour', array( 'name' => 'Cancel', 'div' => false ) );
		?>
	</div>
<?php echo $this->Form->end();?>