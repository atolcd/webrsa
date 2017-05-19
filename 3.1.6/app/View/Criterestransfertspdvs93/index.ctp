<?php
	$title_for_layout = 'Recherche par allocataires sortants, intra-département';
	$this->set( compact( 'title_for_layout' ) );
	echo $this->Html->tag( 'h1', $title_for_layout );

	// Filtre
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo '<ul class="actionMenu"><li>'.$this->Xhtml->link(
		$this->Xhtml->image(
			'icons/application_form_magnify.png',
			array( 'alt' => '' )
		).' Formulaire',
		'#',
		array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "var form = $$( 'form' ); form = form[0]; $( form ).toggle(); return false;" )
	).'</li></ul>';

	// Filtre
	echo $this->Form->create( null, array( 'type' => 'post', 'url' => array( 'controller' => $this->request->params['controller'], 'action' => $this->request->params['action'] ), 'id' => 'Search', 'class' => ( ( is_array( $this->request->data ) && !empty( $this->request->data ) && isset( $this->request->data['Search']['active'] ) ) ? 'folded' : 'unfolded' ) ) );

	echo $this->Form->input( 'Search.active', array( 'type' => 'hidden', 'value' => true ) );

	echo $this->Search->blocAllocataire( array(), array(), 'Search' );
	echo $this->Search->toppersdrodevorsa( $options['toppersdrodevorsa'], 'Search.Calculdroitrsa.toppersdrodevorsa' );

	echo $this->Search->date( 'Search.Orientstruct.date_valid' );
	echo $this->Form->input( 'Search.Dossier.dernier', array( 'label' => 'Uniquement la dernière demande RSA pour un même allocataire', 'type' => 'checkbox' ) );
	echo $this->Search->blocAdresse( $options['mesCodesInsee'], $options['cantons'], 'Search' );

	echo $this->Search->etatdosrsa( $options['etatdosrsa'], 'Search.Situationdossierrsa.etatdosrsa' );

	echo $this->Form->input( 'Search.Orientstruct.typeorient_id', array( 'label' => 'Type d\'orientation', 'type' => 'select', 'empty' => true, 'options' => $options['typesorients'] ) );

	echo $this->Form->input( 'Search.NvOrientstruct.structurereferente_id', array( 'label' => __d( 'criterestransfertspdvs93', 'NvStructurereferente.lib_struc' ), 'type' => 'select', 'empty' => true, 'options' => $options['structuresreferentes'] ) );

	echo $this->Search->referentParcours( $structuresreferentesparcours, $referentsparcours, 'Search' );
	echo $this->Search->paginationNombretotal( 'Search.Pagination.nombre_total' );

	echo $this->Form->submit( __( 'Search' ) );
	echo $this->Form->end();

	// Résultats
	if( isset( $results ) ) {
		echo $this->Html->tag( 'h2', 'Résultats de la recherche' );

		if( empty( $results ) ) {
			echo $this->Html->tag( 'p', 'Aucun résultat', array( 'class' => 'notice' ) );
		}
		else {
			$domain_search_plugin = ( Configure::read( 'Cg.departement' ) == 93 ) ? 'search_plugin_93' : 'search_plugin';

			$pagination = $this->Xpaginator2->paginationBlock( 'Dossier', $this->passedArgs );
			echo $pagination;

			echo '<table id="searchResults" class="cohortestransfertspdvs93 transferes tooltips">';
			echo '<thead>';
			echo $this->Html->tableHeaders(
				array(
					__d( 'dossier', 'Dossier.numdemrsa' ),
					__d( 'dossier', 'Dossier.matricule' ),
					'Adresse précédente',
					'Allocataire',
					__d( 'prestation', 'Prestation.rolepers' ),
					__d( 'criterestransfertspdvs93', 'Transfertpdv93.created' ), //'Date de transfert',
					__d( 'criterestransfertspdvs93', 'VxStructurereferente.lib_struc' ), //'Structure référente source',
					__d( 'criterestransfertspdvs93', 'NvStructurereferente.lib_struc' ), //'Structure référente cible',
					'Actions',
					array( 'Informations complémentaires' => array( 'class' => 'innerTableHeader noprint' ) )
				)
			);
			echo '</thead>';
			echo '<tbody>';
			foreach( $results as $index => $result ) {
				$innerTable = '<table id="innerTablesearchResults'.$index.'" class="innerTable">
					<tbody>
						<tr>
							<th>'.__d( $domain_search_plugin, 'Structurereferenteparcours.lib_struc' ).'</th>
							<td>'.Hash::get( $result, 'Structurereferenteparcours.lib_struc' ).'</td>
						</tr>
						<tr>
							<th>'.__d( $domain_search_plugin, 'Referentparcours.nom_complet' ).'</th>
							<td>'.Hash::get( $result, 'Referentparcours.nom_complet' ).'</td>
						</tr>
					</tbody>
				</table>';

				echo $this->Html->tableCells(
					array(
						h( $result['Dossier']['numdemrsa'] ),
						h( $result['Dossier']['matricule'] ),
						h( "{$result['Adresse']['codepos']} {$result['Adresse']['nomcom']}" ),
						h( "{$options['qual'][$result['Personne']['qual']]} {$result['Personne']['nom']} {$result['Personne']['prenom']}" ),
						$options['rolepers'][$result['Prestation']['rolepers']],
						$this->Locale->date( __( 'Date::short' ), $result['Transfertpdv93']['created'] ),
						$result['VxStructurereferente']['lib_struc'],
						$result['NvStructurereferente']['lib_struc'],
						$this->Xhtml->viewLink(
							'Voir',
							array( 'controller' => 'dossiers', 'action' => 'view', $result['Dossier']['id'] ),
							$this->Permissions->check( 'dossiers', 'view' ),
							true
						),
						array( $innerTable, array( 'class' => 'innerTableCell noprint' ) ),
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);
			}
			echo '</tbody>';
			echo '</table>';

			echo $pagination;
		}
	}
?>
<?php if( isset( $results ) ):?>
<ul class="actionMenu">
	<li><?php
		echo $this->Xhtml->exportLink(
			'Télécharger le tableau',
			array( 'action' => 'exportcsv' ) + Hash::flatten( $this->request->data, '__' ),
			( $this->Permissions->check( $this->request->params['controller'], 'exportcsv' ) && count( $results ) > 0 )
		);
	?></li>
</ul>
<?php endif;?>