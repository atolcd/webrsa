sudo chmod a+rw app/tmp/build
sudo -u www-data ant quality -f app/Plugin/Prototype/Vendor/Jenkins/build.xml

wget http://localhost:8080/jnlpJars/jenkins-cli.jar
java -jar jenkins-cli.jar -s http://localhost:8080 create-job "Plugin Prototype" < app/Plugin/Prototype/Vendor/Jenkins/jobs/build.xml
java -jar jenkins-cli.jar -s http://localhost:8080 create-job "Plugin Prototype Qualité" < app/Plugin/Prototype/Vendor/Jenkins/jobs/quality.xml
