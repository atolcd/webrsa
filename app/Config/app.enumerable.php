<?php
	/**
	* Types enumerables
	* FIXME: autrement qu'avec le configure ?
    *
    * FIXME: si le nom du champ est presence (cf. comiteapre_participantcomite)
    * -> c'est imprédictible (ajouter la notion d'alias -> plutôt que ENUM::TYPE::XX -> ENUM::ALIAS::XX )
    * Attention, si on définit des values pour le code ci-dessous, on aura à la fois les values en dur
    * + ce qui vient de la BDD
    *
        var $actsAs = array(
            'Enumerable' => array(
                'fields' => array(
                    'presence' => array(
                        'type' => 'presence',
                        'domain' => 'apre',
                        'values' => array(
                            'PRE',
                            'ABS',
                            'EXC'
                        )
                    )
                )
            )
        );
	*/

	Configure::write(
		'Enumerable',
		array(
			'presence' => array(
				'domain' => 'default',
				'type' => 'presence',
				'values' => array(
					'absent',
					'present',
					'remplace',
					'excuse'
				)
			),
			'no' => array(
				'domain' => 'default',
				'type' => 'no',
				'values' => array(
					'O',
					'N'
				)
			),
			'booleannumber' => array(
				'domain' => 'default',
				'type' => 'booleannumber',
				'values' => array(
					'1',
					'0'
				)
			)
		)
	);
?>