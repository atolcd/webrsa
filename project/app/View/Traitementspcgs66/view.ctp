<?php
	$this->pageTitle =  __d( 'traitementpcg66', "Traitementspcgs66::{$this->action}" );
?>

	<?php
		echo $this->Xhtml->tag( 'h1', $this->pageTitle );
        echo $this->Form->create( 'Traitementpcg66', array( 'type' => 'post', 'id' => 'traitementpcg66form', 'novalidate' => true ) );
        if( ( $traitementpcg66['Traitementpcg66']['annule'] == 'O' ) ){

			echo $this->Xhtml->tag('div', $this->Xhtml->tag('strong', 'Raison de l\'annulation'));
			echo $this->Default->view(
				$traitementpcg66,
				array(
					'Traitementpcg66.motifannulation' => array( 'type' => 'text' )
				),
				array(
					'widget' => 'table',
					'class' => 'aere'
				)
			);

		}

		echo $this->Default2->view(
			$traitementpcg66,
			array(
				'Descriptionpdo.name',
				'Traitementpcg66.datereception',
				'Traitementpcg66.datedepart',
				'Traitementpcg66.dateecheance',
				'Traitementpcg66.daterevision',
				'Personne.nom_complet' => array( 'type' => 'string', 'value' => '#Personnepcg66.Personne.qual# #Personnepcg66.Personne.nom# #Personnepcg66.Personne.prenom#' )
			),
			array(
				'class' => 'aere'
			)
		);

        if( $traitementpcg66['Traitementpcg66']['typetraitement'] == 'courrier' ) {
            echo $this->Default2->view(
                $traitementpcg66,
                array(
                    'Traitementpcg66.dateenvoicourrier'
                ),
                array(
                    'class' => 'aere'
                )
            );
        }

		echo "<h2>Liste de courriers liés au traitement</h2>";
		if( !empty( $traitementpcg66['Courrierpdo'] ) ){
			$courriersLies = Set::extract( $traitementpcg66, 'Traitementpcg66/Courrierpdo' );
			echo '<table><tbody>';
				echo '<tr><th>Intitulé du courrier</th><th>Action</th></tr>';
				if( isset( $courriersLies ) ){
					foreach( $courriersLies as $i => $courriers ){
						echo '<tr><td>'.$courriers['Courrierpdo']['name'].'</td>';
						echo '<td>'.$this->Xhtml->link( 'Imprimer', array( 'action' => 'printCourrier', $courriers['Courrierpdo']['CourrierpdoTraitementpcg66']['id']    ) ).'</td></tr>';
					}
				}
			echo '</tbody></table>';
		}
		else{
			echo '<p class="notice">Aucun élément.</p>';
		}

		echo "<h2>Pièces jointes</h2>";
		echo $this->Fileuploader->results( Set::classicExtract( $traitementpcg66, 'Fichiermodule' ) );

		echo '<h2>'.__d( 'traitementpcg66', 'Traitementpcg66.ficheanalyse' ).'</h2>';
		if( !empty( $traitementpcg66['Traitementpcg66']['typetraitement'] ) && ( $traitementpcg66['Traitementpcg66']['typetraitement'] != 'analyse' ) ){
			echo '<p class="notice">Pas de fiche d\'analyse.</p>';
		}
		else {
			echo "<p>".nl2br( $traitementpcg66['Traitementpcg66']['ficheanalyse'] )."</p>";
		}

		echo '<h2>'.__d( 'traitementpcg66', 'Traitementpcg66.hasrevenu' ).'</h2>';
		if( !empty( $traitementpcg66['Traitementpcg66']['typetraitement'] ) && ( $traitementpcg66['Traitementpcg66']['typetraitement'] != 'revenu' ) ) {
			echo '<p class="notice aere">Pas de revenu.</p>';
		}
		else {
			$regime = Set::enum( $traitementpcg66['Traitementpcg66']['regime'], $options['Traitementpcg66']['regime'] );

			switch( $traitementpcg66['Traitementpcg66']['regime'] ) {
				case 'microbnc':
					echo $this->Default2->view(
						$traitementpcg66,
						array(
							'Traitementpcg66.regime' => array( 'type' => 'text', 'value' => $regime ),
							'Traitementpcg66.saisonnier' => array( 'type' => 'boolean' ),
							'Traitementpcg66.nrmrcs',
							'Traitementpcg66.dtdebutactivite',
							'Traitementpcg66.raisonsocial',
							'Traitementpcg66.dtdebutperiode',
							'Traitementpcg66.datefinperiode',
							'Traitementpcg66.nbmoisactivite',
							'Traitementpcg66.chaffsrv',
							// FIXME: calculs javascript
							'Traitementpcg66.aidesubvreint',
							'Traitementpcg66.benefpriscompte',
							'Traitementpcg66.revenus',
							'Traitementpcg66.dtdebutprisecompte',
							'Traitementpcg66.datefinprisecompte',
							'Traitementpcg66.dateecheance',
						)
					);
					break;
				case 'microbic':
				case 'microbicauto':
					echo $this->Default2->view(
						$traitementpcg66,
						array(
							'Traitementpcg66.regime' => array( 'type' => 'text', 'value' => $regime ),
							'Traitementpcg66.saisonnier' => array( 'type' => 'boolean' ),
							'Traitementpcg66.nrmrcs',
							'Traitementpcg66.dtdebutactivite',
							'Traitementpcg66.raisonsocial',
							'Traitementpcg66.dtdebutperiode',
							'Traitementpcg66.datefinperiode',
							'Traitementpcg66.nbmoisactivite',
							'Traitementpcg66.chaffvnt',
							'Traitementpcg66.chaffsrv',
							// FIXME: calculs javascript
							'Traitementpcg66.aidesubvreint',
							'Traitementpcg66.benefpriscompte',
							'Traitementpcg66.revenus',
							'Traitementpcg66.dtdebutprisecompte',
							'Traitementpcg66.datefinprisecompte',
							'Traitementpcg66.dateecheance',
						)
					);
					break;
				case 'microbicagri':
					echo $this->Default2->view(
						$traitementpcg66,
						array(
							'Traitementpcg66.regime' => array( 'type' => 'text', 'value' => $regime ),
							'Traitementpcg66.saisonnier' => array( 'type' => 'boolean' ),
							'Traitementpcg66.nrmrcs',
							'Traitementpcg66.dtdebutactivite',
							'Traitementpcg66.raisonsocial',
							'Traitementpcg66.dtdebutperiode',
							'Traitementpcg66.datefinperiode',
							'Traitementpcg66.nbmoisactivite',
							'Traitementpcg66.chaffvnt',
							'Traitementpcg66.chaffsrv',
							'Traitementpcg66.chaffagri',
							// FIXME: calculs javascript
							'Traitementpcg66.aidesubvreint',
							'Traitementpcg66.benefpriscompte',
							'Traitementpcg66.revenus',
							'Traitementpcg66.dtdebutprisecompte',
							'Traitementpcg66.datefinprisecompte',
							'Traitementpcg66.dateecheance',
						)
					);
					break;
				case 'reel':
				case 'ragri':
					echo $this->Default2->view(
						$traitementpcg66,
						array(
							'Traitementpcg66.regime' => array( 'type' => 'text', 'value' => $regime ),
							'Traitementpcg66.saisonnier' => array( 'type' => 'boolean' ),
							'Traitementpcg66.nrmrcs',
							'Traitementpcg66.dtdebutactivite',
							'Traitementpcg66.raisonsocial',
							'Traitementpcg66.dtdebutperiode',
							'Traitementpcg66.datefinperiode',
							'Traitementpcg66.nbmoisactivite',
							'Traitementpcg66.chaffvnt',
							'Traitementpcg66.chaffsrv',
							'Traitementpcg66.benefoudef',
							'Traitementpcg66.ammortissements',
							'Traitementpcg66.salaireexploitant',
							'Traitementpcg66.provisionsnonded',
							'Traitementpcg66.moinsvaluescession',
							'Traitementpcg66.autrecorrection',
							// FIXME: calculs javascript
							'Traitementpcg66.mnttotalpriscompte',
							'Traitementpcg66.revenus',
							'Traitementpcg66.dtdebutprisecompte',
							'Traitementpcg66.datefinprisecompte',
							'Traitementpcg66.dateecheance',
						)
					);
					break;
				case 'fagri':
					echo $this->Default2->view(
						$traitementpcg66,
						array(
							'Traitementpcg66.regime' => array( 'type' => 'text', 'value' => $regime ),
							'Traitementpcg66.saisonnier' => array( 'type' => 'boolean' ),
							'Traitementpcg66.nrmrcs',
							'Traitementpcg66.dtdebutactivite',
							'Traitementpcg66.raisonsocial',
							'Traitementpcg66.dtdebutperiode',
							'Traitementpcg66.datefinperiode',
							'Traitementpcg66.nbmoisactivite',
							'Traitementpcg66.forfait',
							'Traitementpcg66.aidesubvreint' => array( 'type' => 'text', 'value' => Set::enum( $traitementpcg66['Traitementpcg66']['aidesubvreint'], $options['Traitementpcg66']['aidesubvreint'] ) ),
							// FIXME: calculs javascript
							'Traitementpcg66.mnttotalpriscompte',
							'Traitementpcg66.revenus',
							'Traitementpcg66.dtdebutprisecompte',
							'Traitementpcg66.datefinprisecompte',
							'Traitementpcg66.dateecheance',
						)
					);
					break;
			}
		}
	?>
<div class="submit">
	<?php
		echo $this->Form->submit( 'Retour', array( 'name' => 'Cancel', 'div' => false ) );
	?>
</div>
<?php echo $this->Form->end();?>