<?php
	$this->pageTitle = 'Liste des cantons';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );

	$pagination = $this->Xpaginator->paginationBlock( 'Canton', $this->passedArgs );

	if( $this->Permissions->check( 'cantons', 'add' ) ) {
		echo $this->Xhtml->tag( 'ul',
			$this->Xhtml->tag( 'li',
				$this->Xhtml->addLink( 'Ajouter un canton', array( 'action' => 'add' ) )
			),
			array( 'class' => 'actionMenu' )
		);
	}

	// Ajout du formulaire de recherche
	echo '<ul class="actionMenu"><li>'.$this->Xhtml->link(
		$this->Xhtml->image(
			'icons/application_form_magnify.png',
			array( 'alt' => '' )
		).' Formulaire',
		'#',
		array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "$( 'Search' ).toggle(); return false;" )
	).'</li></ul>';
?>
<?php echo $this->Xform->create( 'Canton', array( 'type' => 'post', 'action' => 'index', 'id' => 'Search', 'class' => ( ( is_array( $this->request->data ) && !empty( $this->request->data ) ) ? 'folded' : 'unfolded' ) ) );?>
		<fieldset>
			<?php echo $this->Xform->input( 'Canton.index', array( 'label' => false, 'type' => 'hidden', 'value' => true ) );?>

			<legend>Filtrer par Canton</legend>
			<?php
				echo $this->Default2->subform(
					array(
						'Canton.canton',
						'Canton.nomcom',
						'Canton.zonegeographique_id' => array( 'options' => $zonesgeographiques ),
						'Canton.codepos',
						'Canton.numcom'
					)
				);
			?>
		</fieldset>

		<div class="submit noprint">
			<?php echo $this->Xform->button( 'Rechercher', array( 'type' => 'submit' ) );?>
			<?php echo $this->Xform->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
		</div>

<?php echo $this->Xform->end();?>
<?php

	if( isset( $cantons ) ) {
		if( !empty( $cantons ) ) {
			$headers = array(
				$this->Xpaginator->sort( 'Canton', 'Canton.canton' ),
				$this->Xpaginator->sort( 'Zone géographique', 'Zonegeographique.libelle' ),
				$this->Xpaginator->sort( 'Type de voie', 'Canton.libtypevoie' ),
				$this->Xpaginator->sort( 'Nom de voie', 'Canton.nomvoie' ),
				$this->Xpaginator->sort( 'Localité', 'Canton.nomcom' ),
				$this->Xpaginator->sort( 'Code postal', 'Canton.codepos' ),
				$this->Xpaginator->sort( 'Code INSEE', 'Canton.numcom' )
			);
			$thead = $this->Xhtml->tag( 'thead', $this->Xhtml->tableHeaders( $headers ) );
			$thead = str_replace( '</th></tr>', '</th><th colspan="2">Actions</th></tr>', $thead );

			$rows = array();
			foreach( $cantons as $canton ) {
				$rows[] = array(
					h( Set::extract( $canton, 'Canton.canton' ) ),
					h( Set::extract( $canton, 'Zonegeographique.libelle' ) ),
					h( Set::extract( $canton, 'Canton.libtypevoie' ) ),
					h( Set::extract( $canton, 'Canton.nomvoie' ) ),
					h( Set::extract( $canton, 'Canton.nomcom' ) ),
					h( Set::extract( $canton, 'Canton.codepos' ) ),
					h( Set::extract( $canton, 'Canton.numcom' ) ),
					$this->Xhtml->editLink( 'Modifier le canton', array( 'action' => 'edit', Set::extract( $canton, 'Canton.id' ) ), $this->Permissions->check( 'cantons', 'edit' ) ),
					$this->Xhtml->deleteLink( 'Supprimer le canton', array( 'action' => 'delete', Set::extract( $canton, 'Canton.id' ) ), $this->Permissions->check( 'cantons', 'delete' ) )
				);
			}
			$tbody = $this->Xhtml->tag( 'tbody', $this->Xhtml->tableCells( $rows, array( 'class' => 'odd' ), array( 'class' => 'even' ) ) );

			echo $pagination;
			echo $this->Xhtml->tag( 'table', $thead.$tbody );
			echo $pagination;
		}
		else {
			echo $this->Xhtml->tag( 'p', 'Aucun canton n\'est renseigné pour l\'instant.', array( 'class' => 'notice' ) );
		}
	}
	echo $this->Xform->end();

	echo $this->Default->button(
		'back',
		array(
			'controller' => 'parametrages',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);
?>