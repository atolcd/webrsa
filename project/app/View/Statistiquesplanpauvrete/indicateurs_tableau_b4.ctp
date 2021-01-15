<?php require_once  dirname( __FILE__ ).DS.'search.ctp' ; ?>
<?php if( !empty( $this->request->data ) ) { ?>
	<h2> <?php echo __d('statistiquesplanpauvrete', 'Statistiquesplanpauvrete.menu.indicateurs_tableau_b4', '') ?> </h2>
	<?php
		$annee = Hash::get( $this->request->data, 'Search.annee' );
		$thead = $this->Xhtml->tag(
			'thead',
			$this->Xhtml->tableHeaders(
				array(
					__d( 'statistiquesplanpauvrete', 'Tableaub4.nbOrientation' ) . $annee,
					__d( 'statistiquesplanpauvrete', 'Tableau.jan' ),
					__d( 'statistiquesplanpauvrete', 'Tableau.feb' ),
					__d( 'statistiquesplanpauvrete', 'Tableau.mar' ),
					__d( 'statistiquesplanpauvrete', 'Tableau.apr' ),
					__d( 'statistiquesplanpauvrete', 'Tableau.may' ),
					__d( 'statistiquesplanpauvrete', 'Tableau.jun' ),
					__d( 'statistiquesplanpauvrete', 'Tableau.jul' ),
					__d( 'statistiquesplanpauvrete', 'Tableau.aug' ),
					__d( 'statistiquesplanpauvrete', 'Tableau.sep' ),
					__d( 'statistiquesplanpauvrete', 'Tableau.oct' ),
					__d( 'statistiquesplanpauvrete', 'Tableau.nov' ),
					__d( 'statistiquesplanpauvrete', 'Tableau.dec' ),
					__d( 'statistiquesplanpauvrete', 'Tableau.Total' )
				)
			)
		);

		$cells = array();
		$row = 0;
		foreach( $results as $key => $result) {
			if( in_array($key, array('Social', 'Prepro', 'Pro') ) ) {
				$cells[$row] = array('<b>' . __d('statistiquesplanpauvrete', 'Tableaub4.' . $key ) . '</b>');
				$row++;
				foreach( $results[$key] as $keySpec => $resultSpec) {
					if($keySpec == 'delai') {
						$cells[$row] = array(__d('statistiquesplanpauvrete', 'Tableaub4.' . $keySpec ));
						$row++;
						foreach( $results[$key][$keySpec] as $keyDelai => $delai) {
							$cells[$row] = $delai;
							$joursDelais = explode('_', $keyDelai);
							$strDelai = '';
							if($joursDelais[0] === '0') {
								$strDelai = str_replace('XX', $joursDelais[1], __d('statistiquesplanpauvrete', 'Tableau_delai.0_XX'));
							}else if($joursDelais[1] === '999') {
								$strDelai = str_replace('XX', $joursDelais[0], __d('statistiquesplanpauvrete', 'Tableau_delai.XX_999'));
							} else {
								$strDelai = str_replace('XX', $joursDelais[0], __d('statistiquesplanpauvrete', 'Tableau_delai.XX_YY'));
								$strDelai = str_replace('YY', $joursDelais[1], $strDelai);
							}
							array_unshift($cells[$row], $strDelai);
							$row++;
						}
					} else {
						$cells[$row] = $resultSpec;
					}
					if( isset($cells[$row]) && is_array($cells[$row]) )
						array_unshift($cells[$row], __d('statistiquesplanpauvrete', 'Tableaub4.' . $key . $keySpec ));
					$row++;
				}
			} else {
				$cells[$row] = $result;
				if( is_array($cells[$row]) )
					array_unshift($cells[$row], __d('statistiquesplanpauvrete', 'Tableaub4.' . $key ));
				$row++;
			}
		}
		$tbody = $this->Xhtml->tag( 'tbody', $this->Xhtml->tableCells( $cells ) );

		echo $this->Xhtml->tag( 'table', $thead.$tbody ,array( 'class' => 'first' ) );
		?>
		<ul class="actionMenu">
		<li><?php
			echo $this->Xhtml->exportLink(
				__d('statistiquesplanpauvrete','Statistiquesplanpauvrete.telecharger.csv'),
				array( 'action' => 'exportcsv_tableau_b4', 'visualisation' ) + Hash::flatten( $this->request->data, '__' ),
				true
			);
		?></li>
		</ul>
		<?php
	}
