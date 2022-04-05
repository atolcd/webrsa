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
<h1><?= __m('validation.titre')?></h1>
<br><br>
<?php
if($success){
    echo'<h2>'.sprintf(__d('algorithmeorientation', 'validation.message'), $nb_orientations).'</h2>';
    echo '<br><br><br><br><br><br>';
}
echo '<ul class="actionMenu center">'
.'<li>'
. $this->Xhtml->link(
    __m('retouraccueil'),
    array( 'controller' => 'accueils', 'action' => 'index' )
)
.'</li>'
.'</ul>';