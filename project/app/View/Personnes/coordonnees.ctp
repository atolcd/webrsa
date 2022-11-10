<?php
    if( Configure::read( 'debug' ) > 0 ) {
        echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
    }

    $title = "{$personne['Personne']['nom']} {$personne['Personne']['prenom']}";
    $this->pageTitle = 'Modification des coordonnées de « '.$title.' »';
?>

<h1><?php echo $this->pageTitle;?></h1>

<?php echo $this->Xform->create('Personne');?>
<div>
    <?php
        echo "<div class='aere";
        if (  !empty($errors['fixe'][0] ) ) {
            echo " error";
        }
        echo "'>";
        echo $this->Xform->input( 'Personne.numfixe', array( 'type' => 'text', 'domain' => 'personne' ) );
        if ( !empty($errors['fixe'][0] )) {
            echo $this->Xhtml->tag(
                'div',
                $errors['fixe'][0],
                array(
                    'class' => 'error-message'
                )
            );
        }
        echo "</div>";

        echo "<div class='aere";
        if (  !empty($errors['mobile'][0] ) ) {
            echo " error";
        }
        echo "'>";
        echo $this->Xform->input( 'Personne.numport', array( 'type' => 'text', 'domain' => 'personne' ) );
        if ( !empty($errors['mobile'][0] )) {
            echo $this->Xhtml->tag(
                'div',
                $errors['mobile'][0],
                array(
                    'class' => 'error-message'
                )
            );
        }
        echo "</div>";

        echo "<div class='aere";
            if (  !empty($errors['email'][0] ) ) {
                echo " error";
            }
        echo "'>";
        echo $this->Xform->input( 'Personne.email', array( 'type' => 'text', 'domain' => 'personne' ) );
        if ( !empty($errors['email'][0] )) {
            echo $this->Xhtml->tag(
                'div',
                $errors['email'][0],
                array(
                    'class' => 'error-message'
                )
            );
        }
        echo "</div>";
    ?>
</div>

<div class="submit">
    <?php
        echo $this->Xform->submit( 'Enregistrer', array( 'div' => false ) );
        echo $this->Xform->submit( 'Annuler', array( 'name' => 'Cancel', 'div' => false ) );
    ?>
</div>
<?php echo $this->Xform->end();?>