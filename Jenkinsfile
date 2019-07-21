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
            dir("/var/www/spider") {

                // echo '--> Before sudo'
                // sh 'sudo su'
                // echo '--> After sudo'

                echo '--> Before deploy'
                sh './deploy.sh'
                echo '--> After Deploy'
            }
        }
    }
}
