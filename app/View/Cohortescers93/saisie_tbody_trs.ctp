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
			date_short( $cer93['Orientstruct']['date_valid'] ),
			date_short( $cer93['PersonneReferent']['dddesignation'] ),
			$cer93['Contratinsertion']['rg_ci'],
			'<ul>'
				.( !empty( $cer93['Rendezvous']['daterdv'] ) ? '<li>'.date_short( $cer93['Rendezvous']['daterdv'] ).'</li>' : null )
				.'<li>'.$this->Html->link( 'Prendre RDV', array( 'controller' => 'rendezvous', 'action' => 'add', $cer93['Personne']['id'] ), array( 'class' => 'external rendezvous add' ) ).'</li>'
			.'</ul>',
			Set::enum( $cer93['Cer93']['positioncer'], $options['Cer93']['positioncer'] ),
		);

		if( $cer93['Cer93']['positioncer'] == null ) {
			$cells = array_merge(
				$cells,
				array(
					null,
					null,
					null,
				)
			);
		}
		else if( $cer93['Cer93']['positioncer'] == '01signe' ) {
			$innerForm = $this->Form->inputs(
				array(
					'legend' => false,
					'fieldset' => false,
					"Histochoixcer93.{$index}.id" => array( 'type' => 'hidden' ),
					"Histochoixcer93.{$index}.cer93_id" => array( 'type' => 'hidden', 'value' => $cer93['Cer93']['id'] ),
					"Histochoixcer93.{$index}.user_id" => array( 'type' => 'hidden', 'value' => $this->Session->read( 'Auth.User.id' ) ),
					"Histochoixcer93.{$index}.datechoix" => array( 'type' => 'hidden', 'value' => date( 'Y-m-d' ) ),
					"Histochoixcer93.{$index}.etape" => array( 'type' => 'hidden', 'value' => '02attdecisioncpdv' ),
				)
			)
			.'<ul><li>'.$this->Html->link(
				'Imprimer',
				array(
					'controller' => 'cers93',
					'action' => 'impression',
					$cer93['Contratinsertion']['id']
				),
				array(
					'class' => 'external print'
				)
			).'</li><li>'
			.$this->Ajax->link(
				'Transférer au responsable',
				array( 'action' => 'saisie' ),
				array(
					'update' => $rowId,
					'with' => 'serializeTableRow( $(this) )',
					'complete' => 'mkTooltipTables();make_external_links();',
					'class' => 'transfert cpdv'
				)
			).'</li></ul>';
			$cells = array_merge(
				$cells,
				array(
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
					$innerForm
				)
			);
		}
		else {
			$cells = array_merge(
				$cells,
				array(
					Set::enum( $cer93['Histochoixcer93etape02']['formeci'], $options['formeci'] ),
					array(
						(
							isset( $cer93['Commentairenormecer93'] )
							? $this->element(
								'modalbox',
								array(
									'modalid' => "CheckboxesInputs{$index}",
									'modalcontent' => $this->Checkboxes->view(
										$cer93,
										'Commentairenormecer93.name',
										'Commentairenormecer93Histochoixcer93.commentaireautre'
									)
								)
							)
							.$this->Html->link( 'Commentaire', '#', array( 'onclick' => "\$( 'CheckboxesInputs{$index}' ).show();return false;", 'class' => 'comment' ) )
							: null
						),
						array()
					),
					'<ul><li>'.$this->Html->link(
						'Imprimer',
						array(
							'controller' => 'cers93',
							'action' => 'impression',
							$cer93['Contratinsertion']['id']
						),
						array(
							'class' => 'external print'
						)
					).'</li></ul>'
				)
			);
		}

		$cells = array_merge(
			$cells,
			array(
				$this->Xhtml->viewLink( 'Voir', array( 'controller' => 'cers93', 'action' => 'index', $cer93['Personne']['id'] ) ),
				array( $innerTable, array( 'class' => 'innerTableCell noprint' ) )
			)
		);

		echo $this->Html->tableCells(
			$cells,
			array( 'class' => 'odd', 'id' => $rowId ),
			array( 'class' => 'even', 'id' => $rowId )
		);
	}
?>