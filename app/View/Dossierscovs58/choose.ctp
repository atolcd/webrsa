<?php echo $this->element( 'required_javascript' );?>

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

				$requestParams = $this->request->params;

				foreach( $themesChoose as $theme ){
					// S'il s'agit d'une ancienne thématique pour laquelle il n'existe pas de dossier, on n'affiche pas l'onglet
					if( !in_array( Inflector::pluralize( $theme ), $options['Dossiercov58']['vx_themecov58'] ) || !empty( $dossiers[$theme] ) ) {
						$class = Inflector::singularize( $theme );
						$className = Inflector::classify( $theme );
						echo "<div id=\"$className\"><h3 class=\"title\">".__d( 'dossiercov58',  'ENUM::THEMECOV::'.Inflector::tableize( $theme ) )."</h3>";
						//------------------------------------------------------
						$this->request->params = array(
							'plugin' => $requestParams['plugin'],
							'controller' => $requestParams['controller'],
							'action' => $requestParams['action'],
							'named' => array(),
							'pass' => $requestParams['pass'],
							'paging' => array(
								$className => Hash::get($requestParams, "paging.{$className}")
							),
							'models' => $requestParams['models']
						);
						$this->request->params['named']['page'] = Hash::get($requestParams, "paging.{$className}.page");
						$order = Hash::get($requestParams, "paging.{$className}.order");

						$this->request->params['named']['sort'] = true === is_array( $order ) ? Hash::get( array_keys($order), 0 ) : null;
						$this->request->params['named']['direction'] = true === is_array( $order ) ? Hash::get( $order, $this->request->params['named']['sort'] ) : null;
						//------------------------------------------------------
						include_once  "choose.{$class}.liste.ctp" ;
						$this->Default2->Xpaginator2->options['url'] = array_filter_keys(
							$this->Default2->Xpaginator2->options['url'],
							array( 'page', 'limit', 'sort', 'direction' ),
							false
						);
						//------------------------------------------------------
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
		echo $this->Html->script( 'webrsa.cake.tabbed.paginator.js' );
	}
?>

<script type="text/javascript">
//<![CDATA[
	document.observe( "dom:loaded", function() {
		CakeTabbedPaginator.init('tabbedWrapper', 3);
	});
//]]>
</script>