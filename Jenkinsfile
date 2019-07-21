#!/usr/bin/env groovy

pipeline {

    agent {
        docker {
            image 'node'
            args '-u root'
        }
    }

    stages {
        stage('Build') {
            steps {
                echo '--> Building'
            }
        }
        stage('Test') {
            steps {
                echo '--> Testing'
            }
        }
        stage('Deploy') {
            steps {
                echo '--> Deploying'
            }
        }
    }

    post {
        success {

            echo '--> Run Success'

            script{

                def remote = [:]
                remote.name = "node-1"
                remote.host = "206.189.27.158"
                remote.allowAnyHosts = true

                withCredentials([sshUserPrivateKey(credentialsId: 'ffb49675-207f-431a-8112-114d573c905c', keyFileVariable: 'identity', passphraseVariable: '', usernameVariable: 'root')]) {
                    remote.user = "root"
                    remote.identityFile = identity

                    stage("SSH Steps Rocks!") {
                        sshCommand remote: remote, sudo: true, command: 'cd /var/www/spider'
                        sshCommand remote: remote, sudo: true, command: 'ls -la'
                    }
                }

            }
        }
    }
}
