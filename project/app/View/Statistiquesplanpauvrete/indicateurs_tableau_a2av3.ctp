<?php require_once  dirname( __FILE__ ).DS.'search.ctp' ; ?>
<?php if( !empty( $this->request->data ) ) { ?>

	<?php
		$annee = Hash::get( $this->request->data, 'Search.annee' );

			$thead = $this->Xhtml->tag(
				'thead',
				$this->Xhtml->tableHeaders(
					array(
						__d( 'statistiquesplanpauvrete', 'Tableaua2a2.title' ). $annee,
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
						__d( 'statistiquesplanpauvrete', 'Tableau.dec' )
					)
				)
			);
			$cells = array();
			$row = 0;
			foreach( $results as $key => $result) {
				if( $key == 'Orientes' || $key == 'Orientes15j') {
					$cells[$row] = array('<b>' . __d('statistiquesplanpauvrete', 'tableaua2a2.' . $key ) . '</b>');
					$row++;
					foreach( $results[$key] as $keySpec => $resultSpec) {
							$cells[$row] = $resultSpec;
						if( isset($cells[$row]) && is_array($cells[$row]) )
							array_unshift($cells[$row], __d('statistiquesplanpauvrete', 'tableaua2a2.' . $key . $keySpec ));
						$row++;
					}
				} else {
					$cells[$row] = $result;
					if( is_array($cells[$row]) )
						array_unshift($cells[$row], __d('statistiquesplanpauvrete', 'tableaua2a2.' . $key ));
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
				array( 'action' => 'exportcsv_tableau_a2a3', 'visualisation' ) + Hash::flatten( $this->request->data, '__' ),
				true
			);
		?></li>
	<?php
	}
