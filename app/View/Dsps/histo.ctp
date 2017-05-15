<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	// Titre
	$this->pageTitle = sprintf(
		__( 'Historique des DSPs de %s' ),
		Set::extract( $dsp, 'Personne.qual' ).' '.Set::extract( $dsp, 'Personne.nom' ).' '.Set::extract( $dsp, 'Personne.prenom' )
	);
	
	App::uses('WebrsaAccess', 'Utility');
	WebrsaAccess::init($dossierMenu);
?>

<div class="tab_histo_dsp">
	<?php
		echo $this->Xhtml->tag( 'h1', $this->pageTitle );
		echo $this->element( 'ancien_dossier' );

		if (!empty($histos[0]['DspRev'])) {
			echo "<table><thead>";
			echo "<tr><th>Date de création</th><th>Date de modification</th><th>Différences</th><th class='action' colspan='6'>Actions</th></tr></thead><tbody>";

			foreach ($histos as $histo) {
				$nbFichiersLies = 0;
				$nbFichiersLies = ( isset( $histo['Fichiermodule'] ) ? count( $histo['Fichiermodule'] ) : 0 );

				echo "<tr><td>";
				if (isset($histo['DspRev']['created'])) echo date_short( $histo['DspRev']['created'] );
				echo "</td><td>";
				if (isset($histo['DspRev']['modified'])) echo date_short( $histo['DspRev']['modified'] );
				echo "<td>".$histo['diff'].'</td>';
				
				echo '<td>'.$this->Xhtml->link($this->Xhtml->image('icons/style.png', array()).' Voir les différences', '/dsps/view_diff/'.$histo['DspRev']['id'], array(
					'escape'=>false, 
					'enabled' => WebrsaAccess::isEnabled($histo, '/Dsps/view_diff')
				)).'</td>';
				
				echo "</td><td>".$this->Xhtml->link($this->Xhtml->image('icons/zoom.png', array()).'Voir', '/dsps/view_revs/'.$histo['DspRev']['id'], array(
					'escape'=>false, 
					'enabled' => WebrsaAccess::isEnabled($histo, '/Dsps/view_revs')
				))."</td><td>".$this->Xhtml->link($this->Xhtml->image('icons/pencil.png', array()).'Modifier', '/dsps/edit/'.$dsp['Personne']['id'].'/'.$histo['DspRev']['id'], array(
						'escape'=>false, 
						'enabled' => WebrsaAccess::isEnabled($histo, '/Dsps/edit')
					))."</td>";
				if( Configure::read( 'Cg.departement' ) != 66 ){
					echo "<td>".$this->Xhtml->link($this->Xhtml->image('icons/arrow_redo.png', array()).'Revenir à cette version', '/dsps/revertTo/'.$histo['DspRev']['id'], array(
						'escape'=>false, 
						'enabled' => WebrsaAccess::isEnabled($histo, '/Dsps/revertTo')
					))."</td>";
				}

				echo "<td>".$this->Xhtml->link($this->Xhtml->image('icons/attach.png', array()).'Fichiers liés', '/dsps/filelink/'.$histo['DspRev']['id'], array(
					'escape'=>false, 
					'enabled' => WebrsaAccess::isEnabled($histo, '/Dsps/filelink')
				))."</td>";
				
				echo "<td>".'('.$nbFichiersLies.')'."</td>";
				echo "</tr>";
			}
			echo "</tbody></table>";
	?>
	<?php
		}
		else {
			echo "Aucun historique de DSP n'existe pour cette personne.";
		}
	?>
</div>