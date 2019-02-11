#!/usr/bin/env groovy

pipeline {
  agent {
    label 'docker&&ovh'
  }
  environment {
     PROJECT = sh(script: './jenkins-env.sh PROJECT', returnStdout: true).trim()
     ZULIP_STREAM = sh(script: './jenkins-env.sh ZULIP_STREAM', returnStdout: true).trim()
     NEXUS_REPO = sh(script: './jenkins-env.sh NEXUS_REPO', returnStdout: true).trim()
     TYPE = sh(script: './jenkins-env.sh TYPE', returnStdout: true).trim()
     VERSION = sh(script: './jenkins-env.sh VERSION', returnStdout: true).trim()
     BASE_URL_UPLOAD = sh(script: './jenkins-env.sh BASE_URL_UPLOAD', returnStdout: true).trim()
     BASE_URL_FINAL = sh(script: './jenkins-env.sh BASE_URL_FINAL', returnStdout: true).trim()
     APP_FILENAME_TAR = sh(script: './jenkins-env.sh APP_FILENAME_TAR', returnStdout: true).trim()
  }
  stages {
    stage('Notify') {
      steps {
        script {
          zulipSend stream: "$ZULIP_STREAM", topic: "jenkins-$TYPE", message: ":gear: Start build : $VERSION"
        }
      }
    }
    stage('Build') {
      steps {
        script {
          docker.image("docker-registry.priv.atolcd.com/atolcd/php:7.2-1.6").inside('-v "/var/lib/jenkins/composer/auth.json:/home/.composer/auth.json"') {
            sh 'composer install --no-progress --no-dev --no-suggest'
          }
        }
        script {
          sh 'ls final > /dev/null 2>&1 && rm -fr final || true'
          sh 'mkdir final && cp -RLp app vendor final/ || true'
          sh 'find final/ -type d -exec chmod 750 {} \\; && find final/ -type f -exec chmod 640 {} \\;'
        }
      }
    }
    stage('Package') {
      parallel{
        stage('Application tar') {
          steps {
            script {
              sh 'cd final && tar -chpzf "../$APP_FILENAME_TAR" *'
            }
          }
        }
      }
    }
    stage('Upload') {
      parallel{
        stage('Application tar upload') {
          steps {
            withCredentials([usernameColonPassword(credentialsId: 'nexus3-jenkins', variable: 'NEXUS3_AUTH')]) {
              sh 'curl -v --user "$NEXUS3_AUTH" --upload-file ./$APP_FILENAME_TAR $BASE_URL_UPLOAD/tar/$APP_FILENAME_TAR'
            }
          }
        }
      }
      post {
        success {
          zulipSend stream: "$ZULIP_STREAM", topic: "jenkins-$TYPE", message: ":check: Build success $VERSION"
        }
        failure {
          zulipSend stream: "$ZULIP_STREAM", topic: "jenkins-$TYPE", message: ":prohibited: Build failed $VERSION"
        }
      }
    }
    stage('Tag release') {
      when {
        environment name: 'TYPE', value: 'release'
      }
      steps {
        script {
          sh('git tag -a $VERSION -m "$VERSION"')
          sh('git push --tags')
        }
      }
    }
  }
  post {
    always {
      deleteDir()
    }
  }
}
