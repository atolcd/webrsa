<?php $this->pageTitle = 'Paramétrage du module rendez-vous';?>
<h1><?php echo $this->pageTitle;?></h1>

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
					h( 'Objets du rendez-vous' ),
					$this->Xhtml->viewLink(
						'Voir la table',
						array( 'controller' => 'typesrdv', 'action' => 'index' ),
						$this->Permissions->check( 'typesrdv', 'index' )
					)
				),
				array( 'class' => 'odd' ),
				array( 'class' => 'even' )
			);
			echo $this->Xhtml->tableCells(
				array(
					h( 'Statut des RDVs' ),
					$this->Xhtml->viewLink(
						'Voir la table',
						array( 'controller' => 'statutsrdvs', 'action' => 'index' ),
						$this->Permissions->check( 'statutsrdvs', 'index' )
					)
				),
				array( 'class' => 'odd' ),
				array( 'class' => 'even' )
			);
			
            if( Configure::read( 'Rendezvous.useThematique' ) ) {
                echo $this->Xhtml->tableCells(
                    array(
                        h( 'Thématiques des RDVs' ),
                        $this->Xhtml->viewLink(
                            'Voir la table',
                            array( 'controller' => 'thematiquesrdvs', 'action' => 'index' ),
                            $this->Permissions->check( 'thematiquesrdvs', 'index' )
                        )
                    ),
                    array( 'class' => 'odd' ),
                    array( 'class' => 'even' )
                );
            }
			if( Configure::read( 'Cg.departement' ) == 58 ){
				echo $this->Xhtml->tableCells(
					array(
						h( 'Passage en EP des RDVs' ),
						$this->Xhtml->viewLink(
							'Voir la table',
							array( 'controller' => 'statutsrdvs_typesrdv', 'action' => 'index' ),
							$this->Permissions->check( 'statutsrdvs_typesrdv', 'index' )
						)
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);
			}
		?>
	</tbody>
</table>
<?php
	echo $this->Default3->actions(
		array(
			"/Parametrages/index" => array(
				'text' => 'Retour',
				'class' => 'back',
				'disabled' => !$this->Permissions->check( 'Parametrages', 'index' )
			),
		)
	);
?>
