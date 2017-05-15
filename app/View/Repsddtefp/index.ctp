<?php
	$this->pageTitle = 'APRE: Reporting bi-mensuel DDTEFP';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<h1><?php echo $this->pageTitle;?></h1>

<?php
	echo $this->Form->create( 'Repddtefp', array( 'type' => 'post', 'class' => 'noprint' ) );

	echo $this->Form->input( 'Repddtefp.annee', array( 'label' => 'Année', 'type' => 'select', 'options' => array_range( date( 'Y' ), 2008 ), 'empty' => true ) );
	echo $this->Form->input( 'Repddtefp.semestre', array( 'label' => 'Semestre', 'type' => 'select', 'options' => array_range( 1, 2 ) ) );
	echo $this->Form->input( 'Repddtefp.numcom', array( 'label' => __d( 'apre', 'Repddtefp.numcom' ), 'type' => 'select', 'options' => $mesCodesInsee,  'empty' => true ) );

	echo $this->Html->tag( 'div', $this->Form->button( __( 'Calculer' ) ), array( 'class' => 'submit' ) );
	echo $this->Form->end();
?>
<?php if( !empty( $this->request->data ) && isset( $listeSexe ) && isset( $listeAge ) ) :?>
	<div class="submit noprint">
		<?php echo $this->Form->button( 'Imprimer cette page', array( 'type' => 'button', 'onclick' => 'printit();' ) );?>
	</div>
<?php endif;?>
<?php
	if( !empty( $this->request->data ) && isset( $listeSexe ) && isset( $listeAge ) ) {
		$annee = Set::extract( $this->request->data, 'Repddtefp.annee' );
		$semestre = Set::extract( $this->request->data, 'Repddtefp.semestre' );
		$ville = Set::extract( $this->request->data, 'Repddtefp.numcom' );

		//**************************************************************************

		function lastday($month = '', $year = '') {
			if (empty($month)) {
				$month = date('m');
			}
			if (empty($year)) {
				$year = date('Y');
			}
			$result = strtotime("{$year}-{$month}-01");
			$result = strtotime('-1 second', strtotime('+1 month', $result));
			return date('d', $result);
		}

		function lines( $results, $thisData, $locale ) {
			$rows = array();
			foreach( $results as $key => $repddtefp ) {
				$row = array();
				for( $i = 1 ; $i <= 6 ; $i++ ) {
					$mois = ( ( Set::classicExtract( $thisData, 'Repddtefp.semestre' ) - 1 ) * 6  ) + $i;
					$total = 0;
					foreach( array( 1, 2 ) as $quinzaine ) {
						$indicateur = Set::extract( $results, "/{$key}[mois=$mois][quinzaine=$quinzaine]/indicateur" );
						if( !empty( $indicateur ) ) {
							$indicateur = $indicateur[0];
						}
						else {
							$indicateur = 0;
						}
						$total += $indicateur;
						$row[] = $indicateur;
					}
					$row[] = $total;

				}
				$rows[] = $row;
			}

			// FIXME: $this->Locale->number( ... ) Pour les indicateurs
			$totaux = array();
			foreach( $rows as $k => $row ) {
				foreach( $row as $i => $value ) {
					if( !empty( $value ) ) {
						if( !isset( $totaux[$i] ) ) {
							$totaux[$i] = 0;
						}
						$totaux[$i] = $totaux[$i] + $value;
					}
					else {
						$rows[$k][$i] = null;
					}

					if( !isset( $totaux[$i] ) ) {
						$totaux[$i] = null;
					}
				}
			}

			$ths = array_keys( $results );
			$lines = array();
			foreach( $rows as $i => $row ) {
				$lines[] = $rows[] = '<tr class="'.( ( $i % 2 ) == 0 ? 'even' : 'odd' ).'"><td>'.h( __d( 'apre', 'Repddtefp.'.$ths[$i] ) ).'</td><td class="number">'.implode( '</td><td class="number">', $row ).'</td></tr>';
			}
			$lines[] = $rows[] = '<tr class="total '.( ( ( $i + 1 ) % 2 ) == 0 ? 'even' : 'odd' ).'"><td>Total</td><td class="number">'.implode( '</td><td class="number">', $totaux ).'</td></tr>';

			return $lines;
		}

		//**************************************************************************
		if( !empty( $ville ) ){
			echo '<h2>Données pour la ville : '.$ville.'</h2>';
		}
		//**************************************************************************

		$headers1 = array();
		$headers2 = array( null );
		for( $i = 1 ; $i <= 6 ; $i++ ) {
			$mois = ( ( Set::classicExtract( $this->request->data, 'Repddtefp.semestre' ) - 1 ) * 6  ) + $i;
			$headers1[] = ucfirst( $this->Locale->date( '%b %Y', $annee.'-'.( $mois < 10 ? '0'.$mois : $mois ).'-'.'01' ) );
			$headers2[] = '1er au 14';
			$headers2[] = '15 au '.lastday( $mois, $annee );
			$headers2[] = 'Total';
		}

		$headers1 = str_replace( '<th>', '<th colspan="3">', $this->Xhtml->tableHeaders( $headers1 ) );
		$headers1 = str_replace( '<tr>', '<tr><th></th>', $headers1 );

		$headers2 = $this->Xhtml->tableHeaders( $headers2 );
		$headers2 = str_replace( '<tr>', '<tr id="datesApre">', $headers2 );

		//**************************************************************************


		$thead = $this->Xhtml->tag( 'thead', $headers1.$headers2 );
		$tbody = $this->Xhtml->tag( 'tbody', '<tr><th colspan="19">Sexe</th></tr>'.implode( '', lines( $listeSexe, $this->request->data, $this->Locale ) ) );
		$tbody .= $this->Xhtml->tag( 'tbody', '<tr><th colspan="19">Âge</th></tr>'.implode( '', lines( $listeAge, $this->request->data, $this->Locale ) ) );
		echo $this->Xhtml->tag( 'table', $thead.$tbody, array( 'id' => 'repddtefp' ) );
	}
?>