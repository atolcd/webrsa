<h1>
	<?php
		echo $this->pageTitle = sprintf(
			'Dossiers à passer dans la COV « %s » du %s',
			$cov58['Cov58']['name'],
			$this->Locale->date( 'Locale->datetime', $cov58['Cov58']['datecommission'] )
		);
	?>
</h1>
<br />
<div id="tabbedWrapper" class="tabs">
	<div id="dossierscovs">
		<?php
			if ( isset( $themeEmpty ) && $themeEmpty == true ) {
				echo '<p class="notice">Veuillez attribuer des thèmes à l\'EP gérant la commission avant.</p>';
			}
			else {
				$dossiersAllocataires = array();
				// L'allocataire passe-t'il plusieurs fois dans cette commission
				foreach( $dossiers as $thmeme => $dossiersTmp ) {
					foreach( $dossiersTmp as $dossier ) {
						$dossiersAllocataires[$dossier['Personne']['id']][] = $dossier['Dossiercov58']['themecov58'];
					}
				}
				$trClass = array(
					'eval' => 'count($dossiersAllocataires[#Personne.id#]) > 1 ? "multipleDossiers" : null',
					'params' => array( 'dossiersAllocataires' => $dossiersAllocataires )
				);

				foreach( $themesChoose as $theme ){
					// S'il s'agit d'une ancienne thématique pour laquelle il n'existe pas de dossier, on n'affiche pas l'onglet
					if( !in_array( Inflector::pluralize( $theme ), $options['Dossiercov58']['vx_themecov58'] ) || !empty( $dossiers[$theme] ) ) {
						$class = Inflector::singularize( $theme );
						echo "<div id=\"$theme\"><h3 class=\"title\">".__d( 'dossiercov58',  'ENUM::THEMECOV::'.Inflector::tableize( $theme ) )."</h3>";
						include_once  "choose.{$class}.liste.ctp" ;
						if( !empty( $dossiers[$theme]) ) {
							echo $this->Form->button( 'Tout cocher', array( 'type' => 'button', 'onclick' => "return toutCocher( '#{$theme} input[type=checkbox]' );" ) );
							echo $this->Form->button( 'Tout décocher', array( 'type' => 'button', 'onclick' => "return toutDecocher( '#{$theme} input[type=checkbox]' );" ) );
						}
						echo "</div>";
					}
				}
			}
		?>
	</div>
</div>
<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->script( 'prototype.livepipe.js' );
		echo $this->Html->script( 'prototype.tabs.js' );
	}
?>

<script type="text/javascript">
	makeTabbed( 'tabbedWrapper', 3 );

	// Permet de rester sur le bon onglet lorsqu'on trie sur une colonne
	$$( '#dossierscovs > div' ).each( function(tab) {
		$(tab).getElementsBySelector( 'table thead a' ).each( function(link) {
			$(link).writeAttribute( 'href', $(link).readAttribute( 'href' ) + '#' + $(tab).readAttribute( 'id' ) );
		} );
	} );
</script>