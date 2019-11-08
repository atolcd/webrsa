<?php

	App::uses('WebrsaAccess', 'Utility');
	WebrsaAccess::init($dossierMenu);

    echo '<h1>' . __m('/Foyerspiecesjointes/archivage/:heading') . '</h1>';
?>

<!-- Bouton pour retourner à l'index -->
<ul class="actions">
    <li class="action">
    <?php
        echo $this->Xhtml->link(
            __m('/Foyerpiecejointe/index'),
            array('controller' => 'foyerspiecesjointes', 'action' => 'index', $foyer_id),
            array(
                'title' => __m('/Foyerpiecejointe/index:title'),
                'class' => 'back'
            )
        );
    ?>
    </li>
</ul>
<?php
    // Visualisation des pièces jointes archivées
    if( empty($pjArchives) ) {
        echo '<p class="notice">' . __m('Foyerpiecejointe::archivage::nopjarchive') . '</p>';
    } else {
        echo $this->Default3->index(
            $pjArchives,
            $this->Translator->normalize(
                array(
                    'Foyerpiecejointe.nom',
                    'Foyerpiecejointe.created',
                    'User.username',
                    'Categoriepiecejointe.nom',
                ) + WebrsaAccess::links(
                    array(
                        '/Foyerspiecesjointes/view/#Foyerpiecejointe.id#',
                        '/Foyerspiecesjointes/edit/#Foyerpiecejointe.id#',
                        '/Foyerspiecesjointes/dearchive/#Foyerpiecejointe.id#' => array(
                            'class' => 'edit'
                        ),
                        '/Foyerspiecesjointes/delete/#Foyerpiecejointe.id#'
                    )
                )
            ),
            array(
                'paginate' => false,
            )
        );
    }