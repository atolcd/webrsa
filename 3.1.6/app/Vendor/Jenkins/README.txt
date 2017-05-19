mkdir app/tmp/build
sudo chmod a+rw app/tmp/build
wget http://localhost:8080/jnlpJars/jenkins-cli.jar

web-rsa (app)
=============
sudo -u www-data ant quality -f app/Vendor/Jenkins/build-app.xml

java -jar jenkins-cli.jar -s http://localhost:8080 create-job "WebRSA trunk app" < app/Vendor/Jenkins/jobs/app-build.xml
java -jar jenkins-cli.jar -s http://localhost:8080 create-job "WebRSA trunk app Qualité" < app/Vendor/Jenkins/jobs/app-quality.xml

module Apre
==========================
sudo -u www-data ant quality -f app/Vendor/Jenkins/build-module-Apre.xml

java -jar jenkins-cli.jar -s http://localhost:8080 create-job "WebRSA trunk module Apre" < app/Vendor/Jenkins/jobs/module-Apre-build.xml
java -jar jenkins-cli.jar -s http://localhost:8080 create-job "WebRSA trunk module Apre Qualité" < app/Vendor/Jenkins/jobs/module-Apre-quality.xml

module Ficheprescription93
==========================
sudo -u www-data ant quality -f app/Vendor/Jenkins/build-module-Ficheprescription93.xml

java -jar jenkins-cli.jar -s http://localhost:8080 create-job "WebRSA trunk module Ficheprescription93" < app/Vendor/Jenkins/jobs/module-Ficheprescription93-build.xml
java -jar jenkins-cli.jar -s http://localhost:8080 create-job "WebRSA trunk module Ficheprescription93 Qualité" < app/Vendor/Jenkins/jobs/module-Ficheprescription93-quality.xml

module Pcg66
==========================
sudo -u www-data ant quality -f app/Vendor/Jenkins/build-module-Pcg66.xml

java -jar jenkins-cli.jar -s http://localhost:8080 create-job "WebRSA trunk module Pcg66" < app/Vendor/Jenkins/jobs/module-Pcg66-build.xml
java -jar jenkins-cli.jar -s http://localhost:8080 create-job "WebRSA trunk module Pcg66 Qualité" < app/Vendor/Jenkins/jobs/module-Pcg66-quality.xml

module Romev3
==========================
sudo -u www-data ant quality -f app/Vendor/Jenkins/build-module-Romev3.xml

java -jar jenkins-cli.jar -s http://localhost:8080 create-job "WebRSA trunk module Romev3" < app/Vendor/Jenkins/jobs/module-Romev3-build.xml
java -jar jenkins-cli.jar -s http://localhost:8080 create-job "WebRSA trunk module Romev3 Qualité" < app/Vendor/Jenkins/jobs/module-Romev3-quality.xml

module Recherches
=================
sudo -u www-data ant quality -f app/Vendor/Jenkins/build-module-Recherches.xml

java -jar jenkins-cli.jar -s http://localhost:8080 create-job "WebRSA trunk module Recherches" < app/Vendor/Jenkins/jobs/module-Recherches-build.xml
java -jar jenkins-cli.jar -s http://localhost:8080 create-job "WebRSA trunk module Recherches Qualité" < app/Vendor/Jenkins/jobs/module-Recherches-quality.xml