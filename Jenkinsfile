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
            steps {

                def remote = [:]
                remote.name = "node-1"
                remote.host = "206.189.27.158"
                remote.allowAnyHosts = true

                node{

                    withCredentials([sshUserPrivateKey(credentialsId: 'ffb49675-207f-431a-8112-114d573c905c', keyFileVariable: 'identity', passphraseVariable: '', usernameVariable: 'root')]) {
                        remote.user = userName
                        remote.identityFile = identity
                        stage("SSH Steps Rocks!") {
                            writeFile file: 'abc.sh', text: 'ls'
                            sshCommand remote: remote, command: 'for i in {1..5}; do echo -n \"Loop \$i \"; date ; sleep 1; done'
                            sshPut remote: remote, from: 'abc.sh', into: '.'
                            sshGet remote: remote, from: 'abc.sh', into: 'bac.sh', override: true
                            sshScript remote: remote, script: 'abc.sh'
                            sshRemove remote: remote, path: 'abc.sh'
                        }
                    }
                }
            }
        }
    }
}
