<?php

echo $this->element(
    'WebrsaParametrages/add_edit',
    array(
        'fields' => array(
            'Exceptionsimpression.id',
            'Exceptionsimpression.origine' => array( 'type' => 'select', 'options' => $options['origines'] ),
            'Exceptionsimpression.act' => array( 'type' => 'select', 'options' => $options['Activite']['act'] ),
            'Exceptionsimpression.porteurprojet' => array( 'type' => 'select', 'options' => $options['porteurprojet'] ),
            'Exceptionsimpression.modele_notif',
            'Exceptionsimpression.actif',
            'Exceptionsimpression.typeorient_id' => array( 'type' => 'hidden', 'value' => $typeorient_id),
            'Exceptionsimpression.ordre' => array( 'type' => 'hidden', 'value' => $options['ordre'])
        ),
    )
);
