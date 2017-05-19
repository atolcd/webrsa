<?php
	foreach( $cers93 as $index => $cer93 ) {
		$rowId = "innerTableTrigger{$index}";
		$rowValidationErrors = ( isset( $this->validationErrors['Histochoixcer93'][$index] ) ? $this->validationErrors['Histochoixcer93'][$index] : array() );

		$innerTable = '<table id="innerTablesearchResults'.$index.'" class="innerTable">
				<tbody>
					<tr>
						<th>N° de dossier</th>
						<td>'.$cer93['Dossier']['numdemrsa'].'</td>
					</tr>
					<tr>
						<th>Date de demande RSA</th>
						<td>'.date_short( $cer93['Dossier']['dtdemrsa'] ).'</td>
					</tr>
					<tr>
						<th>Date de naissance</th>
						<td>'.date_short( $cer93['Personne']['dtnai'] ).'</td>
					</tr>
					<tr>
						<th>N° CAF</th>
						<td>'.$cer93['Dossier']['matricule'].'</td>
					</tr>
					<tr>
						<th>NIR</th>
						<td>'.$cer93['Personne']['nir'].'</td>
					</tr>
					<tr>
						<th>Code postal</th>
						<td>'.$cer93['Adresse']['codepos'].'</td>
					</tr>
					<tr>
						<th>Date de fin de droit</th>
						<td>'.$cer93['Situationdossierrsa']['dtclorsa'].'</td>
					</tr>
					<tr>
						<th>Motif de fin de droit</th>
						<td>'.Set::enum( $cer93['Situationdossierrsa']['moticlorsa'], $options['moticlorsa'] ).'</td>
					</tr>
					<tr>
						<th>Rôle</th>
						<td>'.Set::enum( $cer93['Prestation']['rolepers'], $options['rolepers'] ).'</td>
					</tr>
					<tr>
						<th>Etat du dossier</th>
						<td>'.Set::classicExtract( $options['etatdosrsa'], $cer93['Situationdossierrsa']['etatdosrsa'] ).'</td>
					</tr>
					<tr>
						<th>Présence DSP</th>
						<td>'.$this->Xhtml->boolean( $cer93['Dsp']['exists'] ).'</td>
					</tr>
					<tr>
						<th>Adresse</th>
						<td>'.$cer93['Adresse']['numvoie'].' '.$cer93['Adresse']['libtypevoie'].' '.$cer93['Adresse']['nomvoie'].' '.$cer93['Adresse']['codepos'].' '.$cer93['Adresse']['nomcom'].'</td>
					</tr>
				</tbody>
			</table>';

		$cells = array(
			$cer93['Adresse']['nomcom'],
			$cer93['Personne']['nom_complet_court'],
			$cer93['Referent']['nom_complet'],
			date_short( $cer93['Orientstruct']['date_valid'] ),
			date_short( $cer93['Contratinsertion']['created'] ),
			array( $this->Form->input( "Histochoixcer93.{$index}.formeci", array( 'type' => 'radio', 'options' => $options['formeci'], 'div' => false, 'legend' => false, 'separator' => '<br/>' ) ), array( 'class' => ( isset( $rowValidationErrors['formeci'] ) ? 'error' : null ) ) ),
			array(
				$this->element(
					'modalbox',
					array(
						'modalid' => "CheckboxesInputs{$index}",
						'modalcontent' => $this->Checkboxes->inputs(
							'Commentairenormecer93.Commentairenormecer93.%d',
							array(
								'fk_field' => 'commentairenormecer93_id',
								'autre_field' => 'commentaireautre',
								'autres_type' => 'textarea',
								'offset' => ( count( $options['commentairesnormescers93_list'] ) * $index ),
								'options' => $options['commentairesnormescers93_list'],
								'autres_ids' => $options['commentairesnormescers93_autres_ids'],
								'cohorte' => true
							)
						),
						'modalmessage' => 'Sélectionnez la ou les valeurs et fermez pour enregistrer.'
					)
				)
				.$this->Html->link( 'Commentaire', '#', array( 'onclick' => "\$( 'CheckboxesInputs{$index}' ).show();return false;", 'class' => 'comment' ) ),
				array()
			),
			$this->Form->inputs(
				array(
					'legend' => false,
					'fieldset' => false,
					"Histochoixcer93.{$index}.id" => array( 'type' => 'hidden' ),
					"Histochoixcer93.{$index}.cer93_id" => array( 'type' => 'hidden', 'value' => $cer93['Cer93']['id'] ),
					"Histochoixcer93.{$index}.user_id" => array( 'type' => 'hidden', 'value' => $this->Session->read( 'Auth.User.id' ) ),
					"Histochoixcer93.{$index}.datechoix" => array( 'type' => 'hidden', 'value' => date( 'Y-m-d' ) ),
					"Histochoixcer93.{$index}.etape" => array( 'type' => 'hidden', 'value' => '03attdecisioncg' ),
				)
			)
			.'<ul class="decisions"><li>'.$this->Ajax->link(
				'Rejeter',
				array( 'action' => $this->action, 'decision' => '99rejetecpdv' ),
				array(
					'update' => $rowId,
					'with' => 'serializeTableRow( $(this) )',
					'complete' => 'mkTooltipTables();make_external_links();',
					'class' => 'rejeter'
				),
                'Confirmez-vous le rejet du CER ?'
			).'</li><li>'
			.$this->Ajax->link(
				'Transférer au CG',
				array( 'action' => $this->action, 'decision' => '03attdecisioncg' ),
				array(
					'update' => $rowId,
					'with' => 'serializeTableRow( $(this) )',
					'complete' => 'mkTooltipTables();make_external_links();',
					'class' => 'transferer cg'
				),
                'Confirmez-vous le transfert au CG du CER ?'
			).'</li></ul>',
			$this->Xhtml->printLink( 'Imprimer', array( 'controller' => 'cers93', 'action' => 'impression', $cer93['Contratinsertion']['id'] ), true, true ),
			array( $this->Xhtml->link( 'Voir', array( 'controller' => 'histoschoixcers93', 'action' => 'attdecisioncg', $cer93['Contratinsertion']['id'] ), array( 'target' => 'histoschoixcers93_attdecisioncg' ) ), array( 'class' => 'button view' ) ),
			array( $innerTable, array( 'class' => 'innerTableCell noprint' ) )
		);

		echo $this->Html->tableCells(
			$cells,
			array( 'class' => 'odd', 'id' => $rowId ),
			array( 'class' => 'even', 'id' => $rowId )
		);
	}
?>