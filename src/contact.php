<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'dependency/PHPMailer/src/Exception.php';
require 'dependency/PHPMailer/src/PHPMailer.php';
require 'dependency/PHPMailer/src/SMTP.php';
require_once 'config.php';

session_start();

if (isset($_POST['cemail'])) {
    // Check if submission cookie exists
    if (isset($_COOKIE['contact-submitted'])) {
        $_SESSION['error'] = 'We limited the number of submissions to once a day. Please wait for your concern to be addressed.';
    } else {
        if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) { 
            $secretKey = '6Ld5yh8rAAAAACiw8pZF--6HW30qGsv6RNRHWKPY';
            $verifyResponse = file_get_contents(
                'https://www.google.com/recaptcha/api/siteverify?secret=' . $secretKey . 
                '&response=' . $_POST['g-recaptcha-response']
            ); 
            $responseData = json_decode($verifyResponse); 

            if ($responseData->success) {
                $message = $_POST['concern'] . "<br><br>" . $_POST['message'] . "<br><br>" . "Sent by: " . $_POST['cemail'];
                $email = 'renzjan.moncinilla@umak.edu.ph';
                $mail = new PHPMailer(true);

                try {
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'renzjan.moncinilla@umak.edu.ph';
                    $mail->Password   = 'lqdn wude utoj smds';
                    $mail->SMTPSecure = 'tls';
                    $mail->Port       = 587;

                    $mail->setFrom('no-reply@scholarfinds.com', 'Scholar Finds');
                    $mail->addAddress($email);
                    $mail->isHTML(true);
                    $mail->Subject = 'Scholar Finds Concern Form';
                    $mail->Body    = $message;

                    $mail->send();

                    // Set cookie to prevent more submissions today (24 hours)
                    setcookie('contact-submitted', true, time() + 86400, "/");

                    $_SESSION['success'] = 'Thank you! Your concern has been received by us.';
                } catch (Exception $e) {
                    $_SESSION['error'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            }
        }
    }
    unset($_POST);
}

?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholar Finds</title>
    <link rel="shortcut icon" href="resources/sf-logo.svg" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link href="./output.css" rel="stylesheet">
</head>
<body class="bg-[url('resources/lib-bg.jpg')] font-nunito text-white flex flex-col overflow-hidden">
    <div class="fixed inset-0 bg-black/50 h-screen z-0"></div>
    <!-- DESKTOP SIDE NAVIGATIONS -->
    <header class="group fixed top-0 left-0 pt-10 pb-10 w-20 hover:w-60 duration-500 ease-out h-screen flex max-tablet:hidden flex-col justify-between bg-ash/85 backdrop-blur-md shadow-[var(--around-shadow-md)] select-none text-off-white z-10">
        <div class="w-full h-35">
            <img src="resources/umak.svg" alt="UMak Logo" class="mt-3 ml-3.5 size-12 inline-block">
            <img src="resources/ccis.svg" alt="CCIS Logo" class="mt-3 ml-3.5 size-12 inline-block">  
            <img src="resources/sf-logo.svg" alt="Scholar Finds Logo" class="mt-3 ml-3.5 size-12 inline-block">
            <a href="index.html" class="outline-none"><h1 class=" m-3.5 whitespace-nowrap overflow-hidden text-3xl opacity-0 group-hover:opacity-100 duration-500 font-semibold">Scholar Finds</h1></a>
        </div>
        <nav>
            <ul class="flex flex-col gap-2">
                <li><a href="index.html" class="flex items-center gap-8 pl-5 py-2 hover:opacity-60 duration-200 ease-linear">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="min-w-8 w-8">
                        <path d="M11.47 3.841a.75.75 0 0 1 1.06 0l8.69 8.69a.75.75 0 1 0 1.06-1.061l-8.689-8.69a2.25 2.25 0 0 0-3.182 0l-8.69 8.69a.75.75 0 1 0 1.061 1.06l8.69-8.689Z" />
                        <path d="m12 5.432 8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 0 1-.75-.75v-4.5a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75V21a.75.75 0 0 1-.75.75H5.625a1.875 1.875 0 0 1-1.875-1.875v-6.198a2.29 2.29 0 0 0 .091-.086L12 5.432Z" />
                    </svg> 
                    <p class="text-lg overflow-hidden text-clip">Home</p>
                </a></li>
                <li><a href="about.html" class="flex items-center gap-8 pl-5 py-2 hover:opacity-60 duration-200 ease-linear">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="min-w-8 w-8">
                        <path fill-rule="evenodd" d="M8.25 6.75a3.75 3.75 0 1 1 7.5 0 3.75 3.75 0 0 1-7.5 0ZM15.75 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM2.25 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM6.31 15.117A6.745 6.745 0 0 1 12 12a6.745 6.745 0 0 1 6.709 7.498.75.75 0 0 1-.372.568A12.696 12.696 0 0 1 12 21.75c-2.305 0-4.47-.612-6.337-1.684a.75.75 0 0 1-.372-.568 6.787 6.787 0 0 1 1.019-4.38Z" clip-rule="evenodd" />
                        <path d="M5.082 14.254a8.287 8.287 0 0 0-1.308 5.135 9.687 9.687 0 0 1-1.764-.44l-.115-.04a.563.563 0 0 1-.373-.487l-.01-.121a3.75 3.75 0 0 1 3.57-4.047ZM20.226 19.389a8.287 8.287 0 0 0-1.308-5.135 3.75 3.75 0 0 1 3.57 4.047l-.01.121a.563.563 0 0 1-.373.486l-.115.04c-.567.2-1.156.349-1.764.441Z" />
                    </svg>                       
                    <p class="text-lg overflow-hidden text-clip">About</p>
                </a></li>
                <li><a href="contact.php" class="flex items-center gap-8 pl-5 py-2 hover:opacity-60 duration-200 ease-linear">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="min-w-8 w-8">
                        <path fill-rule="evenodd" d="M1.5 4.5a3 3 0 0 1 3-3h1.372c.86 0 1.61.586 1.819 1.42l1.105 4.423a1.875 1.875 0 0 1-.694 1.955l-1.293.97c-.135.101-.164.249-.126.352a11.285 11.285 0 0 0 6.697 6.697c.103.038.25.009.352-.126l.97-1.293a1.875 1.875 0 0 1 1.955-.694l4.423 1.105c.834.209 1.42.959 1.42 1.82V19.5a3 3 0 0 1-3 3h-2.25C8.552 22.5 1.5 15.448 1.5 6.75V4.5Z" clip-rule="evenodd" />
                    </svg>                      
                    <p class="text-lg overflow-hidden text-clip">Contact</p>
                </a></li>
                <li><a href="library.php" class="flex items-center gap-8 pl-5 py-2 hover:opacity-60 duration-200 ease-linear">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="min-w-8 w-8">
                        <path d="M11.25 4.533A9.707 9.707 0 0 0 6 3a9.735 9.735 0 0 0-3.25.555.75.75 0 0 0-.5.707v14.25a.75.75 0 0 0 1 .707A8.237 8.237 0 0 1 6 18.75c1.995 0 3.823.707 5.25 1.886V4.533ZM12.75 20.636A8.214 8.214 0 0 1 18 18.75c.966 0 1.89.166 2.75.47a.75.75 0 0 0 1-.708V4.262a.75.75 0 0 0-.5-.707A9.735 9.735 0 0 0 18 3a9.707 9.707 0 0 0-5.25 1.533v16.103Z" />
                    </svg>                      
                    <p class="text-lg overflow-hidden text-clip">Library</p>
                </a></li>
            </ul>
        </nav>
        <menu class="flex flex-col gap-2">
            <li><a href="profile.php" class="flex items-center gap-8 pl-5 py-2 hover:opacity-60 duration-200 ease-linear">
                <div id="profile-nav">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="min-w-8 w-8">
                        <path fill-rule="evenodd" d="M18.685 19.097A9.723 9.723 0 0 0 21.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 0 0 3.065 7.097A9.716 9.716 0 0 0 12 21.75a9.716 9.716 0 0 0 6.685-2.653Zm-12.54-1.285A7.486 7.486 0 0 1 12 15a7.486 7.486 0 0 1 5.855 2.812A8.224 8.224 0 0 1 12 20.25a8.224 8.224 0 0 1-5.855-2.438ZM15.75 9a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <p class="text-lg overflow-hidden text-clip">Profile</p>
            </a></li>
            <li><a href="admin.php" class="flex items-center gap-8 pl-5 py-2 hover:opacity-60 duration-200 ease-linear">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="min-w-8 w-8"><path d="M680-80q-83 0-141.5-58.5T480-280q0-83 58.5-141.5T680-480q83 0 141.5 58.5T880-280q0 83-58.5 141.5T680-80Zm-200 0q-139-35-229.5-159.5T160-516v-244l320-120 320 120v227q-26-13-58.5-20t-61.5-7q-116 0-198 82t-82 198q0 62 23.5 112T483-81q-1 0-1.5.5t-1.5.5Zm200-200q25 0 42.5-17.5T740-340q0-25-17.5-42.5T680-400q-25 0-42.5 17.5T620-340q0 25 17.5 42.5T680-280Zm0 120q31 0 57-14.5t42-38.5q-22-13-47-20t-52-7q-27 0-52 7t-47 20q16 24 42 38.5t57 14.5Z"/></svg>
                <p class="text-lg overflow-hidden text-clip">Admin</p>
            </a></li>
        </menu>
    </header>
    <!-- MOBILE NAVIGATION-->
    <nav class="tablet:hidden z-50">
        <div onclick="toggleNav()" class="fixed right-0 top-50 pl-2.5 rounded-l-full bg-dirty-brown">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#eeeeee" class="p-1.5 size-8.5 cursor-pointer">
                <path fill-rule="evenodd" d="M3 6.75A.75.75 0 0 1 3.75 6h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 6.75ZM3 12a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 12Zm0 5.25a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" />
            </svg>
        </div>
        <div onclick="toggleNav()" id="overlay" class="fixed top-0 left-0 w-screen h-screen bg-black/50 hidden"></div>
        <div id="mob-nav" class="fixed top-0 right-0 pt-20 overflow-clip w-70 h-screen bg-off-white hidden flex-col animate-header font-semibold text-dirty-brown **:select-none">
            <div onclick="toggleNav()" class="absolute top-5 right-5">
                <svg class="size-9" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="#585345"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg>
            </div>
            <h1 class="px-7.5 text-2xl font-bold">Scholar Finds</h1>
            <hr class="my-5 w-full opacity-30 *:active:bg-neutral-300">
            <div class="w-full *:px-7.5 *:py-1 *:flex *:items-center *:gap-2 *:active:bg-neutral-300">
                <a href="index.html" class="block w-full"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5"> <path fill-rule="evenodd" d="M9.293 2.293a1 1 0 0 1 1.414 0l7 7A1 1 0 0 1 17 11h-1v6a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-3a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-6H3a1 1 0 0 1-.707-1.707l7-7Z" clip-rule="evenodd" /></svg>Home</a>
                <a href="about.html" class="block w-full"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5"> <path d="M10 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM6 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM1.49 15.326a.78.78 0 0 1-.358-.442 3 3 0 0 1 4.308-3.516 6.484 6.484 0 0 0-1.905 3.959c-.023.222-.014.442.025.654a4.97 4.97 0 0 1-2.07-.655ZM16.44 15.98a4.97 4.97 0 0 0 2.07-.654.78.78 0 0 0 .357-.442 3 3 0 0 0-4.308-3.517 6.484 6.484 0 0 1 1.907 3.96 2.32 2.32 0 0 1-.026.654ZM18 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM5.304 16.19a.844.844 0 0 1-.277-.71 5 5 0 0 1 9.947 0 .843.843 0 0 1-.277.71A6.975 6.975 0 0 1 10 18a6.974 6.974 0 0 1-4.696-1.81Z" /> </svg>About</a>
                <a href="contact.php" class="block w-full"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5"> <path fill-rule="evenodd" d="M2 3.5A1.5 1.5 0 0 1 3.5 2h1.148a1.5 1.5 0 0 1 1.465 1.175l.716 3.223a1.5 1.5 0 0 1-1.052 1.767l-.933.267c-.41.117-.643.555-.48.95a11.542 11.542 0 0 0 6.254 6.254c.395.163.833-.07.95-.48l.267-.933a1.5 1.5 0 0 1 1.767-1.052l3.223.716A1.5 1.5 0 0 1 18 15.352V16.5a1.5 1.5 0 0 1-1.5 1.5H15c-1.149 0-2.263-.15-3.326-.43A13.022 13.022 0 0 1 2.43 8.326 13.019 13.019 0 0 1 2 5V3.5Z" clip-rule="evenodd" /> </svg>Contact</a>
            </div>
            <hr class="my-5 w-full opacity-30 *:active:bg-neutral-300">
            <div class="w-full *:px-7.5 *:py-1 *:flex *:items-center *:gap-2 *:active:bg-neutral-300">
                <a href="library.php" class="block w-full"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5"> <path d="M10.75 16.82A7.462 7.462 0 0 1 15 15.5c.71 0 1.396.098 2.046.282A.75.75 0 0 0 18 15.06v-11a.75.75 0 0 0-.546-.721A9.006 9.006 0 0 0 15 3a8.963 8.963 0 0 0-4.25 1.065V16.82ZM9.25 4.065A8.963 8.963 0 0 0 5 3c-.85 0-1.673.118-2.454.339A.75.75 0 0 0 2 4.06v11a.75.75 0 0 0 .954.721A7.506 7.506 0 0 1 5 15.5c1.579 0 3.042.487 4.25 1.32V4.065Z" /> </svg>Library</a>
                <a href="profile.php" class="block w-full"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5"> <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-5.5-2.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0ZM10 12a5.99 5.99 0 0 0-4.793 2.39A6.483 6.483 0 0 0 10 16.5a6.483 6.483 0 0 0 4.793-2.11A5.99 5.99 0 0 0 10 12Z" clip-rule="evenodd" /> </svg>Profile</a>
            </div>
            <div id="logged-in">
                <hr class="my-5 w-full opacity-30 *:active:bg-neutral-300">
                <div class="w-full *:px-7.5 *:py-1 *:flex *:items-center *:gap-2 *:active:bg-neutral-300">
                    <a onclick="logOut()" class="block w-full text-red-700"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5"> <path fill-rule="evenodd" d="M3 4.25A2.25 2.25 0 0 1 5.25 2h5.5A2.25 2.25 0 0 1 13 4.25v2a.75.75 0 0 1-1.5 0v-2a.75.75 0 0 0-.75-.75h-5.5a.75.75 0 0 0-.75.75v11.5c0 .414.336.75.75.75h5.5a.75.75 0 0 0 .75-.75v-2a.75.75 0 0 1 1.5 0v2A2.25 2.25 0 0 1 10.75 18h-5.5A2.25 2.25 0 0 1 3 15.75V4.25Z" clip-rule="evenodd" /> <path fill-rule="evenodd" d="M6 10a.75.75 0 0 1 .75-.75h9.546l-1.048-.943a.75.75 0 1 1 1.004-1.114l2.5 2.25a.75.75 0 0 1 0 1.114l-2.5 2.25a.75.75 0 1 1-1.004-1.114l1.048-.943H6.75A.75.75 0 0 1 6 10Z" clip-rule="evenodd" /> </svg>Logout</a>
                    <a href="admin.php" class="block w-full"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="size-5"><path d="M680-80q-83 0-141.5-58.5T480-280q0-83 58.5-141.5T680-480q83 0 141.5 58.5T880-280q0 83-58.5 141.5T680-80Zm-200 0q-139-35-229.5-159.5T160-516v-244l320-120 320 120v227q-26-13-58.5-20t-61.5-7q-116 0-198 82t-82 198q0 62 23.5 112T483-81q-1 0-1.5.5t-1.5.5Zm200-200q25 0 42.5-17.5T740-340q0-25-17.5-42.5T680-400q-25 0-42.5 17.5T620-340q0 25 17.5 42.5T680-280Zm0 120q31 0 57-14.5t42-38.5q-22-13-47-20t-52-7q-27 0-52 7t-47 20q16 24 42 38.5t57 14.5Z"/></svg>Admin</a>
                </div>
            </div>
        </div>
        <!-- SCRIPT -->
        <script>
            const mobNav = document.getElementById("mob-nav");
            const overlay = document.getElementById("overlay");
    
            function toggleNav() {
                mobNav.classList.toggle("hidden");
                mobNav.classList.toggle("flex");
                overlay.classList.toggle("hidden");
            }

            function logOut() {
                ["id", "personalization", "role", "adminMenu", "filters", "selectedTab", "spotify"].forEach(c => document.cookie = `${c}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`);
                window.location.href = "index.html";
            }

            const admin = document.querySelector('a[href="admin.php"]');
            const userId = document.cookie.match(/id=([^;]+)/)?.[1];
            const role = document.cookie.match(/role=([^;]+)/)?.[1];
            if (!role || role === "regular") {
                admin?.classList.add("hidden");
                document.getElementById('logged-in').classList.add("hidden");
            }
            const profileNav = document.getElementById("profile-nav");
            const code = document.cookie.match(/personalization=([^;]+)/)?.[1].slice(0, 2);

            profileNav.innerHTML = code
                ? `<img src="resources/dp/${code}.svg" alt="" class="min-w-8 w-8 rounded-full border border-off-white">`
                : `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="min-w-8 w-8">
                    <path fill-rule="evenodd" d="M18.685 19.097A9.723 9.723 0 0 0 21.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 0 0 3.065 7.097A9.716 9.716 0 0 0 12 21.75a9.716 9.716 0 0 0 6.685-2.653Zm-12.54-1.285A7.486 7.486 0 0 1 12 15a7.486 7.486 0 0 1 5.855 2.812A8.224 8.224 0 0 1 12 20.25a8.224 8.224 0 0 1-5.855-2.438ZM15.75 9a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" clip-rule="evenodd"/>
                </svg>`;
        </script> 
    </nav>
    <!-- ================================================== MAIN ================================================== -->
    <main class="ml-25 m-5 p-15 w-[calc(100vw-135px)] min-h-[calc(100vh-40px)] h-auto rounded-4xl flex flex-col bg-off-white text-dirty-brown drag-none z-1
        max-tablet:m-0 max-tablet:p-7.5 max-phone:p-6 max-tablet:w-full max-tablet:min-h-screen max-tablet:rounded-none">
        <?php
        $success = $_SESSION['success'] ?? '';
        $error = $_SESSION['error'] ?? '';

        if ($success) {
            echo "<div class='absolute top-10 left-1/2 -translate-x-1/2 p-2 px-5 w-100 rounded-xl border-2 bg-[#d9ead3] border-[#b6d7a8] text-[#274e13] select-none leading-none z-5 animate-downfadeinout delay-200'>" . $success . "</div>";
        } else if ($error) {
            echo "<div class='absolute top-10 left-1/2 -translate-x-1/2 p-2 px-5 w-100 rounded-xl border-2 bg-[#e6b8af] border-[#dd7e6b] text-[#5b0f00] select-none leading-none z-5 animate-downfadeinout delay-200'>" . $error . "</div>";
        }

        session_unset();
        ?>
        <section class="my-10 font-bold text-center select-none">
            <h1 class="text-lg max-tablet:text-sm">CONTACT US</h1>
            <h2 class="text-4xl max-tablet:text-2xl">Connect with Our Team</h2>
        </section>
        <section class="relative h-150 max-big-phone:h-180 flex flex-col gap-5">
            <div class="absolute z-1 bottom-0 -left-15 w-[calc(100%+120px)] max-tablet:w-[calc(100%+80px)] h-132.5 max-tablet:h-170 max-big-phone:h-165 bg-neutral-50 drop-shadow-lg"></div>
            <div class="relative z-2 h-35 max-tablet:h-30 flex items-center justify-center gap-10 max-tablet:gap-7.5 *:p-4 *:w-100 *:bg-neutral-50 *:flex *:gap-4 *:size-full *:rounded-lg *:drop-shadow-md **:leading-none font-bold">
                <div id="college-contact" class="max-big-phone:!hidden animate-fadeIn">
                    <img src="resources/ccis.svg" alt="" class="h-full drop-shadow-lg select-none">
                    <div class="flex-1 flex flex-col justify-center gap-4">
                        <h2 class="text-lg max-tablet:text-base">College of Computing and Information Sciences</h2>
                        <span class="text-sm max-tablet:text-xs">
                            <h3>Email Address:</h3>
                            <p class="font-light">ccis@umak.edu.ph</p>
                        </span>
                    </div>
                </div>
                <div id="dev-contact" class="animate-fadeIn">
                    <img src="resources/sf-logo.svg" alt="" class="h-full drop-shadow-lg select-none">
                    <div class="flex-1 flex flex-col justify-center gap-4">
                        <span>
                            <h2 class="text-lg max-tablet:text-base">Mr. Renzjan Moncinilla</h2>
                            <i class="font-light text-sm max-tablet:text-xs">Scholar Finds' Lead Developer</i>
                        </span>
                        <span class="text-sm max-tablet:text-xs">
                            <h3>Email Address:</h3>
                            <p class="font-light break-all">renzjan.moncinilla@umak.edu.ph</p>
                        </span>
                    </div>
                </div>
            </div>
            <script>
                const collegeContact = document.getElementById('college-contact');
                const devContact = document.getElementById('dev-contact');
                setInterval(() => {
                    collegeContact.classList.toggle('max-big-phone:!hidden');
                    devContact.classList.toggle('max-big-phone:!hidden');
                }, 5000);
            </script>
            <div class="relative z-2 px-15 max-tablet:px-5 max-big-phone:p-0 py-5 flex-1 flex max-big-phone:flex-col gap-5">
                <div class="py-10 max-big-phone:py-5 w-100 max-tablet:w-80 max-big-phone:w-full h-full max-big-phone:h-max flex flex-col gap-2.5">
                    <h2 class="font-bold text-3xl max-tablet:text-xl">Get in Touch with Us</h2>
                    <p class="max-tablet:text-sm">Have questions, feedback, or need assistance? We'd love to hear from you! Fill out the form, and our team will get back to you as soon as possible. Whether it’s a quick inquiry or detailed support, we’re here to help.</p>
                </div>
                <div class="big-phone:flex-1 h-full max-big-phone:h-max">
                    <form method="post" id="contact-form" class="p-2.5 size-full max-big-phone:h-max flex flex-col gap-2.5 font-semibold">
                        <span class="flex max-big-phone:flex-col gap-2.5">
                            <input type="email" name="cemail" id="cemail" class="px-3 py-2 w-full bg-stone-200/50 border border-stone-300 rounded-md outline-none placeholder:text-dirty-brown shadow-sm flex-2 max-tablet:text-sm focus:border-dgreen" placeholder="Email Address" required>
                            <select name="concern" id="concern" class="px-3 py-2 w-full bg-stone-200/50 border border-stone-300 rounded-md outline-none placeholder:text-dirty-brown shadow-sm flex-1 max-tablet:text-sm focus:border-dgreen" required>
                                <option value="none" selected disabled>Your concern</option>
                                <option value="removal">Request for Thesis Removal</option>
                                <option value="assistance">Technical Asssitance</option>
                                <option value="suggestions">Suggestions</option>
                            </select>
                        </span>
                        <span>
                            <input type="text" name="subject" id="subject" class="px-3 py-2 w-full bg-stone-200/50 border border-stone-300 rounded-md outline-none placeholder:text-dirty-brown shadow-sm max-tablet:text-sm focus:border-dgreen" placeholder="Subject" required>
                        </span>
                        <span>
                            <textarea name="message" id="message" class="px-3 py-2 w-full h-40 bg-stone-200/50 border border-stone-300 rounded-md outline-none placeholder:text-dirty-brown shadow-sm resize-none max-tablet:text-sm focus:border-dgreen" placeholder="Tell us about your concern..." required></textarea>
                        </span>
                        <button data-sitekey="6Ld5yh8rAAAAAP8xFH5zqZW27LnLRwmvBL4zD4WL" data-callback="onSubmit" class="g-recaptcha px-5 py-1 rounded-md bg-dgreen text-off-white drop-shadow-md select-none cursor-pointer hover:opacity-85 active:brightness-125 duration-100">Send</button>
                    </form>
                </div>
            </div>
        </section>
        <script>
            function onSubmit(token) {
                document.getElementById("contact-form").submit();
            }
        </script>
    </main>
    <script>
        window.history.replaceState(null, null, window.location.pathname);
    </script>
</body>
</html>