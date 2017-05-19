<?php $this->pageTitle = 'Paramétrage des Types de contrat';?>
<?php echo $this->Xform->create( 'Typocontrat' );?>
<div>
	<h1><?php echo 'Visualisation de la table  ';?></h1>

	<ul class="actionMenu">
		<?php
			echo '<li>'.$this->Xhtml->addLink(
				'Ajouter',
				array( 'controller' => 'typoscontrats', 'action' => 'add' )
			).' </li>';
		?>
	</ul>
	<div>
		<h2>Table Types de Contrat d'insertion</h2>
		<table>
		<thead>
			<tr>
				<th>Libellé du type de contrat d'insertion</th>
				<th colspan="2" class="action">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach( $typoscontrats as $typocontrat ):?>
				<?php echo $this->Xhtml->tableCells(
							array(
								h( $typocontrat['Typocontrat']['lib_typo'] ),
								$this->Xhtml->editLink(
									'Éditer le type de contrat d\'insertion ',
									array( 'controller' => 'typoscontrats', 'action' => 'edit', $typocontrat['Typocontrat']['id'] )
								),
								$this->Xhtml->deleteLink(
									'Supprimer le type de contrat d\'insertion ',
									array( 'controller' => 'typoscontrats', 'action' => 'delete', $typocontrat['Typocontrat']['id'] )
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