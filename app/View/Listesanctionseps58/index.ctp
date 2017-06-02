<h1><?php echo $this->pageTitle = 'Liste des sanctions'; ?></h1>

	<ul class="actionMenu">
		<?php
			echo '<li>'.$this->Xhtml->addLink(
				'Ajouter',
				array( 'controller' => 'listesanctionseps58', 'action' => 'add' )
			).' </li>';
		?>
	</ul>

	<?php if ( $sanctionsValides == false ) { ?>
		<p class="error">Attention il y a une erreur dans vos sanctions. Merci de la corriger pour que les EPs fonctionnent correctement.</p>
	<?php } ?>

	<?php if ( empty( $sanctions ) ) { ?>
		<p class="notice">Aucune sanction n'a encore été enregistrée.</p>
	<?php }
	else { ?>
		<table><thead>

		<?php
			echo $this->Xhtml->tag(
				'tr',
				$this->Xhtml->tag(
					'th',
					__d( 'listesanctionep58', 'Listesanctionep58.rang' )
				).
				$this->Xhtml->tag(
					'th',
					__d( 'listesanctionep58', 'Listesanctionep58.sanction' )
				).
				$this->Xhtml->tag(
					'th',
					__d( 'listesanctionep58', 'Listesanctionep58.duree' )
				).
				$this->Xhtml->tag(
					'th',
					'Actions',
					array( 'colspan' => 2 )
				)
			);
		?>
		</thead><tbody>

		<?php
			foreach( $sanctions as $sanction ) {
				echo $this->Xhtml->tag(
					'tr',
					$this->Xhtml->tag(
						'td',
						$sanction['Listesanctionep58']['rang']
					).
					$this->Xhtml->tag(
						'td',
						$sanction['Listesanctionep58']['sanction']
					).
					$this->Xhtml->tag(
						'td',
						$sanction['Listesanctionep58']['duree']
					).
					$this->Xhtml->tag(
						'td',
						$this->Xhtml->editLink( 'Modifier', array( 'controller' => 'listesanctionseps58', 'action' => 'edit', $sanction['Listesanctionep58']['id'] ), true )
					).
					$this->Xhtml->tag(
						'td',
						$this->Xhtml->deleteLink( 'Supprimer', array( 'controller' => 'listesanctionseps58', 'action' => 'delete', $sanction['Listesanctionep58']['id'] ), true )
					)
				);
			}
		?>
		</tbody></table>
	<?php }
	echo $this->Default->button(
		'back',
		array(
			'controller' => 'parametrages',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);
	?>