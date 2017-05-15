<?php
	$this->pageTitle = 'Recherche par critères';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<h1><?php echo $this->pageTitle; ?></h1>

<ul class="actionMenu">
	<?php
		if( $this->Session->read( 'Auth.User.username' ) == 'cg66' ) { // FIXME
			echo '<li>'.$this->Xhtml->addSimpleLink(
				'Ajouter une préconisation d\'orientation',
				array( 'controller' => 'dossierssimplifies', 'action' => 'add' )
			).' </li>';
		}

		if( is_array( $this->request->data ) ) {
			echo '<li>'.$this->Xhtml->link(
				$this->Xhtml->image(
					'icons/application_form_magnify.png',
					array( 'alt' => '' )
				).' Formulaire',
				'#',
				array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "$( 'Search' ).toggle(); return false;" )
			).'</li>';
		}
	?>
</ul>
<?php echo $this->Form->create( 'Critere', array( 'type' => 'post', 'action' => '/index/', 'id' => 'Search', 'class' => ( is_array( $this->request->data ) ? 'folded' : 'unfolded' ) ) );?>

	<fieldset>
		<legend>Recherche par types d'orientations</legend>
		<?php echo $this->Form->input( 'Typeorient.id', array( 'label' =>  __d( 'structurereferente', 'Structurereferente.lib_type_orient' ), 'type' => 'select' , 'options' => $typeorient, 'empty' => true ) );?>
	</fieldset>
	<fieldset>
		<legend>Recherche par Structures référentes</legend>
			<?php echo $this->Form->input( 'Structurereferente.id', array( 'label' => 'Nom de la structure', 'type' => 'select' , 'options' => $typestruct, 'empty' => true  ) );?>
	</fieldset>
	<fieldset>
		<legend>Recherche par Statut</legend>
		<?php echo $this->Form->input( 'Orientstructs.statut_orient', array( 'label' => 'Statut de l\'orientation', 'type' => 'select', 'options' => $statuts, 'empty' => true ) );?>
	</fieldset>

	<?php echo $this->Form->submit( 'Rechercher' );?>
<?php echo $this->Form->end();?>

<!-- Résultats -->
<?php if( isset( $criteres ) ):?>
	<h2>Résultats de la recherche</h2>

	<?php if( is_array( $criteres ) && count( $criteres ) > 0 ):?>
		<table id="searchResults" class="tooltips">
			<thead>
				<tr>
					<th>Numéro dossier</th>
					<th>Date de demande</th>
					<th>NIR</th>
					<th>Allocataire</th>
					<th>État du dossier</th>
					<th class="action">Actions</th>
					<th class="innerTableHeader">Informations complémentaires</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach( $criteres as $index => $critere ):?>
					<?php
						$innerTable = '<table id="innerTablesearchResults'.$index.'" class="innerTable">
							<tbody>
								<tr>
									<th>Commune de naissance</th>
									<td>'. $critere[0]['nomcomnai'].'</td>
								</tr>
								<tr>
									<th>Date de naissance</th>
									<td>'.date_short( $critere[0]['dtnai']).'</td>
								</tr>
							</tbody>
						</table>';

						echo $this->Xhtml->tableCells(
							array(
								h($critere['Dossier']['Dossier']['numdemrsa']),
								h($critere['Dossier']['Dossier']['dtdemrsa']),
								h( $critere[0]['nir'] ), // FIXME: 0
								implode(
									' ',
									array(
										$critere[0]['qual'],
										$critere[0]['nom'],
										implode( ' ', array( $critere[0]['prenom'], $critere[0]['prenom2'], $critere[0]['prenom3'] ) )
									)
								),
								h(' '),
								$this->Xhtml->viewLink(
									'Voir le dossier « '.$critere['Dossier']['Dossier']['numdemrsa'].' »',
									array( 'controller' => 'personnes', 'action' => 'view', $critere[0]['id'] )
								),

								array( $innerTable, array( 'class' => 'innerTableCell' ) ),
							),
							array( 'class' => 'odd', 'id' => 'innerTableTrigger'.$index ),
							array( 'class' => 'even', 'id' => 'innerTableTrigger'.$index )
						);
					?>
				<?php endforeach;?>
			</tbody>
		</table>

	<?php else:?>
		<p>Vos critères n'ont retourné aucun dossier.</p>
	<?php endif?>
<?php endif?>