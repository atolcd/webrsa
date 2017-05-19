<?php
	echo $this->element( 'required_javascript' );

	$title_for_layout = '2. Saisie d\'un CER';
	$this->set( compact( 'title_for_layout' ) );
	echo $this->Xhtml->tag( 'h1', $title_for_layout );

	require_once( dirname( __FILE__ ).DS.'filtre.ctp' );

	if( isset( $cers93 ) ) {
		echo $this->Html->tag( 'h2', 'Résultats de la recherche' );

		if( empty( $cers93 ) ) {
			echo $this->Xhtml->tag( 'p', 'Aucun résultat', array( 'class' => 'notice' ) );
		}
		else {
			$pagination = $this->Xpaginator->paginationBlock( 'Personne', $this->passedArgs );
			echo $pagination;

			echo $this->Form->create( null, array( 'id' => 'Cohortescers93Saisie' ) );
			echo '<table id="searchResults" class="tooltips">';
			echo '<thead>
					<tr>
						<th>Commune</th>
						<th>Nom/Prénom</th>
						<th>Date d\'orientation</th>
						<th>Date d\'affectation</th>
						<th>Rang CER</th>
						<th class="action">Dernier RDV</th>
						<th>Statut CER</th>
						<th class="action">Forme du CER</th>
						<th class="action">Commentaire</th>
						<th class="action">Actions</th>
						<th class="action">Détails</th>
					</tr>
				</thead>';
			echo '<tbody>';

			require_once( dirname( __FILE__ ).DS.'saisie_tbody_trs.ctp' );

			echo '</tbody>';
			echo '</table>';
			echo $this->Form->end();

			echo $pagination;
		}
	}
?>
<?php if( isset( $cers93 ) ):?>
<ul class="actionMenu">
	<li><?php
		echo $this->Xhtml->exportLink(
			'Télécharger le tableau',
			array( 'action' => 'exportcsv', 'saisie' ) + Hash::flatten( $this->request->data, '__' ),
			( $this->Permissions->check( 'cohortescers93', 'exportcsv' ) && count( $cers93 ) > 0 )
		);
	?></li>
</ul>
<?php endif;?>