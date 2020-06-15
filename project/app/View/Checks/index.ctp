<?php $extratabs = array();?>
<h1><?php echo $this->pageTitle = 'Vérification de l\'application'; ?></h1>
<br />
<div id="tabbedWrapper" class="tabs">
	<div id="software">
		<h2 class="title">Environnement logiciel</h2>
		<div id="tabbedWrapperSoftware" class="tabs">
			<div id="binaries">
				<h3 class="title">Binaires</h3>
				<?php echo $this->Checks->table( $results['Environment']['binaries'] );?>
			</div>
			<div id="cakephp">
				<h3 class="title">CakePHP</h3>
				<h4>Informations</h4>
				<?php echo $this->Checks->table( $results['Cakephp']['informations'] );?>
				<h4>Durées de cache</h4>
				<?php echo $this->Checks->table( $results['Cakephp']['cache'] );?>
			</div>
			<div id="directories">
				<h3 class="title">Installation</h3>
				<h4>Répertoires</h4>
				<?php echo $this->Checks->table( $results['Environment']['directories'] );?>
				<h4>Fichiers</h4>
				<?php echo $this->Checks->table( $results['Environment']['files'] );?>
				<h4>Cache</h4>
				<?php echo $this->Checks->table( $results['Environment']['cache'] );?>
				<h4>Accès au cache</h4>
				<?php echo $this->Checks->table( $results['Environment']['cache_check'] );?>
				<h4>Espace libre</h4>
				<?php echo $this->Checks->table( $results['Environment']['freespace'] );?>
			</div>
			<div id="php">
				<h3 class="title">PHP</h3>
				<?php echo $this->Checks->table( $results['Php']['informations'] );?>
				<h4>Configuration</h4>
				<?php echo $this->Checks->table( $results['Php']['inis'] );?>
				<h4>Extensions</h4>
				<?php echo $this->Checks->table( $results['Php']['extensions'] );?>
			</div>
			<div id="postgresql">
				<h3 class="title">PostgreSQL</h3>
				<?php echo $this->Checks->table( $results['Postgresql'] );?>
			</div>
			<div id="webrsa">
				<h3 class="title">WebRSA</h3>
				<?php echo $this->Checks->table( $results['Webrsa']['informations'] );?><br/>
				<div id="tabbedWrapperWebrsa" class="tabs">
					<div id="webrsa_configuration">
						<h4 class="title">Configuration</h4>
						<?php echo $this->Checks->table( $results['Webrsa']['configure'] );?>
					</div>
					<div id="webrsa_pgsqlintervals">
						<h4 class="title">Intervalles PostgreSQL</h4>
						<?php echo $this->Checks->table( $results['Webrsa']['intervals'] );?>
					</div>
					<?php if( !is_null( Configure::read( "Recherche.qdFilters" ) ) ):?>
					<div id="webrsa_sqrecherche">
						<h4 class="title">Fragments SQL pour les moteurs de recherche</h4>
						<?php
							foreach( $results['Webrsa']['sqRechercheErrors'] as $modelName => $entries ) {
								$errorClass = ( empty( $entries ) ? '' : 'error' );
								echo "<h5 class=\"title {$errorClass}\">{$modelName}</h5>";
								$controllerName = Inflector::camelize( Inflector::tableize( $modelName ) );
								echo $this->Default2->index(
									$entries,
									array(
										"{$modelName}.id" => array( 'type' => 'integer' ),
										"{$modelName}.name" => array( 'type' => 'string' ),
										"{$modelName}.sqrecherche" => array( 'type' => 'string' ),
										"{$modelName}.message" => array( 'type' => 'string' ),
									),
									array(
										'actions' => array(
											"{$controllerName}::edit" => array( 'class' => 'external' ),
										)
									)
								);
							}
						?>
					</div>
					<?php endif;?>
					<div id="webrsa_configure_querydata_fragments">
						<h4 class="title">Morceaux de querydata dans le webrsa.inc</h4>
						<?php
							foreach( $results['Webrsa']['querydata_fragments_errors'] as $modelName => $entries ) {
								$errorClass = ( !in_array( false, Hash::extract( $entries, '{s}.success' ), true ) ? '' : 'error' );
								echo "<h5 class=\"title {$errorClass}\">{$modelName}</h5>";
								echo $this->Checks->table( $entries );
							}
						?>
					</div>
					<div id="webrsa_configure_primary_key">
						<h4 class="title">Clés primaires dans le webrsa.inc</h4>
						<?php echo $this->Checks->table( $results['Webrsa']['configure_primary_key'] );?>
					</div>
					<div id="webrsa_configure_regexps">
						<h4 class="title">Expressions rationnelles dans le webrsa.inc</h4>
						<?php echo $this->Checks->table( $results['Webrsa']['configure_regexps'] );?>
					</div>
					<div id="webrsa_configure_fields">
						<h4 class="title">Champs spécifiés dans le webrsa.inc</h4>
						<?php echo $this->Checks->table( $results['Webrsa']['configure_fields'] );?>
					</div>
					<div id="webrsa_ini_set">
						<h4 class="title">Paramétrages ini_set dans le webrsa.inc en fonction du contrôleur et de l'action</h4>
						<?php echo $this->Checks->table( $results['Webrsa']['ini_set'] );?>
					</div>
					<div id="webrsa_configure_badKeys">
						<h4 class="title">Clefs non valide webrsa.inc</h4>
						<?php echo $this->Checks->table( $results['Webrsa']['configure_bad_keys'] );?>
					</div>
					<div id="webrsa_configurable_query">
						<h4 class="title">ConfigurableQuery</h4>
						<div id="tabbedWrapperWebrsaConfigurableQuery" class="tabs">
							<?php
								$foos = array();
								foreach( $results['Webrsa']['configurable_query'] as $key => $result ) {
									list( $controller, $action ) = explode( '.', $key );
									if( !isset( $foos[$controller] ) ) {
										$foos[$controller] = array();
									}
									$foos[$controller][$action] = $result;
								}

								foreach( $foos as $controller => $params ) {
									$id = 'webrsa_configurable_query_'.Inflector::underscore( $controller );
									echo "<div id=\"{$id}\">\n";
									echo "<h5 class=\"title\">{$controller}</h5>\n";

									$id = "tabbedWrapperWebrsaConfigurableQuery{$controller}";
									echo "<div id=\"{$id}\" class=\"tabs\">\n";
									$extratabs[$id] = 6;

									foreach( $params as $action => $result ) {
										$id = 'webrsa_configurable_query_'.Inflector::underscore( $controller ).'_'.Inflector::underscore( $action );
										echo "<div id=\"{$id}\">\n";
										echo "<h6 class=\"title\">{$action}</h6>\n";
										$checks = array_merge(
											$result['config'],
											array(
												'fields' => $result['fields'],
												'query' => $result['query']
											)
										);
										foreach( array( 'cohorte_options', 'cohorte_values' ) as $key ) {
											if( isset( $result[$key] ) ) {
												$checks[$key] = $result[$key];
											}
										}
										echo $this->Checks->table( $checks );
										echo "</div>";
									}

									echo "</div>\n";

									echo "</div>\n";
								}
							?>
						</div>
					</div>
					<div id="webrsa_configure_evidence">
						<h4 class="title">Champs de formulaires mis en évidence</h4>
						<div id="tabbedWrapperWebrsaConfigureEvidence" class="tabs">
							<?php
								$tabs = array();
								$paths = array_keys( $results['Webrsa']['configure_evidence'] );
								foreach( $paths as $path ) {
									list( $controller, $action ) = model_field( $path, false );
									if( !isset( $tabs[$controller] ) ) {
										$tabs[$controller] = array();
									}
									$tabs[$controller][] = $path;
								}

								foreach( $tabs as $controller => $subtabs ) {
									$id = 'webrsa_configure_evidence_'.Inflector::underscore( $controller );
									echo "<div id=\"{$id}\">\n";
									echo "<h5 class=\"title\">{$controller}</h5>\n";
									$configs = array();
									foreach( $subtabs as $subtab ) {
										$configs[$subtab] = $results['Webrsa']['configure_evidence'][$subtab];
									}
									echo $this->Checks->table( $configs );
									echo "</div>\n";
								}
							?>
						</div>
					</div>
					<div id="webrsa_configure_tableaux_conditions">
						<h4 class="title">Conditions pour les tableaux</h4>
						<div id="tabbedWrapperWebrsaConfigureTableauxConditions" class="tabs">
							<?php
								$departement = Configure::read( 'Cg.departement' );

								foreach( $results['Webrsa']['tableaux_conditions'] as $modelName => $modelResults ) {
									$id = 'webrsa_configure_tableaux_conditions_'.Inflector::underscore( $modelName );
									echo "<div id=\"{$id}\">\n";
									echo "<h5 class=\"title\">".__m( $modelName )."</h5>\n";

									foreach( $modelResults as $label => $data ) {
										$id = 'webrsa_configure_tableaux_conditions_'.Inflector::underscore( $modelName ).'_'.Inflector::slug( $label);
										echo "<div id=\"{$id}\">\n";
										echo "<h6 class=\"title\">".__m( $label )."</h6>\n";

										$fields = $data['fields'];
										$data = $data['records'];

										// Création des lignes du tableau
										$rows = array();
										foreach( $data as $label => $records ) {
											if( !empty( $records ) ) {
												foreach( $records as $record ) {
													$row = array( array( $label, array( 'rowspan' => 1 ) ) );
													foreach( $fields as $field ) {
														$row[] = array( Hash::get( $record, $field ), array( 'rowspan' => 1 ) );
													}
													$rows[] = $row;
												}
											}
											else {
												$row = array( array( $label, array( 'rowspan' => 1 ) ) );
												foreach( $fields as $field ) {
													$row[] = array( null, array( 'rowspan' => 1 ) );
												}
												$rows[] = $row;
											}
										}

										// Calcul des rowspan pour chacune des cellules du tableau
										$subkeys = array_reverse( array_keys( $fields ) );
										foreach( array_reverse( array_keys( $rows ) ) as $key ) {
											if( $key > 0 ) {
												foreach( $subkeys as $subkey ) {
													$same = true;
													for( $i = $subkey ; $i >= 0 ; $i-- ) {
														$same = $same && ( $rows[$key-1][$i][0] === $rows[$key][$i][0] );
													}
													if( $same ) {
														$rows[$key-1][$subkey][1]['rowspan'] += $rows[$key][$subkey][1]['rowspan'];
														unset( $rows[$key][$subkey] );
													}
												}
											}
										}

										// Affichage du tableau
										echo '<table>';
										echo '<thead>';
										echo '<tr>';
										foreach( array_merge( array( 'Intitulé' ), $fields ) as $field ) {
											echo '<th>'.__m( $field ).'</th>';
										}
										echo '</tr>';
										echo '</thead>';
										echo '<tbody>';
										echo $this->Html->tableCells( $rows );
										echo '</tbody>';
										echo '</table>';
										echo '</div>';
									}

									echo '</div>';
								}
							?>
						</div>
					</div>
					<div id="webrsa_access">
						<h4 class="title">Contrôle des accès métier</h4>
						<?php echo $this->Checks->table( $results['Webrsa']['webrsa_access'] );?>
					</div>
					<div id="acos">
						<h4 class="title">Contrôle des acos</h4>
						<?php echo $this->Checks->table( $results['Webrsa']['acos'] );?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="modeles">
		<h2 class="title">Modèles de documents</h2>
		<h3>Paramétrables</h3>
		<?php echo $this->Checks->table( $results['Modelesodt']['parametrables'] );?>
		<h3>Statiques</h3>
		<?php echo $this->Checks->table( $results['Modelesodt']['statiques'] );?>
	</div>
	<div id="data">
		<h2 class="title">Données stockées en base</h2>
		<?php foreach( $results['Storeddata']['errors'] as $tablename => $errors ):?>
		<h3 class="storeddata <?php echo  count( $errors ) > 0 ? 'error' : 'success' ;?>"><?php echo h( $tablename );?></h3>
		<?php
			$fields = array();
			$controllerName = Inflector::camelize( $tablename );
			$modelName = Inflector::classify( $controllerName );

			if( count( $errors ) > 0 ) {
				$fields = Hash::normalize( array_keys( Hash::flatten( $errors[0] ) ) );
			}
			$errorFields = array();
			foreach( array_keys( $fields ) as $fieldName ) {
				if( false !== strpos( $fieldName, '.error_' ) ) {
					unset( $fields[$fieldName] );
					$errorFields[$fieldName] = array( 'type' => 'boolean' );
				}
			}
			$fields = array_merge( $fields, $errorFields );

			if( true === array_key_exists( "{$modelName}.id", $fields ) ) {
				$fields[] = "/{$controllerName}/edit/#{$modelName}.id#";
			}

			echo $this->Default3->index(
				$errors,
				$this->Translator->normalize( $fields ),
				array(
					'paginate' => false,
					'empty_label' => 'Aucun enregistrement erroné'
				)
			);
		?>
		<?php endforeach;?>
	</div>
	<div id="services">
		<h2 class="title">Services</h2>
		<?php foreach( $results['Services'] as $serviceName => $serviceResults ):?>
			<h3><?php echo h( $serviceName );?></h3>
			<?php if( !empty( $serviceResults['configure'] ) ):?>
				<h4>Configuration</h4>
				<?php echo $this->Checks->table( $serviceResults['configure'] );?>
			<?php endif;?>
			<?php if( !empty( $serviceResults['tests'] ) ):?>
				<h4>Tests</h4>
				<?php echo $this->Checks->table( $serviceResults['tests'] );?>
			<?php endif;?>
		<?php endforeach;?>
	</div>
	<?php if( !empty( $results['Emails'] ) ): ?>
	<div id="emails">
		<h2 class="title">Emails</h2>
		<?php foreach( $results['Emails'] as $emailName => $emailResults ):?>
			<h3><?php echo h( $emailName );?></h3>
			<?php if( !empty( $emailResults['configure'] ) ):?>
				<h4>Configuration</h4>
				<?php echo $this->Checks->table( $emailResults['configure'] );?>
			<?php endif;?>
			<?php if( !empty( $emailResults['tests'] ) ):?>
				<h4>Tests</h4>
				<?php echo $this->Checks->table( $emailResults['tests'] );?>
			<?php endif;?>
		<?php endforeach;?>
	</div>
	<?php endif;?>
</div>
<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->script( 'prototype.livepipe.js' );
		echo $this->Html->script( 'prototype.tabs.js' );
	}
?>
<script type="text/javascript">
	// On tronque la longueur des titres à 25 caractères avant de faire les onglets.
	$$( 'h2.title, h3.title, h4.title, h5.title, h6.title' ).each( function( title ) { truncateWithEllipsis( title, 25 ); } );

	// Création des onglets à partir des titres.
	makeTabbed( 'tabbedWrapper', 2 );
	makeTabbed( 'tabbedWrapperSoftware', 3 );
	makeTabbed( 'tabbedWrapperWebrsa', 4 );
	makeTabbed( 'tabbedWrapperWebrsaConfigurableQuery', 5 );
	makeTabbed( 'tabbedWrapperWebrsaConfigureEvidence', 5 );
	makeTabbed( 'tabbedWrapperWebrsaConfigureTableauxConditions', 5 );

	<?php foreach( $extratabs as $id => $level ):?>
		<?php echo "makeTabbed( '{$id}', {$level} );\n";?>
	<?php endforeach;?>

	makeErrorTabs();

	ConfigurationParser.vars.infoBlockClass = 'configuration-parser-info-block no-th-background';
	ConfigurationParser.incrustationInfo('table.checks.values th', <?php echo json_encode($configurations);?>);
</script>