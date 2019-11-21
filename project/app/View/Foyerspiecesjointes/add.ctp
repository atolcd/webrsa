<?php
	$this->pageTitle =  __m( 'Foyerspiecesjointes/add::heading' );

    echo $this->Xhtml->tag( 'h1', $this->pageTitle );

    echo $this->Xform->create( 'Foyerspiecesjointes', array('type' => 'post', 'id' => 'FoyerspiecesjointesAddEditForm', 'enctype' => 'multipart/form-data' ) );
?>
<fieldset>
    <legend> <?php echo __m('Foyerspiecesjointes/add::legend'); ?> </legend>
    <fieldset id="filecontainer-piecejointe" class="noborder invisible">
    <?php
        echo $this->Fileuploader->create(
            isset($fichier) ? $fichier : array(),
            array( 'action' => 'ajaxfileupload' )
        );

	    echo $this->Default3->subform( array(
            'Foyerspiecesjointes.categorie_id' => array(
                'type' => 'select',
                'empty' => false,
                'required' => true,
                'options' => $listeCategorie,
            )
        ) );
    ?>
    </fieldset>
    <?php echo $this->Fileuploader->validation( 'FoyerspiecesjointesAddEditForm', 'Foyerspiecesjointes');?>
</fieldset>
<?php
echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
echo $this->Default3->DefaultForm->end();

echo '<h2>' . __m('Foyerspiecesjointe/pjpresentes') . '</h2>';
echo $this->Fileuploader->results($piecesjointes);