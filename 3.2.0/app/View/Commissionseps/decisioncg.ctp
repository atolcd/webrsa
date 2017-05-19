<h1><?php echo $this->pageTitle = '4. Décisions de la commission du '.date('d/m/Y à H:i', strtotime($commissionep['Commissionep']['dateseance'])).' par le CG '; ?></h1>
<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}

	if( $commissionep['Commissionep']['etatcommissionep'] == 'annule' ) {
		echo $this->Xhtml->tag( 'p', "Commission annulée: {$commissionep['Commissionep']['raisonannulation']}", array( 'class' => 'notice' ) );
	}

	if( Configure::read( 'Cg.departement' ) == 93 ) {
		echo '<ul class="actionMenu">';
			echo '<li>'.$this->Xhtml->link(
				__d( 'commissionep', 'Commissionseps::view' ),
				array( 'controller' => 'commissionseps', 'action' => 'view', $commissionep['Commissionep']['id'] ),
				array( 'class' => 'button view external', 'enabled' => $this->Permissions->check( 'commissionseps', 'view' ) )
			).' </li>';
		echo '</ul>';
	}
	else {
		echo '<br />';
	}
?>
<br/>
<div id="tabbedWrapper" class="tabs">
	<?php
		// L'allocataire passe-t'il plusieurs fois dans cette commission
		foreach( $dossiers as $thmeme => $dossiersTmp ) {
			foreach( $dossiersTmp['liste'] as $dossier ) {
				$dossiersAllocataires[$dossier['Personne']['id']][] = $dossier['Dossierep']['themeep'];
			}
		}

		foreach( array_keys( $dossiers ) as $theme ) {
			// S'il s'agit d'une ancienne thématique pour laquelle il n'existe pas de dossier, on n'affiche pas l'onglet
			if( !in_array( Inflector::tableize( $theme ), $options['Dossierep']['vx_themeep'] ) || !empty( $dossiers[$theme]['liste'] ) ) {
				$file = sprintf( 'decisioncg.%s.liste.ctp', Inflector::underscore( $theme ) );
				echo '<div id="'.$theme.'"><h2 class="title">'.__d( 'dossierep', 'ENUM::THEMEEP::'.Inflector::tableize( $theme ) ).'</h2>';
				if( !empty( $dossiers[$theme]['liste'] ) ) {
					include_once  $file ;
				}
				else {
					echo '<p class="notice">Aucun dossier n\'a été traité pour cette thématique.</p>';
				}
				echo '</div>';
			}
		}

		if( Configure::read( 'Cg.departement' )  == 93 ) {
			echo "<div id=\"synthese\"><h2 class=\"title\">Synthèse</h2>";
				if( isset($syntheses) ) {
					echo '<ul class="actions">';
					echo '<li>'.$this->Xhtml->link(
						__d( 'commissionep','Commissionseps::impressionsDecisions' ),
						array( 'controller' => 'commissionseps', 'action' => 'impressionsDecisions', $commissionep['Commissionep']['id'] ),
						array( 'class' => 'button impressionsDecisions', 'enabled' => ( $commissionep['Commissionep']['etatcommissionep'] != 'annule' ) && $this->Permissions->check( 'commissionseps', 'impressionsDecisions' ) ),
                        'Etes-vous sûr de vouloir imprimer les décisions ?'
					).' </li>';
					echo '</ul>';

					echo $this->Default2->index(
						$syntheses,
						array(
							'Dossierep.Personne.qual',
							'Dossierep.Personne.nom',
							'Dossierep.Personne.prenom',
							'Dossierep.Personne.dtnai',
							'Dossierep.Personne.Foyer.Adressefoyer.0.Adresse.nomcom',
							'Dossierep.created',
							'Dossierep.themeep',
							'Passagecommissionep.etatdossierep',
						),
						array(
							'actions' => array(
								'Dossierseps::view' => array( 'label' => 'Voir', 'url' => array( 'controller' => 'historiqueseps', 'action' => 'view_passage', '#Passagecommissionep.id#' ), 'class' => 'external' ),
								'Commissionseps::impressionDecision' => array(
									'url' => array( 'controller' => 'commissionseps', 'action' => 'impressionDecision',  '#Passagecommissionep.id#' ),
									'disabled' => ( !$this->Permissions->check( 'commissionseps', 'impressionDecision' ) || $commissionep['Commissionep']['etatcommissionep'] == 'annule' )
								)
							),
							'options' => $options,
							'id' => $theme,
							'trClass' => array(
								'eval' => 'count($dossiersAllocataires[#Dossierep.personne_id#]) > 1 ? "multipleDossiers" : null',
								'params' => array( 'dossiersAllocataires' => $dossiersAllocataires )
							),
						)
					);
				}
				else {
					echo '<p class="notice">Il n\'existe aucun dossier associé à cette commission d\'EP.</p>';
				}
			echo "</div>";
		}

		echo $this->Default->button(
			'back',
			array(
				'controller' => 'commissionseps',
				'action'     => 'view',
				$commissionep['Commissionep']['id']
			),
			array(
				'id' => 'Back'
			)
		);
	?>
</div>

<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->script( 'prototype.livepipe.js' );
		echo $this->Html->script( 'prototype.tabs.js' );
	}
?>

<script type="text/javascript">
	makeTabbed( 'tabbedWrapper', 2 );
</script>