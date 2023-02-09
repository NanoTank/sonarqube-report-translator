@Library('shared-jenkins-libraries')
import groovy.transform.Field

autocancelConsecutiveBuilds()

pipeline {
    agent {
        // Please choose lowmem / mediummem / highmem
        label 'medmem'
    }
    options {
        disableResume()
        ansiColor('xterm')
        buildDiscarder(
                logRotator(
                        numToKeepStr: '10',
                )
        )
        retry(0)
        timeout(time: 30, unit: 'MINUTES')
        copyArtifactPermission('*');
    }

    stages {
    }
    post {
        always {
            script {
                buildInfo.endBuild()
            }
        }
        cleanup {
            deleteDir()
            cleanWs()
        }
    }
}