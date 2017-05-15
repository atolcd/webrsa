<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Html->tag( 'h1', $title_for_layout );

	echo $this->Form->create();
	echo $this->Search->blocAdresse( $mesCodesInsee, $cantons );
	echo $this->Form->input( 'Indicateurmensuel.serviceinstructeur', array( 'type' => 'select', 'options' => $servicesinstructeurs, 'empty' => true, 'label' => 'MSP' ) );
	echo $this->Form->input( 'Indicateurmensuel.annee', array( 'type' => 'select', 'options' => array_combine( range( date( 'Y' ), 2009, -1 ), range( date( 'Y' ), 2009, -1 ) ), 'label' => 'Année' ) );
	echo $this->Form->end( 'Calculer' );

	if( isset( $results ) ) {
		if( !empty( $results ) ) {
			echo '<table>
					<thead>
						<tr>
							<th>'.$title_for_layout.'</th>
							<th>Janv.</th>
							<th>Févr.</th>
							<th>Mars</th>
							<th>Avr.</th>
							<th>Mai</th>
							<th>Juin</th>
							<th>Juil.</th>
							<th>Août</th>
							<th>Sept.</th>
							<th>Oct.</th>
							<th>Nov.</th>
							<th>Déc.</th>
						</tr>
					</thead>
					<tbody>';
			foreach( $results as $label => $result ) {
				echo "<tr><th>".__d( 'indicateurmensuel', $label )."</th>";
				foreach( range( 1, 12 ) as $mois ) {
					$value = (int)Hash::get( $result, $mois );
					echo "<td class=\"number\">".$this->Locale->number( $value )."</td>";
				}
				echo '</tr>';
			}
			echo '</tbody></table>';
		}
		else {
			echo $this->Html->tag( 'p', 'Aucun résultat', array( 'class' => 'notice' ) );
		}

//		debug( $results );
	}
?>