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
            sh './var/www/spider/deploy.sh'

            //dir("") {
            //
            //    echo '--> Before deploy'
            //
            //    echo '--> After Deploy'
            // }
        }
    }
}
