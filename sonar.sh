#! /bin/bash
mv ./vendor ../vendor-bak
~/sonar-scanner-3.0.3.778-macosx/bin/sonar-scanner   -Dsonar.projectKey=lb-framework   -Dsonar.sources=.   -Dsonar.host.url=http://localhost:9000   -Dsonar.login=5d5df6457a356c0b3dc62cb6cf664df6439c8945 -Dsonar.language=php
mv ../vendor-bak ./vendor
