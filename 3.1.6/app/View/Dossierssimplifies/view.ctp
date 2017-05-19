<?php $this->pageTitle = 'Préconisation d\'orientation';?>
<h1>Préconisation d'orientation</h1>

<table class="tooltips">
	<thead>
		<tr>
			<th>Nom</th>
			<th>Prénom</th>
			<th>Date de la demande</th>
			<th>Date d'orientation</th>
			<th>Statut de l'orientation</th>
			<th>Préconisation d'orientation</th>
			<th>Structure référente</th>
			<th colspan="2" class="action">Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php
			foreach( $personnes as $personne ) {
				$typeorient_id = Hash::get( $personne, 'Orientstruct.typeorient_id' );

				echo $this->Xhtml->tableCells(
					array(
						h( $personne['Personne']['nom'] ),
						h( $personne['Personne']['prenom'] ),
						h( $this->Locale->date( 'Date::short', $details['Dossier']['dtdemrsa'] ) ),
						h( $this->Locale->date( 'Date::short', Hash::get( $personne, 'Orientstruct.date_valid' ) ) ),
						h( Hash::get( $personne, 'Orientstruct.statut_orient' ) ),
						h( Set::enum( Set::classicExtract( $personne, 'Structurereferente.typeorient_id' ), $typeorient ) ) ,
						h( Set::classicExtract( $personne, 'Structurereferente.lib_struc' )  ),
						$this->Xhtml->editLink(
							'Editer l\'orientation',
							array( 'controller' => 'dossierssimplifies', 'action' => 'edit', $personne['Personne']['id'] ),
							$this->Permissions->checkDossier( 'dossierssimplifies', 'edit', $dossierMenu )
						),
						$this->Xhtml->printLink(
							'Imprimer la notification',
							array( 'controller' => 'orientsstructs', 'action' => 'impression', Hash::get( $personne, 'Orientstruct.id' ) ),
							$this->Permissions->checkDossier( 'orientsstructs', 'impression', $dossierMenu ) && !empty( $typeorient_id )
						),
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);
			}
		?>
	</tbody>
</table>