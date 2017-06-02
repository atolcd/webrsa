<?php
	/**
	 * Code source de la classe Tableaud2Helper.
	 *
	 * PHP 5.3
	 *
	 * @package app.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppHelper', 'View/Helper' );

	/**
	 * La classe Tableaud2Helper fournit des méthodes permettant de construire
	 * le tableau de résultat D2.
	 *
	 * @package app.View.Helper
	 */
	class Tableaud2Helper extends AppHelper
	{
		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array( 'Html', 'Locale', 'Csv' );

		/**
		 * Les colonnes du tableau (sachant que pour chacune d'entre elles, on
		 * ajoutera une colonne de pourcentage).
		 *
		 * @var array
		 */
		public $columns = array(
			'nombre',
			'hommes',
			'femmes',
			'cer',
		);

		/**
		 *
		 * @param array $data
		 * @return string
		 */
		public function numberCells( array $data, $class = null ) {
			$cells = '';

			$class = "number {$class}";

			foreach( $this->columns as $column ) {
				// Valeur
				$cells .= $this->Html->tag(
					'td',
					$this->Locale->number( Hash::get( $data, $column ) ),
					array( 'class' => $class )
				);

				// Pourcentage
				$cells .= $this->Html->tag(
					'td',
					$this->Locale->number( Hash::get( $data, "{$column}_%" ), 2 ),
					array( 'class' => $class )
				);
			}

			return $cells;
		}

		/**
		 * Retourne la traduction du libellé, soit le nom du contrôleur en
		 * CamelCase slash le nom de l'action, slash la catégorie, traduite dans
		 * le fichier du même nom que le contrôleur.
		 *
		 * @param string $categorie
		 * @return string
		 */
		public function categorie1Label( $categorie ) {
			$domain = $this->request->controller;
			$msgid = sprintf( '/%s/%s/%s', Inflector::camelize( $this->request->controller ), $this->request->action, $categorie );

			return __d( $domain, $msgid );
		}

		/**
		 *
		 * @param string $categorie
		 * @return string
		 */
		public function totalLabel( $categorie ) { // FIXME
			$domain = $this->request->controller;
			$msgid = sprintf( '/%s/%s/total', Inflector::camelize( $this->request->controller ), $this->request->action );

			return sprintf( __d( $domain, $msgid ), $categorie );
		}

		/**
		 * Retourne une partie de tableau lorsqu'on n'a qu'une seule catégorie.
		 *
		 * @param string $categorie
		 * @param array $results
		 * @return string
		 */
		public function line1Categorie( $categorie, array $results ) {
			$cells = '';

			$cells .= $this->Html->tag( 'th', $this->categorie1Label( $categorie ), array( 'colspan' => 3, 'class' => 'categorie1' ) );

			$cells .= $this->numberCells( $results[$categorie] );

			return $this->Html->tag( 'tr', $cells );
		}

		/**
		 * Retourne une partie de tableau lorsqu'on a deux catégories.
		 *
		 * @todo: sous-totaux
		 *
		 * @param string $categorie
		 * @param array $results
		 * @param array $categories
		 * @return string
		 */
		public function line2Categorie( $categorie, array $results, array $categories ) {
			$rows = '';
			$i = 0;

			$total = array();
			foreach( $this->columns as $column ) {
				$total[$column] = $total["{$column}_%"] = 0;
			}

			foreach( $results[$categorie] as $label => $data ) {
				$cells = '';

				if( $i == 0 ) {
					$cells .= $this->Html->tag( 'th', $this->categorie1Label( $categorie ), array( 'rowspan' => count( Hash::flatten( $categories[$categorie] ) ) + 1, 'class' => 'categorie1' ) );
				}

				$cells .= $this->Html->tag( 'th', $label, array( 'colspan' => 2, 'class' => 'categorie2' ) );
				$cells .= $this->numberCells( $data );

				foreach( $this->columns as $column ) {
					$total[$column] += $data[$column];
					$total["{$column}_%"] += $data["{$column}_%"];
				}

				$rows .= $this->Html->tag( 'tr', $cells );

				$i++;
			}

			// Total
			$rows .= $this->Html->tag(
				'tr',
				$this->Html->tag( 'th', $this->totalLabel( __d( $this->request->controller, "SORTIE::{$categorie}" ) ), array( 'colspan' => 2, 'class' => 'total' ) )
				.$this->numberCells( $total, 'total' )
			);

			return $rows;
		}

		/**
		 * Retourne une partie de tableau lorsqu'on a trois catégories.
		 *
		 * @todo: sous-totaux
		 *
		 * @param string $categorie
		 * @param array $results
		 * @param array $categories
		 * @return string
		 */
		public function line3Categorie( $categorie, array $results, array $categories ) {
			$rows = '';
			$i1 = 0;

			foreach( $results[$categorie] as $label1 => $data1 ) {
				$i2 = 0;

				$total = array();
				foreach( $this->columns as $column ) {
					$total[$column] = $total["{$column}_%"] = 0;
				}


				foreach( $data1 as $label2 => $data2 ) {
					$cells = '';

					if( $i1 == 0 ) {
						$cells .= $this->Html->tag( 'th', $this->categorie1Label( $categorie ), array( 'rowspan' => count( Hash::flatten( $categories[$categorie] ) ) + count( array_keys( $results[$categorie] ) ), 'class' => 'categorie1' ) );
					}

					if( $i2 == 0 ) {
						$cells .= $this->Html->tag( 'th', $label1, array( 'rowspan' => count( array_keys( $data1 ) ), 'class' => 'categorie2' ) );
					}

					$cells .= $this->Html->tag( 'th', $label2, array( 'class' => 'categorie3' ) );
					$cells .= $this->numberCells( $data2 );

					foreach( $this->columns as $column ) {
						$total[$column] += $data2[$column];
						$total["{$column}_%"] += $data2["{$column}_%"];
					}

					$rows .= $this->Html->tag( 'tr', $cells );

					$i1++;
					$i2++;
				}

				// Total
				$rows .= $this->Html->tag(
					'tr',
					$this->Html->tag( 'th', $this->totalLabel( $label1 ), array( 'class' => 'total', 'colspan' => 2 ) )
					.$this->numberCells( $total, 'total' )
				);
			}

			return $rows;
		}

		/**
		 *
		 * @param array $data
		 * @return string
		 */
		public function numberCellsCsv( $row, array $data ) {
			foreach( $this->columns as $column ) {
				// Valeur
				$row[] = str_replace( '&nbsp;', '', $this->Locale->number( Hash::get( $data, $column ) ) );

				// Pourcentage
				$row[] = str_replace( '&nbsp;', '', $this->Locale->number( Hash::get( $data, "{$column}_%" ), 2 ) );
			}

			return $row;
		}

		/**
		 * Retourne une partie de tableau CSV lorsqu'on n'a qu'une seule catégorie.
		 *
		 * @param string $categorie
		 * @param array $results
		 * @return string
		 */
		public function line1CategorieCsv( $categorie, array $results ) {
			$row = array(
				$this->categorie1Label( $categorie ),
				null,
				null,
			);

			$row = $this->numberCellsCsv( $row, $results[$categorie] );

			$this->Csv->addRow( $row );
		}

		/**
		 * Retourne une partie de tableau CSV lorsqu'on a deux catégories.
		 *
		 * @todo: sous-totaux
		 *
		 * @param string $categorie
		 * @param array $results
		 * @param array $categories
		 * @return string
		 */
		public function line2CategorieCsv( $categorie, array $results, array $categories ) {
			$i = 0;

			$total = array();
			foreach( $this->columns as $column ) {
				$total[$column] = $total["{$column}_%"] = 0;
			}

			foreach( $results[$categorie] as $label => $data ) {
				$row = array(
					$this->categorie1Label( $categorie ),
					$label,
					null,
				);

				$row = $this->numberCellsCsv( $row, $data );

				foreach( $this->columns as $column ) {
					$total[$column] += $data[$column];
					$total["{$column}_%"] += $data["{$column}_%"];
				}

				$this->Csv->addRow( $row );

				$i++;
			}

			// Total
			$row = array(
				$this->categorie1Label( $categorie ),
				$this->totalLabel( __d( $this->request->controller, "SORTIE::{$categorie}" ) ),
				null
			);
			$row = $this->numberCellsCsv( $row, $total );

			$this->Csv->addRow( $row );
		}

		/**
		 * Retourne une partie de tableau CSV lorsqu'on a trois catégories.
		 *
		 * @todo: sous-totaux
		 *
		 * @param string $categorie
		 * @param array $results
		 * @param array $categories
		 * @return string
		 */
		public function line3CategorieCsv( $categorie, array $results, array $categories ) {
			$i1 = 0;

			foreach( $results[$categorie] as $label1 => $data1 ) {
				$i2 = 0;

				$total = array();
				foreach( $this->columns as $column ) {
					$total[$column] = $total["{$column}_%"] = 0;
				}


				foreach( $data1 as $label2 => $data2 ) {
					$row = array(
						$this->categorie1Label( $categorie ),
						$label1,
						$label2
					);

					$row = $this->numberCellsCsv( $row, $data2 );

					foreach( $this->columns as $column ) {
						$total[$column] += $data2[$column];
						$total["{$column}_%"] += $data2["{$column}_%"];
					}

					$this->Csv->addRow( $row );

					$i1++;
					$i2++;
				}

				// Total
				$row = array(
					$this->categorie1Label( $categorie ),
					$label1,
					$this->totalLabel( $label1 )
				);
				$row = $this->numberCellsCsv( $row, $total );

				$this->Csv->addRow( $row );
			}
		}
	}
?>