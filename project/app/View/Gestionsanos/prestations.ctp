<h1>Nettoyage des prestations RSA multiples des allocataires et de leur famille</h1>
<?php
	if( isset( $foyers ) ) {
		$pagination = $this->Xpaginator->paginationBlock( 'Foyer', Set::merge( $this->request->params['pass'], $this->request->params['named'] ) );

		echo $pagination;
		echo '<table>
			<!--<thead>
				<tr>
					<th>qual</th>
					<th>nom</th>
					<th>prenom</th>
					<th>dtnai</th>
					<th>enregistrements</th>
					<th>rolepers</th>
				</tr>
			</thead>-->
			<tbody>';
		foreach( $foyers as $foyer ) {
			echo $this->Xhtml->tableCells(
				array(
					$foyer['Dossier']['numdemrsa'],
					$foyer['Foyer']['sitfam'],
					$this->Locale->date( 'Locale->date', $foyer['Foyer']['ddsitfam'] ),
					$foyer['Foyer']['enerreur'],
					$this->Xhtml->link( 'Résolution', array( 'action' => 'prestations', $foyer['Foyer']['id'] ) ),
				)
			);
		}
		echo '</tbody></table>';
		echo $pagination;
	}
	else if( isset( $foyer ) ) {
		echo $this->Xform->create();
		echo '<table>
			<thead>
				<tr>
					<th>qual</th>
					<th>nom</th>
					<th>prenom</th>
					<th>dtnai</th>
					<th>enregistrements</th>
					<th>rolepers</th>
				</tr>
			</thead>
			<tbody>';
		$i = 0;
		foreach( $foyer['Personne'] as $personne ) {
			if( count( $personne['Prestation'] ) == 1 ) {
				$prestation = $personne['Prestation'][0]['rolepers'];
			}
			else {
				$prestations = Set::extract( '/Prestation/rolepers', $personne );
				$prestations = array_combine( $prestations, $prestations );
				$prestation = $this->Xform->input( "Prestation.{$i}.personne_id", array( 'type' => 'hidden', 'value' => $personne['id'] ) );
				$prestation .= $this->Xform->input( "Prestation.{$i}.rolepers", array( 'label' => false, 'type' => 'select', 'options' => $prestations, 'empty' => true ) );
				$prestation = array( $prestation, array( 'class' => ( !empty( $this->validationErrors['Prestation'][$i]['rolepers'] ) ? 'error' : null ) ) );
				$i++;
			}

			echo $this->Xhtml->tableCells(
				array(
					$personne['qual'],
					$personne['nom'],
					$personne['prenom'],
					$this->Locale->date( 'Locale->date', $personne['dtnai'] ),
					$personne['nbrliens'],
					$prestation
				)
			);
		}
		echo '</tbody></table>';
		echo $this->Xform->end( __( 'Save' ) );
	}
?>