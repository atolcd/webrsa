<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	$title_for_layout = 'Indicateurs mensuels';
	$this->set( compact( 'title_for_layout' ) );
?>
<h1><?php echo $title_for_layout;?></h1>

<?php
	echo $this->Form->create( 'Search', array( 'type' => 'post', 'class' => 'noprint', 'novalidate' => true ) );

	echo $this->Form->input( 'Search.year', array( 'label' => __d( 'indicateursmensuels58', 'Search.year' ), 'type' => 'select', 'options' => $options['Search']['year'] ) );
	echo $this->Form->input( 'Search.sitecov58_id', array( 'label' => __d( 'indicateursmensuels58', 'Search.site' ), 'type' => 'select', 'options' => $options['Search']['sitecov58_id'], 'empty' => true ) );

	echo $this->Form->submit( 'Calculer' );
	echo $this->Form->end();
?>

<?php if( isset( $results ) ): ?>
	<table>
		<thead>
			<?php
				$tr = $this->Html->tag( 'th', null, array( 'colspan' => 2 ) );

				foreach( range( 1, 12 ) as $month ) {
					$tr .= $this->Html->tag( 'th', strftime( '%B', strtotime( "2013-{$month}-01" ) ) );
				}

				$tr .= $this->Html->tag( 'th', 'Total annuel' );

				echo $this->Html->tag( 'tr', $tr );
			?>
		</thead>
		<tbody>
			<?php
				// Partie CAF
				$i = 0;
				foreach( $results['Personnecaf'] as $theme => $data ) {
					$tr = '';
					if( $i == 0 ) {
						$tr .= $this->Html->tag( 'th', __d( 'indicateursmensuels58', 'Personnecaf' ), array( 'rowspan' => count( $results['Personnecaf'] ), 'class' => 'section' ) );
					}
					$tr .= $this->Html->tag( 'th', __d( 'indicateursmensuels58', "Personnecaf.{$theme}" ), array( 'class' => ( strpos( $theme, 'total' ) === 0 ) ? 'section' : null ) );

					foreach( $data as $month => $number ) {
						$tr .= $this->Html->tag( 'td', $number, array( 'class' => 'number' ) );
					}

					echo $this->Html->tag( 'tr', $tr, array( 'class' => ( ( $i == 0 || strpos( $theme, 'total' ) === 0 ) ? 'total' : null ) ) );
					$i++;
				}
			?>
			<?php
				// Partie COV
				$i = 0;
				foreach( $results['Dossiercov58'] as $theme => $data ) {
					$tr = '';
					if( $i == 0 ) {
						$tr .= $this->Html->tag( 'th', __d( 'indicateursmensuels58', 'Dossiercov58' ), array( 'rowspan' => count( $results['Dossiercov58'] ), 'class' => 'section' ) );
					}
					$tr .= $this->Html->tag( 'th', __d( 'indicateursmensuels58', "Dossiercov58.{$theme}" ) );

					foreach( $data as $month => $number ) {
						$tr .= $this->Html->tag( 'td', $number, array( 'class' => 'number' ) );
					}

					echo $this->Html->tag( 'tr', $tr, array( 'class' => ( $i == 0 ? 'total' : null ) ) );
					$i++;
				}
			?>
			<?php
				// Partie EP
				$i = 0;
				foreach( $results['Dossierep'] as $theme => $data ) {
					$tr = '';
					if( $i == 0 ) {
						$tr .= $this->Html->tag( 'th', __d( 'indicateursmensuels58', 'Dossierep' ), array( 'rowspan' => count( $results['Dossierep'] ), 'class' => 'section' ) );
					}
					$tr .= $this->Html->tag( 'th', __d( 'indicateursmensuels58', "Dossierep.{$theme}" ) );

					foreach( $data as $month => $number ) {
						$tr .= $this->Html->tag( 'td', $number, array( 'class' => 'number' ) );
					}

					echo $this->Html->tag( 'tr', $tr, array( 'class' => ( $i == 0 ? 'total' : null ) ) );
					$i++;
				}
			?>
		</tbody>
	</table>
<?php endif;?>