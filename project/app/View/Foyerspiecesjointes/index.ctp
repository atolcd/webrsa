<?php

	App::uses('WebrsaAccess', 'Utility');
	WebrsaAccess::init($dossierMenu);

    //echo $this->element('default_index');
    echo '<h1>' . __m('/Foyerspiecesjointes/index/:heading') . '</h1>';
?>

<!-- Bouton pour ajouter & accéder aux fichiers archivés -->
<ul class="actions">
    <li class="action">
    <?php
        echo $this->Xhtml->link(
            __m('/Foyerspiecesjointes/add'),
            array('controller' => 'foyerspiecesjointes', 'action' => 'add', $foyer_id),
            array(
                'title' => __m('/Foyerspiecesjointes/add:title'),
                'enabled' => WebrsaAccess::isEnabled(array('/Foyerspiecesjointes/add' => true), '/Foyerspiecesjointes/add'),
                'class' => 'add link'
            )
        );
    ?>
    </li>
    <li class="action">
    <?php
        echo $this->Xhtml->link(
            __m('/Foyerpiecejointe/archivage') . ' (' . $nbPieceArchives . ')',
            array('controller' => 'foyerspiecesjointes', 'action' => 'archivage', $foyer_id),
            array(
                'title' => __m('/Foyerpiecejointe/archivage:title'),
                'enabled' => $pjArchivesActif,
                'class' => 'edit link',
            )
        );
    ?>
    </li>
</ul>
<?php
    //Visualisation des pièces jointes
    if( empty($pjNonArchives) ) {
        echo '<p class="notice">' . __m('Foyerpiecejointe::index::nopjnonarchive') . '</p>';
    } else {
        echo $this->Default3->index(
            $pjNonArchives,
            $this->Translator->normalize(
                array(
                    'Fichiermodule.name',
                    'Foyerpiecejointe.created',
                    'User.username',
                    'Categoriepiecejointe.nom'
                ) + WebrsaAccess::links(
                    array(
                        '/Foyerspiecesjointes/view/#Foyerpiecejointe.id#',
                        '/Foyerspiecesjointes/edit/#Foyerpiecejointe.id#',
                        '/Foyerspiecesjointes/archive/#Foyerpiecejointe.id#' => array(
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
?>