<?php $this->pageTitle = 'Paramétrage des Services instructeurs';?>
<?php echo $this->Xform->create( 'Serviceinstructeur' );?>
<div>
	<h1><?php echo 'Visualisation de la table  ';?></h1>

	<?php if( $this->Permissions->check( 'servicesinstructeurs', 'add' ) ):?>
		<ul class="actionMenu">
			<?php
				echo '<li>'.$this->Xhtml->addLink(
					'Ajouter',
					array( 'controller' => 'servicesinstructeurs', 'action' => 'add' )
				).' </li>';
			?>
		</ul>
	<?php endif;?>

	<div>
		<h2>Table Service instructeur</h2>
		<table>
		<thead>
			<tr>
				<th>Nom du service</th>
				<th>N° de rue</th>
				<th>Type de voie</th>
				<th>Nom de rue</th>
				<th>Code INSEE</th>
				<th>Code postal</th>
				<th>Ville</th>
                <th>Adresse électronique</th>
				<th>N° dépt</th>
				<th>Type service</th>
				<th>N° commune</th>
				<th>N° agréement</th>
				<?php if( Configure::read( 'Recherche.qdFilters.Serviceinstructeur' ) ):?><th>Fragment SQL ?</th><?php endif;?>
				<th colspan="2" class="action">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach( $servicesinstructeurs as $serviceinstructeur ):?>
				<?php
					$values = array(
						h( $serviceinstructeur['Serviceinstructeur']['lib_service'] ),
						h( $serviceinstructeur['Serviceinstructeur']['num_rue'] ),
						h( isset( $typevoie[$serviceinstructeur['Serviceinstructeur']['type_voie']] ) ? $typevoie[$serviceinstructeur['Serviceinstructeur']['type_voie']] : null ),
						h( $serviceinstructeur['Serviceinstructeur']['nom_rue'] ),
						h( $serviceinstructeur['Serviceinstructeur']['code_insee'] ),
						h( $serviceinstructeur['Serviceinstructeur']['code_postal'] ),
						h( $serviceinstructeur['Serviceinstructeur']['ville'] ),
                        h( $serviceinstructeur['Serviceinstructeur']['email'] ),
						h( $serviceinstructeur['Serviceinstructeur']['numdepins'] ),
						h( isset( $typeserins[$serviceinstructeur['Serviceinstructeur']['typeserins']] ) ? $typeserins[$serviceinstructeur['Serviceinstructeur']['typeserins']] : null ),
						h( $serviceinstructeur['Serviceinstructeur']['numcomins'] ),
						h( $serviceinstructeur['Serviceinstructeur']['numagrins'] ),
					);

					if( Configure::read( 'Recherche.qdFilters.Serviceinstructeur' ) ) {
						$values[] = $this->Xhtml->boolean( $serviceinstructeur['Serviceinstructeur']['sqrecherche'] );
					}

					echo $this->Xhtml->tableCells(
						array_merge(
							$values,
							array(
								$this->Xhtml->editLink(
									'Éditer le service instructeur ',
									array( 'controller' => 'servicesinstructeurs', 'action' => 'edit', $serviceinstructeur['Serviceinstructeur']['id'] ),
									$this->Permissions->check( 'servicesinstructeurs', 'edit' )
								),
								$this->Xhtml->deleteLink(
									'Supprimer le service instructeur ',
									array( 'controller' => 'servicesinstructeurs', 'action' => 'delete', $serviceinstructeur['Serviceinstructeur']['id'] ),
									$this->Permissions->check( 'servicesinstructeurs', 'delete' ) && ( $serviceinstructeur['Serviceinstructeur']['nbUsers'] == 0 )
								)
							)
						),
						array( 'class' => 'odd' ),
						array( 'class' => 'even' )
					); ?>
			<?php endforeach;?>
			</tbody>
		</table>
</div>
</div>
	<div class="submit">
		<?php
			echo $this->Xform->submit( 'Retour', array( 'name' => 'Cancel', 'div' => false ) );
		?>
	</div>

<div class="clearer"><hr /></div>
<?php echo $this->Xform->end();?>