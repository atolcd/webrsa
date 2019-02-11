<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'offreinsertion', "Offresinsertion::{$this->action}" )
	)
?>
<?php
	// Formulaire de recherche des actions, partenaires et contacts
    echo '<ul class="actionMenu"><li>'.$this->Xhtml->link(
        $this->Xhtml->image(
            'icons/application_form_magnify.png',
            array( 'alt' => '' )
        ).' Formulaire',
        '#',
        array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "$( 'Search' ).toggle(); return false;" )
    ).'</li></ul>';

    //Création du formulaire
    echo $this->Xform->create( 'Offreinsertion', array( 'type' => 'post', 'url' => array( 'action' => 'index' ), 'id' => 'Search', 'class' => ( ( is_array( $this->request->data ) && !empty( $this->request->data ) ) ? 'folded' : 'unfolded' ) ) );
?>
	<fieldset>
		<?php
			echo $this->Xform->input( 'Search.active', array( 'type' => 'hidden', 'value' => true ) );

			echo $this->Default2->subform(
				array(
					'Search.Actioncandidat.name' => array( 'label' => __d( 'actioncandidat', 'Actioncandidat.name' ), 'type' => 'select', 'options' => $listeActions, 'empty' => true ),
					'Search.Partenaire.id' => array( 'label' => __d( 'partenaire', 'Partenaire.libstruc' ), 'type' => 'select', 'options' => $listePartenaires, 'empty' => true ),
					'Search.Contactpartenaire.id' => array( 'label' => __d( 'contactpartenaire', 'Contactpartenaire.nom' ), 'type' => 'select', 'options' => $listeContacts, 'empty' => true ),
					'Search.Partenaire.codepartenaire' => array( 'label' => __d( 'partenaire', 'Partenaire.codepartenaire' ) ),
					'Search.Actioncandidat.themecode' => array( 'label' => __d( 'actioncandidat', 'Actioncandidat.themecode' ) ),
					'Search.Actioncandidat.codefamille' => array( 'label' => __d( 'actioncandidat', 'Actioncandidat.codefamille' ) ),
					'Search.Actioncandidat.numcodefamille' => array( 'label' => __d( 'actioncandidat', 'Actioncandidat.numcodefamille' ) ),
					'Search.Actioncandidat.referent_id' => array( 'label' => __d( 'actioncandidat', 'Actioncandidat.referent_id' ), 'options' => $correspondants, 'empty' => true ),
					'Search.Actioncandidat.hasfichecandidature' => array( 'label' => __d( 'actioncandidat', 'Actioncandidat.hasfichecandidature', true ), 'options' => $options['Actioncandidat']['hasfichecandidature'], 'empty' => true ),
					'Search.Actioncandidat.actif' => array( 'label' =>'Action active ?', 'options' => $options['Actioncandidat']['actif'], 'value' => 'N' )
				),
				array(
					'options' => $options
				)
			);
		?>
	</fieldset>

    <div class="submit noprint">
        <?php echo $this->Xform->button( 'Rechercher', array( 'type' => 'submit' ) );?>
        <?php echo $this->Xform->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
    </div>

<?php echo $this->Xform->end();?>
	<?php if( isset( $results ) ):?>
		<?php if( is_array( $results ) && count( $results ) > 0  ):?>

	<h2 class="noprint">Résultats de la recherche</h2>
	<br>
		<div id="tabbedWrapper" class="tabs">
			<?php
				foreach( Hash::flatten( $this->request->data['Search'] ) as $filtre => $value  ) {
					echo $this->Form->input( "Search.{$filtre}", array( 'type' => 'hidden', 'value' => $value ) );
				}
			?>
			<div id="global">
				<h2 class="title">Global</h2>
				<?php $pagination = $this->Xpaginator->paginationBlock( 'Actioncandidat', $this->passedArgs ); ?>
				<?php /*echo $pagination;*/?>
				<table>
					<colgroup span="10" style="border-right: 5px solid #235F7D;border-left: 5px solid #235F7D;" />
					<colgroup span="4" style="border-right: 5px solid #235F7D;border-left: 5px solid #235F7D;" />
					<colgroup span="4" style="border-right: 5px solid #235F7D;border-left: 5px solid #235F7D;" />
					<colgroup />
					<colgroup />
					<thead>
						<tr>
							<th colspan="10">Action de candidature</th>
							<th colspan="4">Contact</th>
							<th colspan="5">Partenaire/Prestataire</th>
							<th>Actions</th>
						</tr>
						<tr>
                            <th>Intitulé de l'action</th>
							<th>Code de l'action</th>
							<th>Chargé d'insertion</th>
							<th>Secrétaire</th>
							<th>Ville</th>
							<th>Canton</th>
							<th>Début de l'action</th>
							<th>Fin de l'action</th>
							<th>Nombre de postes disponibles</th>
							<th>Nombre d\'heures disponibles</th>

							<th>Nom du contact</th>
							<th>N° de téléphone du contact</th>
							<th>N° de fax</th>
							<th>Email du contact</th>

							<th>Libellé du partenaire</th>
							<th>Code du partenaire</th>
							<th>Adresse du partenaire</th>
							<th>N° de téléphone du partenaire</th>

							<th>Nb de fichiers liés</th>

							<th></th>
						</tr>
					</thead>
					<tbody>
					<?php
						$urlParams = Hash::flatten( $this->request->data, '__' );
						foreach( $results['global'] as $result ) {
							echo $this->Xhtml->tableCells(
								array(
									Set::classicExtract( $result, 'Actioncandidat.name' ),
									Set::classicExtract( $result, 'Actioncandidat.codeaction' ),
									Set::classicExtract( $result, 'Chargeinsertion.nom_complet' ),
									Set::classicExtract( $result, 'Secretaire.nom_complet' ),
									Set::classicExtract( $result, 'Actioncandidat.lieuaction' ),
									Set::classicExtract( $result, 'Actioncandidat.cantonaction' ),
									date_short( Set::classicExtract( $result, 'Actioncandidat.ddaction' ) ),
									date_short( Set::classicExtract( $result, 'Actioncandidat.dfaction' ) ),
									Set::classicExtract( $result, 'Actioncandidat.nbpostedispo' ),
									Set::classicExtract( $result, 'Actioncandidat.nbheuredispo' ),
									Set::classicExtract( $result, 'Contactpartenaire.nom_candidat' ),
									Set::classicExtract( $result, 'Contactpartenaire.numtel' ),
									Set::classicExtract( $result, 'Contactpartenaire.numfax' ),
									Set::classicExtract( $result, 'Contactpartenaire.email' ),
									Set::classicExtract( $result, 'Partenaire.libstruc' ),
									Set::classicExtract( $result, 'Partenaire.codepartenaire' ),
									Set::classicExtract( $result, 'Partenaire.adresse' ),
									Set::classicExtract( $result, 'Partenaire.numtel' ),
									Set::classicExtract( $result, 'Fichiermodule.nb_fichiers_lies' ),
									$this->Xhtml->filelink(
										'Voir',
										array_merge(
											array(
												'controller' => 'offresinsertion', 'action' => 'view', Set::classicExtract( $result, 'Actioncandidat.id' )
											),
											$urlParams
										)
									)
								),
								array( 'class' => 'odd' ),
								array( 'class' => 'even' )
							);

						}
					?>
					</tbody>
				</table>
                <ul class="actionMenu">
                    <li><?php
                        echo $this->Xhtml->exportLink(
                            'Télécharger le tableau',
                            array( 'action' => 'exportcsv' ) + Hash::flatten( $this->request->data, '__' ),
                            $this->Permissions->check( 'offresinsertion', 'exportcsv' )
                        );
                    ?></li>
                </ul>
			</div>
			<div id="actioncandidat">
				<h2 class="title">Actions</h2>
                <?php /*echo $pagination;*/?>
				<?php
					echo $this->Default2->index(
						$results['actions'],
						array(
							'Actioncandidat.name',
							'Actioncandidat.codeaction' => array( 'type' => 'text', 'sort' => false ),
							'Chargeinsertion.nom_complet' => array( 'label' => 'Chargé d\'insertion', 'type' => 'text' ),
							'Secretaire.nom_complet' => array( 'label' => 'Secrétaire', 'type' => 'text' ),
							'Actioncandidat.lieuaction',
							'Actioncandidat.cantonaction',
							'Actioncandidat.ddaction',
							'Actioncandidat.dfaction',
							'Actioncandidat.nbpostedispo',
							'Actioncandidat.nbheuredispo',
							'Fichiermodule.nb_fichiers_lies' => array( 'label' => 'Nb fichiers liés', 'type' => 'integer' )
						),
						array(
							'cohorte' => false,
                            'paginate' => Inflector::classify( 'actions' ),
                            'id' => 'actions',
							'actions' => array(
								'Actionscandidats::view' => array( 'url' => array( 'controller' => 'offresinsertion', 'action' => 'view', '#Actioncandidat.id#' ) )
							),
							'options' => $options
						)
					);
				?>
                <?php /*echo $pagination;*/?>
			</div>
			<div id="partenaires">
				<h2 class="title">Partenaires</h2>
                <?php /*echo $pagination;*/?>
				<?php
					echo $this->Default2->index(
						$results['partenaires'],
						array(
							'Partenaire.libstruc',
							'Partenaire.codepartenaire',
							'Partenaire.adresse' => array( 'type' => 'text' ),
							'Partenaire.numtel',
							'Partenaire.numfax'
						),
						array(
							'cohorte' => false,
                            'paginate' => Inflector::classify( 'partenaires' ),
                            'id' => 'partenaires',
							'actions' => array(
								'Actionscandidats::view' => array( 'url' => array( 'controller' => 'offresinsertion', 'action' => 'view', '#Partenaire.id#' ) )
							),
							'options' => $options
						)
					);
				?>
                <?php /*echo $pagination;*/?>
			</div>
			<div id="contacts">
				<h2 class="title">Contacts partenaires</h2>
                <?php /*echo $pagination;*/?>
				<?php
					echo $this->Default2->index(
						$results['contactpartenaires'],
						array(
//                            'Actioncandidat.name',
							'Contactpartenaire.nom_candidat' => array( 'type' => 'text' ),
							'Contactpartenaire.numtel',
							'Contactpartenaire.numfax',
							'Contactpartenaire.email',
                            'Partenaire.libstruc'
						),
						array(
							'cohorte' => false,
                            'paginate' => Inflector::classify( 'contactpartenaires' ),
                            'id' => 'contactpartenaires',
							'actions' => array(
								'Actionscandidats::view' => array( 'url' => array( 'controller' => 'offresinsertion', 'action' => 'view', '#Contactpartenaire.id#' ) )
							),
							'options' => $options
						)
					);
				?>
                <?php /*echo $pagination;*/?>
			</div>
			<div id="actionspartenaires">
				<h2 class="title">Liste d'actions par Partenaires</h2>
                <?php // echo $pagination;?>
				<table class="tooltips">
					<thead>
						<tr>
							<th>Partenaire</th>
							<th>Liste d'actions</th>
							<!--<th class="action">Actions</th>-->
						</tr>
					</thead>
					<tbody>
						<?php
							foreach( $results['actions_par_partenaires'] as $index => $results ) {
								$listeActionscandidats = '';
								foreach( $results['Partenaire']['listeactions'] as $key => $result ) {
									if( !empty( $result ) ) {
										$listeActionscandidats .= $this->Xhtml->tag( 'h3', '' ).'<ul><li>'.$result.'</li></ul>';
									}
								}


								echo $this->Xhtml->tableCells(
									array(
										$results['Partenaire']['libstruc'],
										$listeActionscandidats,
									)
								);
							}
						?>
					</tbody>
				</table>
			</div>
		</div>
		<?php else:?>
			<p class="notice">Vos critères n'ont retourné aucune information.</p>
		<?php endif?>
	<?php endif;?>
</div>

<!-- *********************************************************************** -->

<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->script( 'prototype.livepipe.js' );
		echo $this->Html->script( 'prototype.tabs.js' );
	}
?>
<script type="text/javascript">
	makeTabbed( 'tabbedWrapper', 2 );
</script>