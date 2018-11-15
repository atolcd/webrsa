<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<h1><?php echo $this->pageTile = __m( 'Admin.Indicateurmensuel.RDVCER' ); ?></h1>

<?php
	echo $this->Form->create( 'Indicateurmensuel', array( 'type' => 'post', 'class' => 'noprint', 'novalidate' => true ) );
	echo $this->Form->input( 'Indicateurmensuel.annee', array( 'label' => __d( 'indicateurmensuel', 'Indicateurmensuel.annee' ), 'type' => 'select', 'options' => array_range( date( 'Y' ), date( 'Y' ) - 20 ) ) );
	echo $this->Form->input( 'Indicateurmensuel.departement', array( 'label' => __d( 'indicateurmensuel', 'Indicateurmensuel.departement' ), 'type' => 'select', 'empty' => true, 'options' => $optionsDpt ) );
	echo $this->Form->input( 'Indicateurmensuel.structuresreferentes', array( 'label' => __d( 'indicateurmensuel', 'Indicateurmensuel.structuresreferentes' ), 'empty' => true, 'type' => 'select', 'options' => $options ) );
	echo $this->Form->input( 'Indicateurmensuel.communautessrs', array( 'label' => __d( 'indicateurmensuel', 'Indicateurmensuel.communautessrs' ), 'empty' => true, 'type' => 'select', 'options' => $optionsSrs ) );

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
			//nouveautés ticket 54177
			'nbMoyJrsRdvIndivPrevuPIE' => array( 'type' => 'float', 'result' => 'avg' ),
			'nbMoyJrsRdvIndivHonorePIE' => array( 'type' => 'float', 'result' => 'avg' ),
			'nbMoyJrsRdvCollPrevuPIE' => array( 'type' => 'float', 'result' => 'avg' ),
			'nbMoyJrsRdvCollHonorePIE' => array( 'type' => 'float', 'result' => 'avg' ),
			'nbMoyJrsCerEtabliPIE' => array( 'type' => 'float', 'result' => 'avg' ),
			'nbMoyJrsRdvIndivCerEtabliePIE' => array( 'type' => 'float', 'result' => 'avg' )
		);

		$rows = array();
		$nbResult	=	0;
		foreach( $indicateurs as $key => $indicateur ) {
			$row = array( '<td>'.__d( 'indicateurmensuel', 'Indicateurmensuel.'.$key ).'</td>' );
			$nbColonne	=	count($indicateur);
			foreach($indicateur as $index=>$value) {
				$nbResult++;
				if($index<=($nbColonne-2)) {
					$personnes	=	(isset($value[0][0]['nbpersonnes'])) ? '<br /><i>'.$value[0][0]['nbpersonnes'].' pers.</i>' : '';
					$row[] = '<td class="number">'.number_format($value[0][0]['indicateur'], 2, ',', ' ').' j.'.$personnes.'</td>';
				}
				else
					$row[] = '<td class="number"><strong>'.number_format($value[0][0]['indicateur'], (($index==$nbColonne)?0:2), ',', ' ').'</strong</td>';
			}
			$rows[] = '<tr class="'.( ( ( count( $rows ) + 1 ) % 2 ) == 0 ? 'even' : 'odd' ).'">'.implode( '', $row ).'</tr>';
		}

		if($nbResult>0){
			$headers = array( null );
			$nbVagues	=	count($vagues);
			for( $i = 1 ; $i<=$nbVagues ; $i++ ) {
				$dateDebut	=	substr(implode('/', array_reverse(explode('-', $vagues[$i]['dateDebut']))), 0, 5);
				$dateFin	=	substr(implode('/', array_reverse(explode('-', $vagues[$i]['dateFin']))), 0, 5);
				$headers[] = '<b>Vague '.$i.'</b><br /><br />'.$dateDebut.' - '.$dateFin.'';
			}
			$headers[]	=	'Moyenne<br />'.$annee;
			$headers[]	=	'Personnes<br />concernées';
			$thead = $this->Xhtml->tag( 'thead', $this->Xhtml->tableHeaders( $headers ) );
			$tbody = $this->Xhtml->tag( 'tbody', implode( '', $rows ) );
			echo $this->Xhtml->tag( 'table', $thead.$tbody );
		}
		else {
			echo '<div class="noResult">Aucun résultat</div>';
		}
	}
?>