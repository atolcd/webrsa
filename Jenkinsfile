#!/usr/bin/env groovy

library 'atolcd-jenkins'

pipeline {
  options {
    disableConcurrentBuilds()
    buildDiscarder(logRotator(numToKeepStr: '10'))
  }
  agent any
  environment {
     PROJECT = bashEval('./jenkins-env.sh PROJECT')
     ZULIP_STREAM = bashEval('./jenkins-env.sh ZULIP_STREAM')
     NEXUS_REPO = bashEval('./jenkins-env.sh NEXUS_REPO')
     TYPE = bashEval('./jenkins-env.sh TYPE')
     VERSION = bashEval('./jenkins-env.sh VERSION')
     APP_FILENAME_TAR = bashEval('./jenkins-env.sh APP_FILENAME_TAR')
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
        phpInsideDocker(imageVersion: '7.2-2.0') {
          sh 'composer install --no-progress --no-dev --no-suggest'
        }
        script {
          sh 'echo $VERSION > app/VERSION.txt'
          sh 'ls final > /dev/null 2>&1 && rm -fr final || true'
          sh 'mkdir final && cp -RLp app vendor final/ || true'
          sh 'find final/ -type d -exec chmod 750 {} \\; && find final/ -type f -exec chmod 640 {} \\;'
          sh 'chmod 755 final/app/webrsa.sh'
        }
      }
    }
    stage('Package tar') {
      steps {
        sh 'cd final && tar -chpzf "../$APP_FILENAME_TAR" *'
      }
    }
    stage('Upload tar') {
      steps {
        publishRawNexus repository: env.NEXUS_REPO, remoteDir: "${env.PROJECT}/tar", files: env.APP_FILENAME_TAR
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
