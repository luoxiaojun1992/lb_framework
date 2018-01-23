#! /bin/bash

#./vendor/bin/phpunit --coverage-xml ./reports ./tests

mv ./vendor ../vendor-bak
~/sonar-scanner-3.0.3.778-macosx/bin/sonar-scanner   -Dsonar.projectKey=lb-framework   -Dsonar.sources=.   -Dsonar.host.url=http://localhost:9000   -Dsonar.login=fe511242e2fb1be1f486b62de3ecc173847e684c -Dsonar.language=php
mv ../vendor-bak ./vendor
