1°) Copie de l'ensemble des fichiers techniques CNAF nécessaires aux shells FluxCnaf de web-rsa vers /tmp
find -regex "\(.*/Schema/.*\.xsd\|.*DICO.xml\)" -exec cp {} /tmp \;

2°) Comparaisons entre le nouveau et l'ancien flux bénéficiaire
sudo -u www-data lib/Cake/Console/cake Fluxcnaf.FluxcnafSchema /tmp/vrsd0301.xsd,/tmp/vrsb0801.xsd
sudo -u www-data lib/Cake/Console/cake Fluxcnaf.FluxcnafDico compare /tmp/vrsd0301DICO.xml,/tmp/vrsb0801DICO.xml

3°) Comparaisons, etc.. avec l'ensemble des (nouveaux) flux
sudo -u www-data lib/Cake/Console/cake Fluxcnaf.FluxcnafSchema /tmp/vrsd0301.xsd,/tmp/virs0901.xsd,/tmp/vird0201.xsd,/tmp/vrsc0201.xsd,/tmp/vrsf0501.xsd
sudo -u www-data lib/Cake/Console/cake Fluxcnaf.FluxcnafDico compare /tmp/vrsd0301DICO.xml,/tmp/virs0901DICO.xml,/tmp/vird0201DICO.xml,/tmp/vrsc0201DICO.xml,/tmp/vrsf0501DICO.xml
sudo -u www-data lib/Cake/Console/cake Fluxcnaf.FluxcnafDico locale /tmp/vrsd0301DICO.xml,/tmp/virs0901DICO.xml,/tmp/vird0201DICO.xml,/tmp/vrsc0201DICO.xml,/tmp/vrsf0501DICO.xml
sudo -u www-data lib/Cake/Console/cake Fluxcnaf.FluxcnafDico in_list /tmp/vrsd0301DICO.xml,/tmp/virs0901DICO.xml,/tmp/vird0201DICO.xml,/tmp/vrsc0201DICO.xml,/tmp/vrsf0501DICO.xml