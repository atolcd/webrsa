sudo chmod a+rw app/tmp/build
sudo chmod a+rw /var/lib/php5/sess_00000000000000000000000000

sudo -u www-data ant quality -f app/Plugin/Default/Vendor/Jenkins/build.xml

wget http://localhost:8080/jnlpJars/jenkins-cli.jar
java -jar jenkins-cli.jar -s http://localhost:8080 create-job "Plugin Default" < app/Plugin/Default/Vendor/Jenkins/jobs/build.xml
java -jar jenkins-cli.jar -s http://localhost:8080 create-job "Plugin Default Qualité" < app/Plugin/Default/Vendor/Jenkins/jobs/quality.xml
