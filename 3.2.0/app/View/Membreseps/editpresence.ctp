<?php
	$this->pageTitle = 'Présence des membres à la commission d\'EP du : '.$this->Locale->date( "Datetime::short", $commissionep['Commissionep']['dateseance']);

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>
<script type="text/javascript">
	document.observe("dom:loaded", function() {
		<?php foreach( $membres as $membre ) :?>
			dependantSelect( 'CommissionepMembreep<?php echo $membre['Membreep']['id'];?>PresencesuppleantId',  'CommissionepMembreep<?php echo $membre['Membreep']['id'];?>FonctionpresencesuppleantId' );
		<?php endforeach;?>
	});
</script>

<h1><?php echo $this->pageTitle;?></h1>

<?php
	if ( $commissionep['Commissionep']['etatcommissionep'] == 'quorum' ) {
		echo "<p class='error'>Quorum non atteint, la commission ne peut avoir lieu.</p>";
		if ( isset( $messageQuorum ) && !empty( $messageQuorum ) ) {
			echo "<p class='error'>{$messageQuorum}</p>";
		}
	}
?>

<?php echo $this->Xform->create( 'Membreep', array( 'type' => 'post', 'url' => array( 'controller' => 'membreseps', 'action' => 'editpresence', $seance_id ) ) ); ?>
	<div class="aere">
		<fieldset>
			<legend>Liste des participants</legend>
			<?php
				echo "<table id='listeParticipants'>";
				foreach($fonctionsmembres as $fonction) {
					echo $this->Xhtml->tag(
						'tr',
						$this->Xhtml->tag(
							'td',
							$fonction['Fonctionmembreep']['name'].' :',
							array(
								'colspan' => 4
							)
						)
					);
					foreach( $membres as $membre ) {

						if ( $membre['Membreep']['fonctionmembreep_id'] == $fonction['Fonctionmembreep']['id'] ) {
							if ( empty( $membre['CommissionepMembreep']['presence'] ) ) {
								if ( $membre['CommissionepMembreep']['reponse'] == 'confirme' )
									$defaut = 'present';
								elseif ( $membre['CommissionepMembreep']['reponse'] == 'remplacepar' )
									$defaut = 'remplacepar';
								else
									$defaut = 'excuse';
							}
							else {
								$defaut = $membre['CommissionepMembreep']['presence'];
							}

// 							if ( !empty( $membre['CommissionepMembreep']['presencesuppleant_id'] ) ) {
// 								$valueDefaut = $membre['CommissionepMembreep']['fonctionpresencesuppleant_id'].'_'.$membre['CommissionepMembreep']['presencesuppleant_id'];
// 							}
// 							elseif ( !empty( $membre['CommissionepMembreep']['reponsesuppleant_id'] ) ) {
// 								$membre['CommissionepMembreep']['presencesuppleant_id'] = $membre['CommissionepMembreep']['fonctionreponsesuppleant_id'].'_'.$membre['CommissionepMembreep']['reponsesuppleant_id'];
// 							}
// 							else {
// 								$valueDefaut = null;
// 							}

							echo $this->Xhtml->tag(
								'tr',
								$this->Xhtml->tag(
									'td',
									implode( ' ', array( $membre['Membreep']['qual'], $membre['Membreep']['nom'], $membre['Membreep']['prenom'] ) )
								).
								$this->Xhtml->tag(
									'td',
									$this->Form->input(
										'CommissionepMembreep.'.$membre['Membreep']['id'].'.presence',
										array(
											'type' => 'select',
											'label' => false,
											'default' => $defaut,
											'options' => $options['CommissionepMembreep']['presence'],
											'class' => 'presence'
										)
									),
									array(
										'colspan' => 1,
										'id' => 'presence_membre_'.$membre['Membreep']['id']
									)
								).
								$this->Xhtml->tag(
									'td',
									$this->Form->input(
										'CommissionepMembreep.'.$membre['Membreep']['id'].'.fonctionpresencesuppleant_id',
										array(
											'label' => false,
											'type' => 'select',
											'empty' => true,
											'options' => $options['Membreep']['fonctionmembreep_id']//@$membres_fonction[$membre['Membreep']['fonctionmembreep_id']]
										)
									)
								).
								$this->Xhtml->tag(
									'td',
									$this->Form->input(
										'CommissionepMembreep.'.$membre['Membreep']['id'].'.presencesuppleant_id',
										array(
											'label' => false,
											'type' => 'select',
											'empty' => true,
											'options' => $membres_fonction//@$membres_fonction[$membre['Membreep']['fonctionmembreep_id']]
										)
									)
								)
							);
						}
					}
				}
				echo "</table>";
			?>
		</fieldset>
	</div>

<?php echo $this->Xform->end( 'Enregistrer' );?>

<?php
	if ( Configure::read( 'Cg.departement' ) == 93 ) {
		echo $this->Default->button(
			'back',
			array(
				'controller' => 'commissionseps',
				'action'     => 'arbitrageep'
			),
			array(
				'id' => 'Back'
			)
		);
	}
	else {
		echo $this->Default->button(
			'back',
			array(
				'controller' => 'commissionseps',
				'action'     => 'view',
				$seance_id
			),
			array(
				'id' => 'Back'
			)
		);
	}
?>



<script type="text/javascript">
	document.observe("dom:loaded", function() {
		<?php foreach( $membres as $membre ) { ?>
			$( 'CommissionepMembreep<?php echo $membre['Membreep']['id'] ?>Presence' ).observe( 'change', function() {
				checkPresence( <?php echo $membre['Membreep']['id'] ?> );
			} );
			checkPresence( <?php echo $membre['Membreep']['id'] ?> );
		<?php } ?>
	} );

	function checkPresence( id ) {
		if ( $( 'CommissionepMembreep'+id+'Presence' ).getValue() == 'remplacepar' ) {
			$( 'presence_membre_'+id ).writeAttribute('colspan', 1);
			$( 'CommissionepMembreep'+id+'FonctionpresencesuppleantId' ).writeAttribute( 'disabled', false );
			$( 'CommissionepMembreep'+id+'FonctionpresencesuppleantId' ).up('td').show();
			$( 'CommissionepMembreep'+id+'PresencesuppleantId' ).writeAttribute( 'disabled', false );
			$( 'CommissionepMembreep'+id+'PresencesuppleantId' ).up('td').show();
		}
		else {
			$( 'presence_membre_'+id ).writeAttribute('colspan', 2);
			$( 'CommissionepMembreep'+id+'FonctionpresencesuppleantId' ).writeAttribute( 'disabled', 'disabled' );
			$( 'CommissionepMembreep'+id+'FonctionpresencesuppleantId' ).up('td').hide();
			$( 'CommissionepMembreep'+id+'PresencesuppleantId' ).writeAttribute( 'disabled', 'disabled' );
			$( 'CommissionepMembreep'+id+'PresencesuppleantId' ).up('td').hide();
		}
	}
</script>

<div class="clearer"><hr /></div>