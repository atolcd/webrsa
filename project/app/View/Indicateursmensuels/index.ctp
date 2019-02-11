<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<h1><?php echo $this->pageTile = __m( 'Admin.Indicateurmensuel.General' ); ?></h1>

<?php
	echo $this->Form->create( 'Indicateurmensuel', array( 'type' => 'post', 'class' => 'noprint', 'novalidate' => true ) );
	echo $this->Form->input( 'Indicateurmensuel.annee', array( 'label' => __d( 'indicateurmensuel', 'Indicateurmensuel.annee' ), 'type' => 'select', 'options' => array_range( date( 'Y' ), date( 'Y' ) - 20 ) ) );
	echo $this->Form->submit( 'Calculer' );
	echo $this->Form->end();
?>

<?php if( !empty( $this->request->data ) && isset( $indicateurs ) ) :?>
	<div class="submit noprint">
		<?php echo $this->Form->button( 'Imprimer cette page', array( 'type' => 'button', 'onclick' => 'printit();' ) );?>
	</div>
<?php endif;?>

<?php
	if( !empty( $this->request->data ) && isset( $indicateurs ) ) {
		$annee = Set::extract( $this->request->data, 'Indicateurmensuel.annee' );
		$types = array(
			'nbrDossiersInstruits'						=> array( 'type' => 'int', 'result' => 'sum' ),
			'nbrDossiersRejetesCaf'						=> array( 'type' => 'int', 'result' => 'sum' ),
			'nbrOuverturesDroits'						=> array( 'type' => 'int', 'result' => 'sum' ),
			'nbrAllocatairesDroitsEtDevoirs'			=> array( 'type' => 'int', 'result' => 'sum' ),
			'nbrPreorientationsEmploi'					=> array( 'type' => 'int', 'result' => 'sum' ),
			'nbrPreorientationsSocial'					=> array( 'type' => 'int', 'result' => 'sum' ),
			'nbrPreorientationsSocioprofessionnelle'	=> array( 'type' => 'int', 'result' => 'sum' ),
			'delaiOuvertureNotification'				=> array( 'type' => 'float', 'result' => 'avg' ),
			'delaiNotificationSignature'				=> array( 'type' => 'float', 'result' => 'avg' ),
			'montantsIndusConstates'					=> array( 'type' => 'int', 'result' => 'sum' ),
			'montantsIndusTransferes'					=> array( 'type' => 'int', 'result' => 'sum' ),
			'nbrCiNouveauxEntrantsCg'					=> array( 'type' => 'int', 'result' => 'sum' ),
			'nbrSuspensionsDroits'						=> array( 'type' => 'int', 'result' => 'sum' ),
		);

		//**************************************************************************

		$rows = array();
		foreach( $indicateurs as $key => $indicateur ) {
			$row = array( '<td>'.__d( 'indicateurmensuel', 'Indicateurmensuel.'.$key ).'</td>' );
			$type = Set::extract( $types, $key.'.type' );
			$result = Set::extract( $types, $key.'.result' );
			for( $i = 1 ; $i <= 12 ; $i++ ) {
				$value = ( ( isset( $indicateur[$i] ) ? $indicateur[$i] : 0 ) );
				$row[] = '<td class="number">'.$this->Locale->number( $value, ( ( $type == 'int' ) ? 0 : 2 ) ).'</td>';
			}
			$value = ( ( $type == 'int' ) ? array_sum( $indicateur ) : array_avg( $indicateur ) );
			$row[] = '<td class="number"><strong>'.$this->Locale->number( $value, ( ( $type == 'int' ) ? 0 : 2 ) ).'</strong></td>';
			$rows[] = '<tr class="'.( ( ( count( $rows ) + 1 ) % 2 ) == 0 ? 'even' : 'odd' ).'">'.implode( '', $row ).'</tr>';
		}

		//**************************************************************************

		$headers = array( null );
		for( $i = 1 ; $i <= 12 ; $i++ ) {
			$headers[] = ucfirst( $this->Locale->date( '%b %Y', $annee.( ( $i < 10 ) ? '0'.$i : $i ).'01' ) );
		}
		$headers[] = 'Total / Moyenne '.$annee;

		$thead = $this->Xhtml->tag( 'thead', $this->Xhtml->tableHeaders( $headers ) );
		$tbody = $this->Xhtml->tag( 'tbody', implode( '', $rows ) );
		echo $this->Xhtml->tag( 'table', $thead.$tbody );
	}
?>