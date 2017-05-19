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
        echo $this->Xform->input( 'Personne.numfixe', array( 'type' => 'text', 'domain' => 'personne' ) );
        echo $this->Xform->input( 'Personne.numport', array( 'type' => 'text', 'domain' => 'personne' ) );
        echo $this->Xform->input( 'Personne.email', array( 'type' => 'text', 'domain' => 'personne' ) );
    ?>
</div>

<div class="submit">
    <?php
        echo $this->Xform->submit( 'Enregistrer', array( 'div' => false ) );
        echo $this->Xform->submit( 'Annuler', array( 'name' => 'Cancel', 'div' => false ) );
    ?>
</div>
<?php echo $this->Xform->end();?>