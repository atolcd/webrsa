<h1><?php	echo $this->pageTitle = 'Affichage de la COV'; ?></h1>
<div  id="ficheCI">
	<ul class="actionMenu">
	<?php
		if( in_array( $cov58['Cov58']['etatcov'], array( 'associe', 'cree' ) ) ) {
			echo '<li>'.$this->Xhtml->editLink(
				__d( 'cov58', 'Cov58.edit' ),
				array( 'controller' => 'covs58', 'action' => 'edit', $cov58['Cov58']['id'] ),
				$this->Permissions->check( 'covs58', 'edit' )
			).' </li>';
		}
		else {
			echo '<li><span class="disabled"> Modifier</span></li>';
		}

		if( $countDossiers > 0 ) {
			echo '<li>'.$this->Xhtml->link(
				__d( 'cov58','Covs58::ordredujour' ),
				array( 'controller' => 'covs58', 'action' => 'ordredujour', $cov58['Cov58']['id'] ),
				array( 'enabled' => $this->Permissions->check( 'covs58', 'ordredujour' ) )
			).' </li>';
		}
		else {
			echo '<li><span class="disabled"> '.__d( 'cov58','Covs58::ordredujour' ).'</span></li>';
		}

		if( $cov58['Cov58']['etatcov'] != 'finalise' && $countDossiers > 0 ) {
			echo '<li>'.$this->Xhtml->link(
				__d( 'cov58','Covs58::decisioncov' ),
				array( 'controller' => 'covs58', 'action' => 'decisioncov', $cov58['Cov58']['id'] ),
				array( 'enabled' => $this->Permissions->check( 'covs58', 'decisioncov' ) )
			).' </li>';
		}
		else {
			echo '<li><span class="disabled"> '.__d( 'cov58','Covs58::decisioncov' ).'</span></li>';
		}

		if( $cov58['Cov58']['etatcov'] == 'finalise' ) {
			echo '<li>'.$this->Xhtml->link(
				__d( 'cov58','Covs58::impressionpv' ),
				array( 'controller' => 'covs58', 'action' => 'impressionpv', $cov58['Cov58']['id'] ),
				array( 'enabled' => $this->Permissions->check( 'covs58', 'impressionpv' ) )
			).' </li>';
		}
		else {
			echo '<li><span class="disabled"> '.__d( 'cov58','Covs58::impressionpv' ).'</span></li>';
		}
	?>
	</ul>
	<table>
		<tbody>
			<tr class="odd">
				<th><?php echo __d( 'cov58', 'Cov58.datecommission' );?></th>
				<td><?php echo isset( $cov58['Cov58']['datecommission'] ) ? strftime( '%d/%m/%Y à %H:%M', strtotime( $cov58['Cov58']['datecommission'])) : null ;?></td>
			</tr>
			<tr class="even">
				<th><?php echo __d( 'cov58', 'Cov58.sitecov58_id' );?></th>
				<td><?php echo isset( $cov58['Sitecov58']['name'] ) ? $cov58['Sitecov58']['name'] : null ;?></td>
			</tr>
			<tr class="odd">
				<th><?php echo __d( 'cov58', 'Cov58.lieu' );?></th>
				<td><?php echo isset( $cov58['Cov58']['lieu'] ) ? $cov58['Cov58']['lieu'] : null ;?></td>
			</tr>
			<tr class="even">
				<th><?php echo __d( 'cov58', 'Cov58.observation' );?></th>
				<td><?php echo isset( $cov58['Cov58']['observation'] ) ? $cov58['Cov58']['observation'] : null ;?></td>
			</tr>
			<tr class="odd">
				<th><?php echo __d( 'cov58', 'Cov58.etatcov' );?></th>
				<td><?php echo isset( $cov58['Cov58']['etatcov'] ) ? __d ( 'cov58', 'ENUM::ETATCOV::'.$cov58['Cov58']['etatcov'] ) : null ;?></td>
			</tr>
		</tbody>
	</table>
</div>
<br />
<div id="tabbedWrapper" class="tabs">
	<div id="dossiers">
		<h2 class="title">Liste des dossiers</h2>
		<ul class="actionMenu">
			<?php
				if( in_array( $cov58['Cov58']['etatcov'], array( 'associe', 'cree' ) ) ) {
					echo '<li>'.$this->Xhtml->affecteLink(
						'Affecter les dossiers',
						array( 'controller' => 'dossierscovs58', 'action' => 'choose', Set::classicExtract( $cov58, 'Cov58.id' ) ),
						$this->Permissions->check( 'dossierscovs58', 'choose' )
					).' </li>';
				}
				else {
					echo '<li><span class="disabled"> Affecter les dossiers</span></li>';
				}
			?>
		</ul>
		<div id="dossierscovs">
			<?php
				$covTraitee = ( $cov58['Cov58']['etatcov'] == 'finalise' );

				foreach( $themes as $theme ) {
					// S'il s'agit d'une ancienne thématique pour laquelle il n'existe pas de dossier, on n'affiche pas l'onglet
					if( !in_array( Inflector::pluralize( $theme ), $options['Dossiercov58']['vx_themecov58'] ) || !empty( $dossiers[$theme] ) ) {
						$controller = 'orientsstructs';
						if( $theme == 'propocontratinsertioncov58' ){
							$controller = 'contratsinsertion';
						}

						$class = Inflector::classify( $theme );

						echo "<div id=\"$class\"><h3 class=\"title\">".__d( 'dossiercov58', 'ENUM::THEMECOV::'.$theme )."</h3>";

						echo $this->Default2->index(
							$dossiers[$theme],
							array(
								'Personne.qual',
								'Personne.nom',
								'Personne.prenom',
								'Personne.dtnai',
								'Adresse.nomcom',
								'Passagecov58.etatdossiercov'/* => array( 'value' => $options['Passagecov58']['etatdossiercov'] )*/
							),
							array(
								'actions' => array(
									'Dossierscovs58::view' => array(
										'label' => 'Voir',
										'url' => array( 'controller' => $controller, 'action' => 'index', '#Personne.id#' ),
										'class' => 'external',
										'target' => '_blank',
									),
									'Dossierscovs58::impressiondecision' => array(
										'label' => 'Imprimer la décision',
										'url' => array( 'controller' => 'covs58', 'action' => 'impressiondecision', '#Passagecov58.id#' ),
										'disabled' => '( "#Passagecov58.etatdossiercov#" != "traite" ) || ( "'.$theme.'" == "propocontratinsertioncov58" )'
									)
								),
								'options' => $options
							)
						);
						echo "</div>";
					}
				}
			?>
		</div>
	</div>
</div>

<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->script( 'prototype.livepipe.js' );
		echo $this->Html->script( 'prototype.tabs.js' );
	}
?>
<script type="text/javascript">
//<![CDATA[
	document.observe( "dom:loaded", function() {
		makeTabbed( 'dossierscovs', 3 );

		observeOnclickUrlFragments( 'ul.ui-tabs-nav li.tab a', '#dossiers ul.actionMenu li a', 'tabbedWrapper' );
		observeOnloadUrlFragments( '#dossiers ul.actionMenu li a', 'tabbedWrapper' );
	});
//]]>
</script>