<?php  $this->pageTitle = 'Situation du dossier RSA';?>

<h1><?php echo $this->pageTitle;?></h1>

<?php if( empty( $situationdossierrsa ) ):?>
	<p class="notice">Ce dossier ne possède pas encore de situation.</p>

<?php else:?>
	<?php if( $situationdossierrsa['Situationdossierrsa']['etatdosrsa'] == 0 ): ?>
		<table class="aere">
			<thead>
				<tr>
					<th>Etat du dossier </th>
				</tr>
			</thead>
			<tbody>
				<?php
					echo $this->Xhtml->tableCells(
						array(
							h( $etatdosrsa[$situationdossierrsa['Situationdossierrsa']['etatdosrsa']]),
						),
						array( 'class' => 'odd' ),
						array( 'class' => 'even' )
					);
				?>
			</tbody>
		</table>

	<?php elseif( $situationdossierrsa['Situationdossierrsa']['etatdosrsa'] == 1 ): ?>
		<h2>Refus</h2>
		<table class="aere">
			<thead>
				<tr>
					<th>Etat du dossier </th>
					<th>Date de refus</th>
				</tr>
			</thead>
			<tbody>
				<?php
					echo $this->Xhtml->tableCells(
						array(
							h( $etatdosrsa[$situationdossierrsa['Situationdossierrsa']['etatdosrsa']]),
							h( date_short($situationdossierrsa['Situationdossierrsa']['dtrefursa'] ) ) ,
						),
						array( 'class' => 'odd' ),
						array( 'class' => 'even' )
					);
				?>
			</tbody>
		</table>
	<?php elseif( $situationdossierrsa['Situationdossierrsa']['etatdosrsa'] == 2 ): ?>
		<table class="aere">
			<thead>
				<tr>
					<th>Etat du dossier </th>
				</tr>
			</thead>
			<tbody>
				<?php
					echo $this->Xhtml->tableCells(
						array(
							h( $etatdosrsa[$situationdossierrsa['Situationdossierrsa']['etatdosrsa']]),
						),
						array( 'class' => 'odd' ),
						array( 'class' => 'even' )
					);
				?>
			</tbody>
		</table>
	<?php elseif( $situationdossierrsa['Situationdossierrsa']['etatdosrsa'] == 3 ): ?>
		<h2>Suspension des droits</h2>
		<table class="aere">
			<thead>
				<tr>
					<th>Etat du dossier </th>
					<th>Motif de la suspension des droits</th>
					<th>Date de la suspension des droits</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$listmotisusdos = array();
					$listddsusdrorsa = array();
					foreach( $situationdossierrsa['Suspensiondroit'] as $suspensiondroit ) {
						$listmotisusdos[] = h( $motisusdrorsa[$suspensiondroit['motisusdrorsa']] );
						$listddsusdrorsa[] = h( date_short( $suspensiondroit['ddsusdrorsa'] ) );
					}
					echo $this->Xhtml->tableCells(
						array(
							h( $etatdosrsa[$situationdossierrsa['Situationdossierrsa']['etatdosrsa']]),
							( ( count( $listmotisusdos ) > 0 ) ? '<ul><li>'.implode( '</li><li>', $listmotisusdos ).'</li></ul>' : null ),
							( ( count( $listddsusdrorsa ) > 0 ) ? '<ul><li>'.implode( '</li><li>', $listddsusdrorsa ).'</li></ul>' : null ),
						),
						array( 'class' => 'odd' ),
						array( 'class' => 'even' )
					);
				?>
			</tbody>
		</table>
	<?php elseif( $situationdossierrsa['Situationdossierrsa']['etatdosrsa'] == 4 ): ?>
		<h2>Suspension des versements</h2>
		<table class="aere">
			<thead>
				<tr>
					<th>Etat du dossier </th>
					<th>Motif de la suspension des versements</th>
					<th>Date de la suspension des versements</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$listmotisusvers = array();
					$listddsusversrsa = array();
					foreach( $situationdossierrsa['Suspensionversement'] as $suspensionversement ) {
						$listmotisusvers[] = h( $motisusversrsa[$suspensionversement['motisusversrsa']] );
						$listddsusversrsa[] = h( date_short( $suspensionversement['ddsusversrsa'] ) );
					}
					echo $this->Xhtml->tableCells(
						array(
							h( $etatdosrsa[$situationdossierrsa['Situationdossierrsa']['etatdosrsa']]),
							( ( count( $listmotisusvers ) > 0 ) ? '<ul><li>'.implode( '</li><li>', $listmotisusvers ).'</li></ul>' : null ),
							( ( count( $listddsusversrsa ) > 0 ) ? '<ul><li>'.implode( '</li><li>', $listddsusversrsa ).'</li></ul>' : null ),
						),
						array( 'class' => 'odd' ),
						array( 'class' => 'even' )
					);
				?>
			</tbody>
		</table>
	<?php elseif( $situationdossierrsa['Situationdossierrsa']['etatdosrsa'] == 5 ): ?>
		<h2>Fin des droits</h2>
		<table class="aere">
			<thead>
				<tr>
					<th>Etat du dossier </th>
					<th>Motif de la cloture</th>
					<th>Date de la cloture</th>
				</tr>
			</thead>
			<tbody>
				<?php
					echo $this->Xhtml->tableCells(
						array(
							h( $etatdosrsa[$situationdossierrsa['Situationdossierrsa']['etatdosrsa']]),
							h( $moticlorsa[$situationdossierrsa['Situationdossierrsa']['moticlorsa']]),
							h( date_short($situationdossierrsa['Situationdossierrsa']['dtclorsa'] ) ),
						),
						array( 'class' => 'odd' ),
						array( 'class' => 'even' )
					);
				?>
			</tbody>
		</table>
	<?php elseif( $situationdossierrsa['Situationdossierrsa']['etatdosrsa'] == 6 ): ?>
		<h2>Fin des droits au mois antérieur</h2>
		<table class="aere">
			<thead>
				<tr>
					<th>Etat du dossier </th>
					<th>Motif de la cloture</th>
					<th>Date de la cloture</th>
				</tr>
			</thead>
			<tbody>
				<?php
					echo $this->Xhtml->tableCells(
						array(
							h( $etatdosrsa[$situationdossierrsa['Situationdossierrsa']['etatdosrsa']]),
							h( $moticlorsa[$situationdossierrsa['Situationdossierrsa']['moticlorsa']]),
							h( date_short($situationdossierrsa['Situationdossierrsa']['dtclorsa'] ) ),
						),
						array( 'class' => 'odd' ),
						array( 'class' => 'even' )
					);
				?>
			</tbody>
		</table>
	<?php endif;?>

		<h2>Historique suspension des droits</h2>
		<?php if( empty( $suspensiondroit) ):?>
			<p>Ce dossier ne possède pas de suspension de droits RSA</p>
		<?php else:?>
			<table class="aere">
				<thead>
					<tr>
						<th>Motif suspension au droit RSA</th>
						<th>Date début de suspension</th>
						<th>Date fin de suspension</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$listmotisusdos = array();
						$listddsusdrorsa = array();
						foreach( $situationdossierrsa['Suspensiondroit'] as $suspensiondroit ) {
							$listmotisusdos[] = h( $motisusdrorsa[$suspensiondroit['motisusdrorsa']] );
							$listddsusdrorsa[] = h( date_short( $suspensiondroit['ddsusdrorsa'] ) );
						}
						echo $this->Xhtml->tableCells(
							array(
								( ( count( $listmotisusdos ) > 0 ) ? '<ul><li>'.implode( '</li><li>', $listmotisusdos ).'</li></ul>' : null ),
								( ( count( $listddsusdrorsa ) > 0 ) ? '<ul><li>'.implode( '</li><li>', $listddsusdrorsa ).'</li></ul>' : null ),
								h( date_short( $situationdossierrsa['Situationdossierrsa']['dtclorsa'] ) ),
							),
							array( 'class' => 'odd' ),
							array( 'class' => 'even' )
						);
					?>
				</tbody>
			</table>
		<?php endif;?>

		<h2>Historique suspension versement des droits</h2>
		<?php if( empty( $suspensionversement) ):?>
			<p>Ce dossier ne possède pas de versement de droits RSA</p>
		<?php else:?>
			<table class="aere">
				<thead>
					<tr>
						<th>Motif suspension versement RSA</th>
						<th>Date début de suspension</th>
						<th>Date fin de suspension</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$listmotisusvers = array();
						$listddsusversrsa = array();
						foreach( $situationdossierrsa['Suspensionversement'] as $suspensionversement ) {
							$listmotisusvers[] = h( $motisusversrsa[$suspensionversement['motisusversrsa']] );
							$listddsusversrsa[] = h( date_short( $suspensionversement['ddsusversrsa'] ) );
						}
						echo $this->Xhtml->tableCells(
							array(
								( ( count( $listmotisusvers ) > 0 ) ? '<ul><li>'.implode( '</li><li>', $listmotisusvers ).'</li></ul>' : null ),
								( ( count( $listddsusversrsa ) > 0 ) ? '<ul><li>'.implode( '</li><li>', $listddsusversrsa ).'</li></ul>' : null ),
								h( date_short( $situationdossierrsa['Situationdossierrsa']['dtclorsa'] ) ),

							),
							array( 'class' => 'odd' ),
							array( 'class' => 'even' )
						);
					?>
				</tbody>
			</table>
		<?php endif;?>

<?php endif;?>