<?php
	$this->pageTitle =  __m( 'Foyerspiecesjointes/edit::heading' );

    echo $this->Xhtml->tag( 'h1', $this->pageTitle );
    echo $this->Xform->create( 'Foyerspiecesjointes', array('type' => 'post', 'id' => 'FoyerspiecesjointesAddEditForm', 'enctype' => 'multipart/form-data' ) );
?>
<fieldset>
    <fieldset id="filecontainer-piecejointe" class="noborder invisible">
    <?php
        echo __m( 'Foyerspiecesjointes.nomFichier' ) . '<b>' . $fichier['Fichiermodule']['name'] . '</b><br>';

	    echo $this->Default3->subform( array(
            'Foyerspiecesjointes.categorie_id' => array(
                'type' => 'select',
                'empty' => false,
                'required' => true,
                'options' => $listeCategorie,
                'selected' => $fichier['Categoriepiecejointe']['id']
            )
        ) );
    ?>
    </fieldset>
</fieldset>
<?php
echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
echo $this->Default3->DefaultForm->end();