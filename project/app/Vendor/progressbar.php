<?php

    function Initialize($gauche,$haut,$largeur,$hauteur,$bord_col,$txt_col,$bg_col) {
        $tailletxt=$hauteur-10;

        echo '<div id="contTemp" style="position:absolute;top:0;left:0px;';
        echo 'background-image:url(../../../img/header.png);';
        echo 'width:1000px; height:250px; margin-left: 0px; padding: 0px;">';

        echo '<div id="pourcentage" style="position:absolute;top:'.$haut;
        echo ';left:'.$gauche;
        echo ';width:'.$largeur.'px';
        echo ';height:'.$hauteur.'px;border:1px solid '.$bord_col.';font-family:Tahoma;font-weight:bold';
        echo ';font-size:'.$tailletxt.'px;color:'.$txt_col.';z-index:1;text-align:center;">0%</div>';

        echo '<div id="progrbar" style="position:absolute;top:'.($haut+1); //+1
        echo ';left:'.($gauche+1); //+1
        echo ';width:0px';
        echo ';height:'.$hauteur.'px';
        echo ';background-color:'.$bg_col.';z-index:0;"></div>';

        echo '<div id="affiche" style="position:absolute;top:'.($haut+$hauteur+15);
        echo ';left:'.($gauche+1);
        echo ';width:'.($largeur*2).'px;';
        echo 'height: 30px; font-size: 10px;';
        echo 'z-index:0;"></div>';
        echo '</div>';
    }

    function ProgressBar($indice, $affiche) {
        echo "<script>";
        echo "document.getElementById(\"pourcentage\").innerHTML='".round($indice)."%';";
        echo "document.getElementById(\"affiche\").innerHTML='".$affiche."';";
        echo "document.getElementById('progrbar').style.width=".($indice*2).";";
        echo "</script>";
        flush();
    }

?>