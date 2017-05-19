sudo chmod a+rw app/tmp/build
sudo -u www-data ant quality -f app/Plugin/AppClasses/Vendor/Jenkins/build.xml

wget http://localhost:8080/jnlpJars/jenkins-cli.jar
java -jar jenkins-cli.jar -s http://localhost:8080 create-job "Plugin AppClasses" < app/Plugin/AppClasses/Vendor/Jenkins/jobs/build.xml
java -jar jenkins-cli.jar -s http://localhost:8080 create-job "Plugin AppClasses Qualité" < app/Plugin/AppClasses/Vendor/Jenkins/jobs/quality.xml
