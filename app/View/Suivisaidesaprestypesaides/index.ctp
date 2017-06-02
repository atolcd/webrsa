<?php echo $this->Xform->create( 'Suiviaideapretypeaide' );?>
<div>
<h1><?php echo $this->pageTitle = 'Paramétrage des types d\'aides en fonction des personnes chargés du suivi';?></h1>
	<?php if( empty( $suivisaidesaprestypesaides ) ):?>
	<ul class="actionMenu">
		<?php
			echo '<li>'.$this->Xhtml->addLink(
				'Ajouter',
				array( 'controller' => 'suivisaidesaprestypesaides', 'action' => 'add' ),
				$this->Permissions->check( 'suivisaidesaprestypesaides', 'add' )
			).' </li>';
		?>
	</ul>

		<p class="notice">Aucune aide présente pour le moment.</p>
	<?php else:?>
		<ul class="actionMenu">
			<?php
				echo '<li>'.$this->Xhtml->editLink(
					'Modifier',
					array( 'controller' => 'suivisaidesaprestypesaides', 'action' => 'edit' ),
					$this->Permissions->check( 'suivisaidesaprestypesaides', 'edit' )
				).' </li>';
			?>
		</ul>
	<div>
		<h2>Table des types d'aides en fonction des personnes chargées du suivi</h2>
		<table>
		<thead>
			<tr>
				<th>Type d'aide</th>
				<th>Personne responsable</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach( $suivisaidesaprestypesaides as $suiviaideapretypeaide ):?>
				<?php echo $this->Xhtml->tableCells(
					array(
						h( Set::enum( Set::classicExtract( $suiviaideapretypeaide, 'Suiviaideapretypeaide.typeaide' ), $natureAidesApres ) ),
						h( Set::enum( Set::classicExtract( $suiviaideapretypeaide, 'Suiviaideapretypeaide.suiviaideapre_id' ), $personnessuivis ) ),
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);
				?>
			<?php endforeach;?>
			</tbody>
		</table>
	</div>
	<?php endif;?>
	<div class="submit">
		<?php
			echo $this->Form->submit( 'Retour', array( 'name' => 'Cancel', 'div' => false ) );
		?>
	</div>
</div>
<div class="clearer"><hr /></div>
<?php echo $this->Form->end();?>