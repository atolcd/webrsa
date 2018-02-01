<?php
	$this->pageTitle = 'Comité examen APRE';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<h1>Recherche de Comité d'examen</h1>
	<ul class="actionMenu">
		<?php
			echo '<li>'.$this->Xhtml->addComiteLink(
				'Ajouter Comité',
				array( 'controller' => 'comitesapres', 'action' => 'add' ),
				$this->Permissions->check( 'comitesapres', 'add' )
			).' </li>';
		?>
	</ul>
<?php
	if( is_array( $this->request->data ) ) {
		echo '<ul class="actionMenu"><li>'.$this->Xhtml->link(
			$this->Xhtml->image(
				'icons/application_form_magnify.png',
				array( 'alt' => '' )
			).' Formulaire',
			'#',
			array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "$( 'Search' ).toggle(); return false;" )
		).'</li></ul>';
	}

?>
<script type="text/javascript">
	document.observe("dom:loaded", function() {
		observeDisableFieldsetOnCheckbox( 'ComiteapreDatecomite', $( 'ComiteapreDatecomiteFromDay' ).up( 'fieldset' ), false );
		observeDisableFieldsetOnCheckbox( 'ComiteapreHeurecomite', $( 'ComiteapreHeurecomiteFromHour' ).up( 'fieldset' ), false );
	});
</script>

<?php $pagination = $this->Xpaginator->paginationBlock( 'Comiteapre', $this->passedArgs ); ?>
<?php echo $this->Xform->create( 'Comiteapre', array( 'type' => 'post', 'url' => array( 'action' => 'index' ), 'id' => 'Search', 'class' => ( ( is_array( $this->request->data ) && !empty( $this->request->data ) ) ? 'folded' : 'unfolded' ), 'novalidate' => true ) );?>

	<fieldset>
		<?php echo $this->Xform->input( 'Comiteapre.recherche', array( 'label' => false, 'type' => 'hidden', 'value' => true ) );?>

		<?php echo $this->Xform->input( 'Comiteapre.datecomite', array( 'label' => 'Filtrer par date de Comité d\'examen', 'type' => 'checkbox' ) );?>
		<fieldset>
			<legend>Date de Comité</legend>
			<?php
				$datecomite_from = Set::check( $this->request->data, 'Comiteapre.datecomite_from' ) ? Set::extract( $this->request->data, 'Comiteapre.datecomite_from' ) : strtotime( '-1 week' );
				$datecomite_to = Set::check( $this->request->data, 'Comiteapre.datecomite_to' ) ? Set::extract( $this->request->data, 'Comiteapre.datecomite_to' ) : strtotime( 'now' );
			?>
			<?php echo $this->Xform->input( 'Comiteapre.datecomite_from', array( 'label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 120, 'selected' => $datecomite_from ) );?>
			<?php echo $this->Xform->input( 'Comiteapre.datecomite_to', array( 'label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 5, 'minYear' => date( 'Y' ) - 120, 'selected' => $datecomite_to ) );?>
		</fieldset>
	</fieldset>
	<?php  require_once  'limitpage.ctp' ;?>
	<div class="submit noprint">
		<?php echo $this->Xform->button( 'Rechercher', array( 'type' => 'submit' ) );?>
		<?php echo $this->Xform->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
	</div>

<?php echo $this->Xform->end();?>

<!-- Résultats -->
<?php if( isset( $comitesapres ) ):?>

	<h2 class="noprint">Résultats de la recherche</h2>

	<?php if( is_array( $comitesapres ) && count( $comitesapres ) > 0  ):?>
		<?php echo $pagination;?>
		<table id="searchResults" class="tooltips">
			<thead>
				<tr>
					<th><?php echo $this->Xpaginator->sort( 'Intitulé du comité', 'Comiteapre.intitulecomite' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Lieu du comité', 'Comiteapre.lieucomite' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Date du comité', 'Comiteapre.datecomite' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Heure du comité', 'Comiteapre.heurecomite' );?></th>
					<th colspan="3" class="action">Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach( $comitesapres as $comiteapre ) {

						$decisionPrise = true;
						$decision = Hash::filter( (array)Set::extract( $comiteapre, '/Apre/ApreComiteapre/decisioncomite' ) );
						if( !empty( $decision ) ) {
							$decisionPrise = false;
						}

						echo $this->Xhtml->tableCells(
							array(
								h( Set::classicExtract( $comiteapre, 'Comiteapre.intitulecomite' ) ),
								h( Set::classicExtract( $comiteapre, 'Comiteapre.lieucomite' ) ),
								h( date_short( Set::classicExtract( $comiteapre, 'Comiteapre.datecomite' ) ) ),
								h( $this->Locale->date( 'Time::short', Set::classicExtract( $comiteapre, 'Comiteapre.heurecomite' ) ) ),
								$this->Xhtml->viewLink(
									'Voir le comité',
									array( 'controller' => 'comitesapres', 'action' => 'view', Set::classicExtract( $comiteapre, 'Comiteapre.id' ) ),
									$decisionPrise,
									$this->Permissions->check( 'comitesapres', 'index' )
								),
								$this->Xhtml->rapportLink(
									'Rapport',
									array( 'controller' => 'comitesapres', 'action' => 'rapport', Set::classicExtract( $comiteapre, 'Comiteapre.id' ) ),
									$this->Permissions->check( 'comitesapres', 'rapport' )
								),
								$this->Xhtml->notificationsApreLink(
									'Notifier la décision',
									array( 'controller' => 'cohortescomitesapres', 'action' => 'notificationscomite', 'Cohortecomiteapre__id' => Set::classicExtract( $comiteapre, 'Comiteapre.id' ) ),
									$this->Permissions->check( 'cohortescomitesapres', 'notificationscomite' )
								),
							),
							array( 'class' => 'odd' ),
							array( 'class' => 'even' )
						);
					}
				?>
			</tbody>
		</table>
		<?php echo $pagination;?>
	<ul class="actionMenu">
		<li><?php
			echo $this->Xhtml->exportLink(
				'Télécharger le tableau',
				array( 'controller' => 'comitesapres', 'action' => 'exportcsv' ) + Hash::flatten( $this->request->data, '__' ),
				$this->Permissions->check( 'comitesapres', 'exportcsv' )
			);
		?></li>
	</ul>
	<?php else:?>
		<p>Vos critères n'ont retourné aucun comité.</p>
	<?php endif?>
<?php endif?>