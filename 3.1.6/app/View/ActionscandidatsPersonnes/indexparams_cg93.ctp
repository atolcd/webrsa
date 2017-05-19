<h1><?php
		if( Configure::read( 'ActioncandidatPersonne.suffixe' ) == 'cg66' ){
			$typefiche = 'candidature';
		}
		else if( Configure::read( 'ActioncandidatPersonne.suffixe' ) == 'cg93' ){

			$typefiche = 'prescription';
		}
		echo $this->pageTitle = 'Paramétrages pour les fiches de '.$typefiche;
	?>
</h1>

<?php echo $this->Form->create( 'NouvellesFiches', array() );?>
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
						h( 'Actions d\'insertion pour fiches de '.$typefiche ),
						$this->Xhtml->viewLink(
							'Voir la table',
							array( 'controller' => 'actionscandidats', 'action' => 'index' ),
							( $this->Permissions->check( 'actionscandidats', 'index' ) && ( $compteurs['Contactpartenaire'] > 0 ) && ( $compteurs['Partenaire'] > 0 ) )
						)
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);
				echo $this->Xhtml->tableCells(
					array(
						h( 'Contacts des partenaires' ),
						$this->Xhtml->viewLink(
							'Voir la table',
							array( 'controller' => 'contactspartenaires', 'action' => 'index' ),
							( $this->Permissions->check( 'contactspartenaires', 'index' ) && $compteurs['Partenaire'] > 0 )
						)
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);
				echo $this->Xhtml->tableCells(
					array(
						h( 'Partenaires pour fiche de '.$typefiche ),
						$this->Xhtml->viewLink(
							'Voir la table',
							array( 'controller' => 'partenaires', 'action' => 'index' ),
							$this->Permissions->check( 'partenaires', 'index' )
						)
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);
				echo $this->Xhtml->tableCells(
					array(
						h( 'Motifs de sortie' ),
						$this->Xhtml->viewLink(
							'Voir la table',
							array( 'controller' => 'motifssortie', 'action' => 'index' ),
							$this->Permissions->check( 'motifssortie', 'index' )
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