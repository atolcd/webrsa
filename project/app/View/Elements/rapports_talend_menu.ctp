<?php

if( false === isset( $visionneuse ) ) {
    $visionneuse = false;
}
if( false === isset( $creance ) ) {
    $creance = false;
}
if( false === isset( $contact ) ) {
    $contact = false;
}
if( false === isset( $ali ) ) {
    $ali = false;
}


$actions =  array(
    '/Visionneuses/index' => array(
        'title' => __d('visionneuses', 'Visionneuse::index::title'),
        'text' => __d('visionneuses', 'Visionneuse::index::link'),
        'class' => 'link',
        'enabled' => !$visionneuse
    ),
    '/Rapportstalendscreances/index' => array(
        'title' => __d('visionneuses', 'Rapportstalendscreances::index::title'),
        'text' => __d('visionneuses', 'Rapportstalendscreances::index::link'),
        'class' => 'link',
        'enabled' => !$creance
    ),
    '/Rapportstalendsmodescontacts/index' => array(
        'title' => __d('visionneuses', 'Rapportstalendsmodescontacts::index::title'),
        'text' => __d('visionneuses', 'Rapportstalendsmodescontacts::index::link'),
        'class' => 'link',
        'enabled' => !$contact
    ),
    '/Rapportsechangesali/index' => array(
        'title' => __d('visionneuses', 'Rapportsechangesali::index::title'),
        'text' => __d('visionneuses', 'Rapportsechangesali::index::link'),
        'class' => 'link',
        'enabled' => !$ali
    )
);

echo $this->Default3->actions( $actions );