<?php
	$this->pageTitle = 'Recherche par Bilans de parcours';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>

<h1><?php echo $this->pageTitle;?></h1>

<?php
	echo '<ul class="actionMenu"><li>'.$this->Xhtml->link(
		$this->Xhtml->image(
			'icons/application_form_magnify.png',
			array( 'alt' => '' )
		).' Formulaire',
		'#',
		array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "$( 'Search' ).toggle(); return false;" )
	).'</li></ul>';
?>
<script type="text/javascript">
	document.observe("dom:loaded", function() {
		observeDisableFieldsetOnCheckbox( 'Bilanparcours66Datebilan', $( 'Bilanparcours66DatebilanFromDay' ).up( 'fieldset' ), false );

		dependantSelect( 'Bilanparcours66ReferentId', 'Bilanparcours66StructurereferenteId' );
	});
</script>

<?php echo $this->Xform->create( 'Criterebilanparcours66', array( 'type' => 'post', 'action' => $this->action, 'id' => 'Search', 'class' => ( ( is_array( $this->request->data ) && !empty( $this->request->data ) ) ? 'folded' : 'unfolded' ) ) );?>

	<?php echo $this->Xform->input( 'Bilanparcours66.indexparams', array( 'label' => false, 'type' => 'hidden', 'value' => true ) );?>
	<?php
		echo $this->Search->blocAllocataire();
		echo $this->Search->blocAdresse( $mesCodesInsee, $cantons );
	?>
	<fieldset>
		<legend>Recherche par dossier</legend>
		<?php
			echo $this->Form->input( 'Dossier.numdemrsa', array( 'label' => 'Numéro de demande RSA' ) );
			echo $this->Form->input( 'Dossier.matricule', array( 'label' => __d( 'dossier', 'Dossier.matricule' ), 'maxlength' => 15 ) );

			$valueDossierDernier = isset( $this->request->data['Dossier']['dernier'] ) ? $this->request->data['Dossier']['dernier'] : true;
			echo $this->Form->input( 'Dossier.dernier', array( 'label' => 'Uniquement la dernière demande RSA pour un même allocataire', 'type' => 'checkbox', 'checked' => $valueDossierDernier ) );
			echo $this->Search->etatdosrsa($etatdosrsa);
		?>
	</fieldset>
			<fieldset>
				<legend>Filtrer par Bilan de parcours</legend>
				<?php
					echo $this->Default2->subform(
						array(
							'Bilanparcours66.proposition' => array( 'type' => 'select', 'options' => $options['proposition'] ),
							'Bilanparcours66.choixparcours' => array( 'type' => 'select', 'options' => $options['choixparcours'] ),
							'Bilanparcours66.examenaudition' => array( 'type' => 'select', 'options' => $options['examenaudition'] ),
							'Bilanparcours66.maintienorientation' => array( 'type' => 'select', 'options' => $options['maintienorientation'] ),
							'Bilanparcours66.structurereferente_id' => array( 'type' => 'select', 'options' => $struct ),
							'Bilanparcours66.referent_id' => array( 'type' => 'select', 'options' => $referents ),
							'Bilanparcours66.positionbilan' => array( 'type' => 'select', 'options' => $options['positionbilan'] ),
						),
						array(
							'options' => $options,
						)
					);
				?>

				<?php echo $this->Xform->input( 'Bilanparcours66.datebilan', array( 'label' => 'Filtrer par date de Bilan de parcours', 'type' => 'checkbox' ) );?>
				<fieldset>
					<legend>Filtrer par période</legend>
					<?php
						$datebilan_from = Set::check( $this->request->data, 'Bilanparcours66.datebilan_from' ) ? Set::extract( $this->request->data, 'Bilanparcours66.datebilan_from' ) : strtotime( '-1 week' );
						$datebilan_to = Set::check( $this->request->data, 'Bilanparcours66.datebilan_to' ) ? Set::extract( $this->request->data, 'Bilanparcours66.datebilan_to' ) : strtotime( 'now' );
					?>
					<?php echo $this->Xform->input( 'Bilanparcours66.datebilan_from', array( 'label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 1, 'minYear' => date( 'Y' ) - 10, 'selected' => $datebilan_from ) );?>
					<?php echo $this->Xform->input( 'Bilanparcours66.datebilan_to', array( 'label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 1, 'minYear' => date( 'Y' ) - 10, 'selected' => $datebilan_to ) );?>
				</fieldset>
				<?php
					echo $this->Form->input( 'Bilanparcours66.hasmanifestation', array( 'label' => 'L\'allocataire s\'est-il manifesté ? ', 'type' => 'select', 'options' => array( 'O' => 'Oui', 'N' => 'Non'), 'empty' => true ) );
				?>
			</fieldset>

	<?php
		echo $this->Search->referentParcours( $structuresreferentesparcours, $referentsparcours );
		echo $this->Search->paginationNombretotal();
	?>

	<div class="submit noprint">
		<?php echo $this->Xform->button( 'Rechercher', array( 'type' => 'submit' ) );?>
		<?php echo $this->Xform->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
	</div>

<?php echo $this->Xform->end();?>
<?php $pagination = $this->Xpaginator->paginationBlock( 'Bilanparcours66', $this->passedArgs ); ?>
<?php if ($pagination):?>
<h2 class="noprint">Résultats de la recherche</h2>
<?php echo $pagination;?>
<?php endif;?>
<?php if( isset( $bilansparcours66 ) ):?>
	<?php if( is_array( $bilansparcours66 ) && count( $bilansparcours66 ) > 0  ):?>
		<?php
			echo '<table id="searchResults" class="tooltips"><thead>';
				echo '<tr>
					<th>'.$this->Xpaginator->sort( __d( 'dossier', 'Dossier.numdemrsa' ), 'Dossier.numdemrsa' ).'</th>
					<th>'.$this->Xpaginator->sort( __d( 'bilanparcours66', 'Bilanparcours66.datebilan' ), 'Bilanparcours66.datebilan' ).'</th>
					<th>'.$this->Xpaginator->sort( __d( 'personne', 'Personne.nom_complet' ), 'Personne.nom_complet_court' ).'</th>
					<th>'.$this->Xpaginator->sort( __d( 'structurereferente', 'Structurereferente.lib_struc' ), 'Structurereferente.lib_struc' ).'</th>
					<th>'.$this->Xpaginator->sort( __d( 'referent', 'Referent.nom_complet' ), 'Referent.nom_complet_court' ).'</th>
					<th>'.$this->Xpaginator->sort( __d( 'bilanparcours66', 'Bilanparcours66.proposition' ), 'Bilanparcours66.proposition' ).'</th>
					<th>'.$this->Xpaginator->sort( __d( 'bilanparcours66', 'Bilanparcours66.positionbilan' ), 'Bilanparcours66.positionbilan' ).'</th>
					<th>'.$this->Xpaginator->sort( 'Motif de la saisine', 'Bilanparcours66.choixparcours' ).'</th>
					<th>Saisine EP</th>
					<th>Actions</th>
					<th class="innerTableHeader noprint">Informations complémentaires</th>
				</tr></thead><tbody>';
			foreach( $bilansparcours66 as $index => $bilanparcour66 ) {
				$innerTable = '<table id="innerTablesearchResults'.$index.'" class="innerTable">
					<tbody>
						<tr>
							<th>Code INSEE</th>
							<td>'.$bilanparcour66['Adresse']['numcom'].'</td>
						</tr>
						<tr>
							<th>Localité</th>
							<td>'.$bilanparcour66['Adresse']['nomcom'].'</td>
						</tr>
						<tr>
							<th>'.__d( 'search_plugin', 'Structurereferenteparcours.lib_struc' ).'</th>
							<td>'.Hash::get( $bilanparcour66, 'Structurereferenteparcours.lib_struc' ).'</td>
						</tr>
						<tr>
							<th>'.__d( 'search_plugin', 'Referentparcours.nom_complet' ).'</th>
							<td>'.Hash::get( $bilanparcour66, 'Referentparcours.nom_complet' ).'</td>
						</tr>
					</tbody>
				</table>';

				$isSaisine = '0';
				if( isset( $bilanparcour66['Dossierep']['themeep'] ) ){
					$isSaisine = '1';
				}

				$motif = null;
				if (empty($bilanparcour66['Bilanparcours66']['choixparcours']) && !empty($bilanparcour66['Bilanparcours66']['examenaudition'])) {
					$motif = Set::classicExtract( $options['examenaudition'], $bilanparcour66['Bilanparcours66']['examenaudition'] );
				}
				elseif (empty($bilanparcour66['Bilanparcours66']['choixparcours']) && empty($bilanparcour66['Bilanparcours66']['examenaudition']) && ( $bilanparcour66['Bilanparcours66']['proposition'] != 'auditionpe' ) ) {
					if ($bilanparcour66['Bilanparcours66']['maintienorientation']=='0') {
						$motif = 'Réorientation';
					}
					else {
						$motif = 'Maintien';
					}
				}
				elseif (empty($bilanparcour66['Bilanparcours66']['choixparcours']) && empty($bilanparcour66['Bilanparcours66']['examenaudition']) && ( $bilanparcour66['Bilanparcours66']['proposition'] == 'auditionpe' ) ) {
					$motif = Set::classicExtract( $options['examenauditionpe'], $bilanparcour66['Bilanparcours66']['examenauditionpe'] );
				}
				else {
					$motif = Set::classicExtract( $options['choixparcours'], $bilanparcour66['Bilanparcours66']['choixparcours'] );
				}




				echo '<tr>
					<td>'.h( $bilanparcour66['Dossier']['numdemrsa'] ).'</td>
					<td>'.h( $this->Locale->date( 'Date::short', $bilanparcour66['Bilanparcours66']['datebilan'] ) ).'</td>
					<td>'.h( $bilanparcour66['Personne']['nom_complet'] ).'</td>
					<td>'.h( $bilanparcour66['Structurereferente']['lib_struc'] ).'</td>
					<td>'.h( $bilanparcour66['Referent']['nom_complet'] ).'</td>
					<td>'.h( Set::classicExtract( $options['proposition'], $bilanparcour66['Bilanparcours66']['proposition'] ) ).'</td>
					<td>'.h( Set::enum( Set::classicExtract( $bilanparcour66, 'Bilanparcours66.positionbilan' ),  $options['positionbilan'] ) ).'</td>
					<td>'.h( $motif ).'</td>'.
					$this->Default2->Type2->format( $isSaisine, 'Dossierep.themeep', array( 'type' => 'boolean', 'tag' => 'td' ) ).
					'<td>'.$this->Xhtml->link( 'Voir', array( 'controller' => 'bilansparcours66', 'action' => 'index', $bilanparcour66['Personne']['id'] ) ).'</td>
					<td class="innerTableCell noprint">'.$innerTable.'</td>
				</tr>';
			}
			echo '</tbody></table>';
	?>
	<ul class="actionMenu">
		<li><?php
			echo $this->Xhtml->printLinkJs(
				'Imprimer le tableau',
				array( 'onclick' => 'printit(); return false;', 'class' => 'noprint' )
			);
		?></li>
		<li><?php
			echo $this->Xhtml->exportLink(
				'Télécharger le tableau',
				array( 'controller' => 'criteresbilansparcours66', 'action' => 'exportcsv' ) + Hash::flatten( $this->request->data, '__' ),
				$this->Permissions->check( 'criteresbilansparcours66', 'exportcsv' )
			);
		?></li>
	</ul>
<?php echo $pagination;?>

	<?php else:?>
		<?php echo $this->Xhtml->tag( 'p', 'Aucun résultat ne correspond aux critères choisis.', array( 'class' => 'notice' ) );?>
	<?php endif;?>
<?php endif;?>

<?php echo $this->Search->observeDisableFormOnSubmit( 'Search' ); ?>