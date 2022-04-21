<div class="steps">
	<span class="steps-prog" style="width: 50%;"></span>
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
	<a class="step step-main step-active" href="#">
        <span class="shape">
            <span class="number">2</span>
        </span>
        <span class="text"><?=__m('bandeau.orientables')?></span>
    </a>
    <a class="step step-optional" href="#">
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
<h1><?=__m('liste.titre')?></h1>
<?php
$actions['/algorithmeorientation/orientation'] =  [
    'text' => __m('form.back'),
    'class' => 'search'
];
echo $this->Default3->actions( $actions );
?>
<br><br>
<h2><?= sprintf(__d('algorithmeorientation', 'nborientables.titre'), count($resultats))?></h2>
<br><br>
<?php
    echo '<ul class="actionMenu">'
		.'<li>'
		. $this->Xhtml->link(
			__m('listeorientable.download'),
			array( 'controller' => 'algorithmeorientation', 'action' => 'exportcsv_orientables' )
		)
		.'</li>'
        .'<li>'
		. $this->Xhtml->link(
            __m('statsorientable.download'),
			array( 'controller' => 'algorithmeorientation', 'action' => 'exportcsv_statsorientables' )
		)
		.'</li>'
	.'</ul>';
?>
<!-- tableau pour les répartitions par ville -->
<div class= 'listeorientables'>
    <table>
        <thead>
            <th><?= __m('ville') ?></th>
            <th><?= __m('nb_orientables') ?></th>
            <th><?= __m('pourcentage_orientables') ?></th>
        </thead>
        <tbody>
            <?php
            $class = ['odd','even'];
            $i = 0;
            foreach ($infos_graphiques['ville']['value'] as $nom => $value): ?>
            <tr class=<?=$class[intval($i%2 == 0)]?>>
                <td><?= $nom?></td>
                <td><?= $value?></td>
                <td><?= $infos_graphiques['ville']['pourcentage'][$nom]?></td>
            </tr>
            <?php $i++;
            endforeach; ?>
        </tbody>
    </table>


    <div class="charts">
        <div style="width: 30vw; height: 50vh" id="chart_ept"></div>
        <div style="width: 30vw; height: 50vh" id="chart_age"></div>
        <div style="width: 30vw; height: 50vh" id="chart_sexe"></div>
        <div style="width: 30vw; height: 50vh" id="chart_dsp"></div>
        <div style="width: 30vw; height: 50vh" id="chart_role"></div>
    </div>
</div>

<?php
    echo '<ul class="actionMenu center">'
		.'<li>'
		.  $this->Xhtml->link(
            __m('simulationalgo'),
            array( 'controller' => 'algorithmeorientation', 'action' => 'simulationAlgo')
        )
		.'</li>'
	.'</ul>';
?>

<?php
    echo $this->Html->css('toastui-chart.min.css');
	echo $this->Html->script('toastui-chart');
?>
<script type="text/javascript">
    const infos = <?= json_encode($infos_graphiques)?>;;
	// doc : https://github.com/nhn/tui.chart/blob/main/docs/
	// lib : https://nhn.github.io/tui.chart/latest/tutorial-example09-02-pie-chart-dataLabels

    var series = [];

    var options = {
        chart: { title: '', width: 'auto', height: 'auto' },
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

    //Graphique ept
    series = [];
    for (const property in infos.ept.value) {
        if(infos.ept.value[property] != 0){
            series.push({'name': property, 'data': infos.ept.value[property]})
        }
    }
    options.chart.title = '<?= __m("graphique_ept.titre")?>';
    var chart_ept = toastui.Chart.pieChart({ el : document.getElementById('chart_ept'), data : {series: series}, options });

    //Graphique age
    series = [];
    for (const property in infos.age.value) {
        if(infos.age.value[property] != 0){
            series.push({'name': property, 'data': infos.age.value[property]})
        }
    }
    options.chart.title = '<?= __m("graphique_age.titre")?>';
    var chart_age = toastui.Chart.pieChart({ el : document.getElementById('chart_age'), data : {series: series}, options });

    //Graphique sexe
    series = [];
    for (const property in infos.sexe.value) {
        if(infos.sexe.value[property] != 0){
            series.push({'name': property, 'data': infos.sexe.value[property]})
        }
    }
    options.chart.title = '<?= __m("graphique_sexe.titre")?>';
    var chart_age = toastui.Chart.pieChart({ el : document.getElementById('chart_sexe'), data : {series: series}, options });

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