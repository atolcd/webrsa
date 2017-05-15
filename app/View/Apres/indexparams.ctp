<h1><?php echo $this->pageTitle = 'Paramétrages des APREs';?></h1>

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
					h( 'Paramètres financiers pour la gestion de l\'APRE' ),
					$this->Xhtml->viewLink(
						'Voir la table',
						array( 'controller' => 'parametresfinanciers', 'action' => 'index' )
					)
				),
				array( 'class' => 'odd' ),
				array( 'class' => 'even' )
			);
			echo $this->Xhtml->tableCells(
				array(
					h( 'Participants comités APRE' ),
					$this->Xhtml->viewLink(
						'Voir la table',
						array( 'controller' => 'participantscomites', 'action' => 'index' )
					)
				),
				array( 'class' => 'odd' ),
				array( 'class' => 'even' )
			);
			echo $this->Xhtml->tableCells(
				array(
					h( 'Personnes chargées du suivi des Aides APREs' ),
					$this->Xhtml->viewLink(
						'Voir la table',
						array( 'controller' => 'suivisaidesapres', 'action' => 'index' )
					)
				),
				array( 'class' => 'odd' ),
				array( 'class' => 'even' )
			);
			echo $this->Xhtml->tableCells(
				array(
					h( 'Tiers prestataires de l\'APRE' ),
					$this->Xhtml->viewLink(
						'Voir la table',
						array( 'controller' => 'tiersprestatairesapres', 'action' => 'index' )
					)
				),
				array( 'class' => 'odd' ),
				array( 'class' => 'even' )
			);
			echo $this->Xhtml->tableCells(
				array(
					h( 'Types d\'aides liées au personne chargée du suivi de l\'APRE' ),
					$this->Xhtml->viewLink(
						'Voir la table',
						array( 'controller' => 'suivisaidesaprestypesaides', 'action' => 'index' )
					)
				),
				array( 'class' => 'odd' ),
				array( 'class' => 'even' )
			);

		?>
	</tbody>
</table>

<?php
	echo $this->Default->button(
		'back',
		array( 'controller' => 'parametrages', 'action' => 'index' ),
		array(
			'id' => 'Back'
		)
	);
?>