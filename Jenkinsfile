pipeline {
    agent any

    environment {
        POS_BASE        = 'https://quizquestapp.com/pos-sim1'
        HEALTH_URL      = "${POS_BASE}/health.php"
        REPORT_FILE     = 'pos_test_report.txt'
        PERF_LIMIT_MS   = '4000'

        // Toggle optional heavy tests
        RUN_SELENIUM    = 'true'
        RUN_JMETER      = 'true'
        RUN_ZAP         = 'true'
    }

    stages {

        stage('Prepare Workspace') {
            steps {
                echo 'Preparing workspace...'
                bat '''
                if not exist build mkdir build
                if not exist results mkdir results
                echo Workspace prepared > build\\workspace_status.txt
                '''
            }
        }

        stage('Health Check') {
            steps {
                powershell '''
                try {
                    $response = Invoke-WebRequest -Uri $env:HEALTH_URL -UseBasicParsing -TimeoutSec 20
                    if ($response.StatusCode -eq 200) {
                        Write-Host "SUCCESS: Health endpoint reachable"
                    } else {
                        Write-Error "FAILED: Health endpoint returned $($response.StatusCode)"
                        exit 1
                    }
                } catch {
                    Write-Error "ERROR: Health check failed - $($_.Exception.Message)"
                    exit 1
                }
                '''
            }
        }

        stage('Basic Login Test') {
            steps {
                withCredentials([usernamePassword(
                    credentialsId: 'pos-login-creds',
                    usernameVariable: 'POS_USER',
                    passwordVariable: 'POS_PASS'
                )]) {
                    powershell '''
                    try {
                        $pair = "$env:POS_USER`:$env:POS_PASS"
                        $encoded = [Convert]::ToBase64String([Text.Encoding]::ASCII.GetBytes($pair))
                        $headers = @{ Authorization = "Basic $encoded" }

                        $response = Invoke-WebRequest -Uri "$env:POS_BASE/index.php" -Headers $headers -UseBasicParsing -TimeoutSec 20

                        if ($response.StatusCode -eq 200) {
                            Write-Host "SUCCESS: Basic login test passed"
                        } else {
                            Write-Error "FAILED: Login returned status $($response.StatusCode)"
                            exit 1
                        }
                    } catch {
                        Write-Error "ERROR: Login test failed - $($_.Exception.Message)"
                        exit 1
                    }
                    '''
                }
            }
        }

        stage('Protected Page Coverage Test') {
            steps {
                withCredentials([usernamePassword(
                    credentialsId: 'pos-login-creds',
                    usernameVariable: 'POS_USER',
                    passwordVariable: 'POS_PASS'
                )]) {
                    powershell '''
                    $pair = "$env:POS_USER`:$env:POS_PASS"
                    $encoded = [Convert]::ToBase64String([Text.Encoding]::ASCII.GetBytes($pair))
                    $headers = @{ Authorization = "Basic $encoded" }

                    $pages = @(
                        @{ url = "$env:POS_BASE/index.php";    name = "index.php"    },
                        @{ url = "$env:POS_BASE/product.php";  name = "product.php"  },
                        @{ url = "$env:POS_BASE/cart.php";     name = "cart.php"     },
                        @{ url = "$env:POS_BASE/checkout.php"; name = "checkout.php" }
                    )

                    "POS Remote Page Coverage Results" | Out-File results\\pos_function_result.txt -Encoding utf8

                    foreach ($page in $pages) {
                        try {
                            $response = Invoke-WebRequest -Uri $page.url -Headers $headers -UseBasicParsing -TimeoutSec 20
                            if ($response.StatusCode -eq 200) {
                                "PASS: $($page.name)" | Out-File results\\pos_function_result.txt -Append -Encoding utf8
                                Write-Host "PASS: $($page.name)"
                            } else {
                                "FAIL: $($page.name) returned $($response.StatusCode)" | Out-File results\\pos_function_result.txt -Append -Encoding utf8
                                Write-Error "FAIL: $($page.name) returned $($response.StatusCode)"
                                exit 1
                            }
                        } catch {
                            "ERROR: $($page.name) - $($_.Exception.Message)" | Out-File results\\pos_function_result.txt -Append -Encoding utf8
                            Write-Error "ERROR: $($page.name) - $($_.Exception.Message)"
                            exit 1
                        }
                    }
                    '''
                }
            }
        }

        stage('Page Content Validation') {
            steps {
                withCredentials([usernamePassword(
                    credentialsId: 'pos-login-creds',
                    usernameVariable: 'POS_USER',
                    passwordVariable: 'POS_PASS'
                )]) {
                    powershell '''
                    $pair = "$env:POS_USER`:$env:POS_PASS"
                    $encoded = [Convert]::ToBase64String([Text.Encoding]::ASCII.GetBytes($pair))
                    $headers = @{ Authorization = "Basic $encoded" }

                    $checks = @(
                        @{ url = "$env:POS_BASE/index.php";    expected = "Simple POS Simulation"; page = "index.php" },
                        @{ url = "$env:POS_BASE/product.php";  expected = "Available Products";     page = "product.php" },
                        @{ url = "$env:POS_BASE/cart.php";     expected = "Cart Items";             page = "cart.php" },
                        @{ url = "$env:POS_BASE/checkout.php"; expected = "Checkout Form";          page = "checkout.php" }
                    )

                    "Page Content Validation Results" | Out-File results\\content_validation.txt -Encoding utf8

                    foreach ($check in $checks) {
                        try {
                            $response = Invoke-WebRequest -Uri $check.url -Headers $headers -UseBasicParsing -TimeoutSec 20
                            if ($response.Content -match [regex]::Escape($check.expected)) {
                                "PASS: $($check.page) contains '$($check.expected)'" | Out-File results\\content_validation.txt -Append -Encoding utf8
                                Write-Host "PASS: $($check.page) content validated"
                            } else {
                                "FAIL: $($check.page) missing expected text '$($check.expected)'" | Out-File results\\content_validation.txt -Append -Encoding utf8
                                Write-Error "FAIL: $($check.page) missing expected text"
                                exit 1
                            }
                        } catch {
                            "ERROR: $($check.page) - $($_.Exception.Message)" | Out-File results\\content_validation.txt -Append -Encoding utf8
                            Write-Error "ERROR: $($check.page) - $($_.Exception.Message)"
                            exit 1
                        }
                    }
                    '''
                }
            }
        }

        stage('Checkout Form POST Test') {
            steps {
                withCredentials([usernamePassword(
                    credentialsId: 'pos-login-creds',
                    usernameVariable: 'POS_USER',
                    passwordVariable: 'POS_PASS'
                )]) {
                    powershell '''
                    $pair = "$env:POS_USER`:$env:POS_PASS"
                    $encoded = [Convert]::ToBase64String([Text.Encoding]::ASCII.GetBytes($pair))
                    $headers = @{ Authorization = "Basic $encoded" }

                    $form = @{
                        customer = "Jenkins Test User"
                        payment_method = "Cash"
                    }

                    try {
                        $response = Invoke-WebRequest -Uri "$env:POS_BASE/checkout.php" -Method POST -Body $form -Headers $headers -UseBasicParsing -TimeoutSec 20

                        if ($response.StatusCode -eq 200 -and $response.Content -match "Checkout successful") {
                            Write-Host "SUCCESS: Checkout form POST test passed"
                            "PASS: Checkout POST succeeded" | Out-File results\\checkout_post_result.txt -Encoding utf8
                        } else {
                            Write-Error "FAILED: Checkout form POST test failed"
                            exit 1
                        }
                    } catch {
                        Write-Error "ERROR: Checkout POST failed - $($_.Exception.Message)"
                        exit 1
                    }
                    '''
                }
            }
        }

        stage('Response Time Test') {
            steps {
                withCredentials([usernamePassword(
                    credentialsId: 'pos-login-creds',
                    usernameVariable: 'POS_USER',
                    passwordVariable: 'POS_PASS'
                )]) {
                    powershell '''
                    $pair = "$env:POS_USER`:$env:POS_PASS"
                    $encoded = [Convert]::ToBase64String([Text.Encoding]::ASCII.GetBytes($pair))
                    $headers = @{ Authorization = "Basic $encoded" }

                    try {
                        $sw = [System.Diagnostics.Stopwatch]::StartNew()
                        $response = Invoke-WebRequest -Uri "$env:POS_BASE/index.php" -Headers $headers -UseBasicParsing -TimeoutSec 20
                        $sw.Stop()

                        $elapsedMs = $sw.ElapsedMilliseconds
                        "Response time: $elapsedMs ms" | Out-File results\\perf_result.txt -Encoding utf8

                        if ($response.StatusCode -ne 200) {
                            Write-Error "FAILED: Response status was $($response.StatusCode)"
                            exit 1
                        }

                        if ($elapsedMs -gt [int]$env:PERF_LIMIT_MS) {
                            Write-Error "FAILED: Response time $elapsedMs ms exceeded limit $env:PERF_LIMIT_MS ms"
                            exit 1
                        } else {
                            Write-Host "SUCCESS: Response time $elapsedMs ms within limit"
                        }
                    } catch {
                        Write-Error "ERROR: Response time test failed - $($_.Exception.Message)"
                        exit 1
                    }
                    '''
                }
            }
        }

        stage('404 Negative Test') {
            steps {
                powershell '''
                try {
                    $badUrl = "$env:POS_BASE/does-not-exist.php"
                    Invoke-WebRequest -Uri $badUrl -UseBasicParsing -TimeoutSec 20
                    Write-Error "FAILED: Expected 404 but page responded successfully"
                    exit 1
                } catch {
                    if ($_.Exception.Message -match "404") {
                        Write-Host "SUCCESS: 404 negative test passed"
                        "PASS: 404 returned as expected" | Out-File results\\negative_test_result.txt -Encoding utf8
                    } else {
                        Write-Error "FAILED: Unexpected error during 404 test - $($_.Exception.Message)"
                        exit 1
                    }
                }
                '''
            }
        }

        stage('Security Headers Test') {
            steps {
                withCredentials([usernamePassword(
                    credentialsId: 'pos-login-creds',
                    usernameVariable: 'POS_USER',
                    passwordVariable: 'POS_PASS'
                )]) {
                    powershell '''
                    $pair = "$env:POS_USER`:$env:POS_PASS"
                    $encoded = [Convert]::ToBase64String([Text.Encoding]::ASCII.GetBytes($pair))
                    $headers = @{ Authorization = "Basic $encoded" }

                    try {
                        $response = Invoke-WebRequest -Uri "$env:POS_BASE/index.php" -Headers $headers -UseBasicParsing -TimeoutSec 20
                        $wanted = @("X-Content-Type-Options","X-Frame-Options")

                        "Security Header Results" | Out-File results\\security_headers.txt -Encoding utf8

                        foreach ($h in $wanted) {
                            if ($response.Headers[$h]) {
                                "PASS: $h = $($response.Headers[$h])" | Out-File results\\security_headers.txt -Append -Encoding utf8
                                Write-Host "PASS: Header $h present"
                            } else {
                                "WARN: $h missing" | Out-File results\\security_headers.txt -Append -Encoding utf8
                                Write-Host "WARN: Header $h missing"
                            }
                        }
                    } catch {
                        Write-Error "ERROR: Security header test failed - $($_.Exception.Message)"
                        exit 1
                    }
                    '''
                }
            }
        }


stage('Selenium UI Test') {
   when {
                environment name: 'RUN_SELENIUM', value: 'true'
            }

    steps {
        withCredentials([usernamePassword(
            credentialsId: 'pos-login-creds',
            usernameVariable: 'POS_USER',
            passwordVariable: 'POS_PASS'
        )]) {
		
		bat '''
        echo Current directory:
        cd

        echo Listing files:
        dir
        dir tests

        python -m pip install --upgrade pip
        python -m pip install selenium

        python tests\\selenium_ui_test.py
        '''
        }
    }
}


    
stage('JMeter Load Test') {
 when {
                environment name: 'RUN_JMETER', value: 'true'
            }  

  steps {
        withCredentials([usernamePassword(
            credentialsId: 'pos-login-creds',
            usernameVariable: 'POS_USER',
            passwordVariable: 'POS_PASS'
        )]) {
            bat '''
            echo Running JMeter load test...

            cd
            dir
            dir tests

            if not exist results mkdir results
            if not exist results\\jmeter mkdir results\\jmeter

            echo Cleaning old JMeter reports...

            if exist results\\jmeter\\html (
                rmdir /s /q results\\jmeter\\html
            )

            if exist results\\jmeter\\jmeter.jtl (
                del /q results\\jmeter\\jmeter.jtl
            )

            echo Starting JMeter...

            C:\\Tools\\apache-jmeter-5.6.3\\bin\\jmeter.bat -n ^
             -t tests\\pos_load_test.jmx ^
             -Jpos_user=%POS_USER% ^
             -Jpos_pass=%POS_PASS% ^
             -l results\\jmeter\\jmeter.jtl ^
             -e -o results\\jmeter\\html

            '''
        }
    }
}


stage('OWASP ZAP Scan (No Docker)') {
    when {
        environment name: 'RUN_ZAP', value: 'true'
    }
    steps {
        bat '''
        echo Running ZAP Scan...

        if not exist results mkdir results
        if not exist results\\zap mkdir results\\zap

        cd "C:\\Program Files\\ZAP\\Zed Attack Proxy"

        zap.bat -cmd ^
         -quickurl https://quizquestapp.com/pos-sim/ ^
         -quickout "%WORKSPACE%\\results\\zap\\zap_report.html"

        echo ZAP scan completed
        '''
    }
}

stage('Mark Release Ready') {
    steps {
        bat '''
        echo RELEASE READY > results\\release_ready.txt
        '''
    }
}


stage('Deploy to WebServer') {
    steps {
        // upload files
    }
}

        stage('Generate Report') {
            steps {
                powershell '''
                $report = @"
POS Remote Extended CI/CD Test Report
=====================================
1. Health check                : PASS
2. Basic login                 : PASS
3. Protected page coverage     : PASS
4. Page content validation     : PASS
5. Checkout form POST          : PASS
6. Response time               : PASS
7. 404 negative test           : PASS
8. Security headers            : REVIEW
9. Selenium UI                 : $env:RUN_SELENIUM
10. JMeter load                : $env:RUN_JMETER
11. OWASP ZAP                  : $env:RUN_ZAP
"@
                $report | Out-File -FilePath $env:REPORT_FILE -Encoding utf8
                Write-Host "Extended remote test report generated"
                '''
            }
        }
    }

    post {
        always {
            archiveArtifacts artifacts: 'build/*.txt,results/**/*.txt,results/**/*.jtl,results/**/*.html,*.txt', fingerprint: true
            echo 'Artifacts archived successfully.'
        }
        success {
            echo 'SUCCESS: Extended remote POS pipeline completed successfully.'
        }
        failure {
            echo 'FAILED: Extended remote POS pipeline failed. Review the stage logs.'
        }
    }
}
