<div class="steps">
	<span class="steps-prog" style="width: 75%;"></span>
    <a class="step step-main step-passed" href="#">
        <span class="shape">
            <span class="number">1</span>
        </span>
        <span class="text"><?=__m('bandeau.recherche')?></span>
    </a>
    <a class="step step-optional step-passed" href="#">
        <span class="shape"></span>
        <span class="text"><?=__m('bandeau.adresses')?></span>
    </a>
	<a class="step step-main step-passed" href="#">
        <span class="shape">
            <span class="number">2</span>
        </span>
        <span class="text"><?=__m('bandeau.orientables')?></span>
    </a>
    <a class="step step-optional step-active" href="#">
        <span class="shape"></span>
        <span class="text"><?=__m('bandeau.depassements')?></span>
    </a>
    <a class="step step-main" href="#">
        <span class="shape">
            <span class="number">3</span>
        </span>
        <span class="text"><?=__m('bandeau.simulation')?></span>
    </a>
</div>
<h1><?=__m('depassements.titre')?></h1>
<br>

<?= $this->Default3->DefaultForm->create()?>

<table>
    <thead>
        <th><?= __m('lib_struc')?></th>
        <th><?= __m('nb_actuel')?></th>
        <th><?= __m('nb_nouveaux')?></th>
        <th><?= __m('capacite_max')?></th>
        <th><?= __m('depassement')?></th>
        <th><?= __m('action')?></th>
        <th><?= __m('telechargement')?></th>
    </thead>
    <tbody>
        <?php
        $class = ['odd','even'];
        $i = 0;
        foreach ($depassements as $id_struct => $array): ?>
        <tr class=<?=$class[intval($i%2 == 0)]?>>
            <td><?= $array['lib_struc']?></td>
            <td><?= $array['nb_actuel']?></td>
            <td><?= $array['nb_nouveaux']?></td>
            <td><?= $array['capacite_max']?></td>
            <td><?= $array['nb_depassement']?></td>
            <td><?= $this->Form->input($id_struct, ['label' => false, 'type' => 'select' , 'options' => $options_actions] )?></td>
            <td><?=
                $this->Xhtml->link(
                    __m('nouveaux.telecharger'),
                    array( 'controller' => 'algorithmeorientation', 'action' => 'exportcsv_depassement', $id_struct )
                )
            ?> </td>
        </tr>
        <?php $i++;
        endforeach; ?>
    </tbody>
</table>

<?php
	echo $this->Default3->DefaultForm->buttons( array( 'valider' => ['label' => __m('depassements.valider')]) );
	echo $this->Default3->DefaultForm->end();
?>