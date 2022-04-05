<div class="steps">
	<span class="steps-prog" style="width: 100%;"></span>
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
    <a class="step step-optional step-passed" href="#">
        <span class="shape"></span>
        <span class="text"><?=__m('bandeau.depassements')?></span>
    </a>
    <a class="step step-main step-active" href="#">
        <span class="shape">
            <span class="number">3</span>
        </span>
        <span class="text"><?=__m('bandeau.simulation')?></span>
    </a>
</div>


<h1><?=__m('affichagesimulation.titre')?></h1>
<br><br>
<h2><?=sprintf(__d('algorithmeorientation', 'nborientables.titre'), $stats['parcours']['value']['Total'])?></h2>
<br><br>
<?php
echo '<ul class="actionMenu">'
		.'<li>'
		. $this->Xhtml->link(
			__m('listeorientable.download'),
			array( 'controller' => 'algorithmeorientation', 'action' => 'exportcsv_orientations' )
		)
		.'</li>'
        .'<li>'
		. $this->Xhtml->link(
            __m('statsorientable.download'),
			array( 'controller' => 'algorithmeorientation', 'action' => 'exportcsv_statsorientations' )
		)
		.'</li>'
	.'</ul>';
?>
<div class= 'affichagesimulation'>
    <table>
        <thead>
            <th><?= __m('ville') ?></th>
            <?php foreach ($stats['parcours']['value'] as $nom_parcours => $total_parcours): ?>
                <th><?= $nom_parcours?></th>
            <?php endforeach; ?>
        </thead>
        <tbody>
            <?php
            $class = ['odd','even'];
            $i = 0;
            foreach ($stats['villes']['value'] as $nom_ville => $parcours): ?>
                <tr class=<?=$class[intval($i%2 == 0)]?>>
                    <td><?= $nom_ville?></td>
                    <?php foreach ($parcours as $nom => $nombre): ?>
                        <td><?= $nombre.' ('.$stats['villes']['pourcentage'][$nom_ville][$nom].')'?></td>
                    <?php endforeach; ?>
                </tr>
            <?php $i++;
            endforeach; ?>
            <tr>
                <td><?= __m('total') ?></td>
                <?php foreach ($stats['parcours']['value'] as $nom_parcours => $tot_parcours): ?>
                        <td><?= $tot_parcours.' ('.$stats['parcours']['pourcentage'][$nom_parcours].')'?></td>
                <?php endforeach; ?>
            </tr>
        </tbody>
    </table>

    <div class="charts">
        <div style="width: 35vw; height: 70vh" id="chart_parcours"></div>
        <div style="width: 35vw; height: 70vh" id="chart_role"></div>
        <div style="width: 35vw; height: 70vh" id="chart_dsp"></div>
    </div>
</div>
<br><br><br>

<?php

//TODO Rappeler le nombre de personnes à orienter dans le message de confirmation
echo '<ul class="actionMenu center">'
.'<li>'
. $this->Xhtml->link(
    __m('validerorientations'),
    array( 'controller' => 'algorithmeorientation', 'action' => 'validerOrientations' ),
    [],
    sprintf(__d('algorithmeorientation', 'validerorientation.confirm'), $stats['parcours']['value']['Total'])
)
.'</li>'
.'<li>'
. $this->Xhtml->link(
    __m('retourorientables'),
    array( 'controller' => 'algorithmeorientation', 'action' => 'affichageOrientables' ),
    [],
    __m('retourorientables.confirm')
)
.'</li>'
.'</ul>';

?>

<?php
    echo $this->Html->css('toastui-chart.min.css');
	echo $this->Html->script('toastui-chart');
?>
<script type="text/javascript">
    const infos = <?= json_encode($stats)?>;;
	// doc : https://github.com/nhn/tui.chart/blob/main/docs/
	// lib : https://nhn.github.io/tui.chart/latest/tutorial-example09-02-pie-chart-dataLabels

    var series = [];

    var options = {
        chart: { title: '', width: 500, height: 500 },
        series: {
            dataLabels: {
                visible: true,
                pieSeriesName: {
                    visible: true,
                },
            },
        },
        legend: {
            visible : true,
            showCheckbox: false,
            align: 'bottom',
        },
        exportMenu: {
            visible: false
        }
    };

    //Graphique parcours
    series = [];
    delete infos.parcours.value.Total
    for (const property in infos.parcours.value) {
        if(infos.parcours.value[property] != 0){
            series.push({'name': property, 'data': infos.parcours.value[property]})
        }
    }
    options.chart.title = '<?= __m("graphique_parcours.titre")?>';
    var chart_age = toastui.Chart.pieChart({ el : document.getElementById('chart_parcours'), data : {series: series}, options });

    //Graphique dsp
    series = [];
    for (const property in infos.dsp.value) {
        if(infos.dsp.value[property] != 0){
            series.push({'name': property, 'data': infos.dsp.value[property]})
        }
    }
    options.chart.title = '<?= __m("graphique_dsp.titre")?>';
    var chart_age = toastui.Chart.pieChart({ el : document.getElementById('chart_dsp'), data : {series: series}, options });

    //Graphique role
    series = [];
    for (const property in infos.role.value) {
        if(infos.role.value[property] != 0){
            series.push({'name': property, 'data': infos.role.value[property]})
        }
    }
    options.chart.title = '<?= __m("graphique_role.titre")?>';
    var chart_age = toastui.Chart.pieChart({ el : document.getElementById('chart_role'), data : {series: series}, options });

</script>