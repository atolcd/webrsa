<?php $this->pageTitle = 'Paramétrage des Types d\'orientation';?>
<?php echo $this->Xform->create( 'Typeorient' );?>
<div>
	<h1><?php echo 'Visualisation de la table  ';?></h1>

	<ul class="actionMenu">
		<?php
			echo '<li>'.$this->Xhtml->addLink(
				'Ajouter',
				array( 'controller' => 'typesorients', 'action' => 'add' ),
				$this->Permissions->check( 'typesorients', 'add' )
			).' </li>';
		?>
	</ul>
	<div>
		<h2>Table Types d'orientation</h2>
		<table>
		<thead>
			<tr>
				<th>ID</th>
				<th>Type d'orientation</th>
				<th>Parent</th>
				<th>Modèle de notification</th>
				<th>Modèle de notification pour cohorte</th>
				<th>Actif</th>
				<th colspan="2" class="action">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach( $typesorients as $typeorient ):?>
				<?php
					echo $this->Xhtml->tableCells(
							array(
								h( $typeorient['Typeorient']['id'] ),
								h( $typeorient['Typeorient']['lib_type_orient'] ),
								h( $typeorient['Typeorient']['parentid'] ),
								h( $typeorient['Typeorient']['modele_notif'] ),
								h( $typeorient['Typeorient']['modele_notif_cohorte'] ),
								h( Set::enum( $typeorient['Typeorient']['actif'], $options['actif'] ) ),
								$this->Xhtml->editLink(
									'Éditer le type d\'orientation',
									array( 'controller' => 'typesorients', 'action' => 'edit', $typeorient['Typeorient']['id'] ),
									$this->Permissions->check( 'typesorients', 'edit' )
								),
								$this->Xhtml->deleteLink(
									'Supprimer le type d\'orientation',
									array( 'controller' => 'typesorients', 'action' => 'delete', $typeorient['Typeorient']['id'] ),
									( $this->Permissions->check( 'typesorients', 'delete' ) && !( $typeorient['Typeorient']['has_linkedrecords'] ) )
								)
							),
							array( 'class' => 'odd' ),
							array( 'class' => 'even' )
						);
				?>
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