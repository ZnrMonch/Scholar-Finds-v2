<?php
    session_start();
    if (!isset($_COOKIE['id'])) {
        $_SESSION['log-error'] = "We can't take you there yet! Please log in first.";
        header("Location: access.php");
        exit();
    }

    require_once('config.php');
    $stmt = $conn->prepare("SELECT * FROM accounts WHERE user_id = ?");
    $stmt->bind_param("s", $_COOKIE['id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        $userId = $user['user_id'];
        $dateJoined = $user['date_joined'];
        $verified = $user['verified'];
        $membership = $user['membership'];
        $username = $user['username'];
        $name = $user['name'];
        $email = $user['email'];
        $college = $user['college'];
        $yearsection = $user['yearsection'] ?? '';
        if (!empty($yearsection)) {
            $yearsection = explode("-", $yearsection);
        }
        $year = $yearsection[0] ?? '';
        $section = $yearsection[1] ?? '';
        $bio = $user['bio'];
        $personalization = $user['personalization'];
        $dp = substr($personalization, 0, 2);
        $bg = substr($personalization, 3);
        $preferences = explode(',', $user['preferences']);
        setcookie("personalization", $personalization, time() + (86400 * 30), "/");
    }   

    $stmt->close();
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
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <link href="./output.css" rel="stylesheet">
</head>
<body class="bg-[url('resources/lib-bg.jpg')] font-nunito text-white flex">
    <div class="fixed inset-0 bg-black/50 h-full z-0 max-tablet:hidden"></div>
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
    <?php
        $success = $_SESSION['success'] ?? '';

        if ($success) {
            echo "<div class='absolute top-10 left-1/2 -translate-x-1/2 p-2 px-5 w-100 rounded-xl border-2 bg-[#d9ead3] border-[#b6d7a8] text-[#274e13] select-none leading-none z-5 animate-downfadeinout delay-200'>" . $success . "</div>";
        }

        session_unset();
    ?>
    <main class="relative ml-25 m-5 p-15 w-[calc(100vw-135px)] min-h-[calc(100vh-40px)] h-auto rounded-4xl flex bg-[#eeeeee] z-2 text-dirty-brown drag-none
    max-tablet:m-0 max-tablet:p-5 max-tablet:min-h-screen max-tablet:size-full max-tablet:rounded-none">
        <div class="absolute w-75 flex flex-col gap-2.5 *:p-4 *:rounded-xl *:bg-white *:border *:border-neutral-200 *:shadow-md *:flex *:gap-2.5">
            <div class="items-center">
                <a href="#"><img src="resources/dp/<?php echo substr($personalization, 0, 2);?>.svg" alt="" class="size-10 rounded-full border-2 border-dirty-brown shadow-md select-none"></a>
                <span class="font-semibold leading-none">
                    <a class="cursor-pointer active:cursor-text flex items-center font-bold" href="#"><?php echo $name?></a>
                </span>
            </div>
            <div class="px-0! gap-0! flex-col font-bold *:px-5 *:py-3 *:flex *:items-center *:gap-2.5 *:hover:bg-off-white select-none *:cursor-pointer *:duration-200">
                <a href="#preferences">
                    <svg class="size-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor"><path d="m80-520 200-360 200 360H80Zm200 400q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47Zm240 0v-320h320v320H520Zm160-400q-57-48-95.5-81T523-659q-23-25-33-47t-10-47q0-45 31.5-76t78.5-31q27 0 50.5 12.5T680-813q16-22 39.5-34.5T770-860q47 0 78.5 31t31.5 76q0 25-10 47t-33 47q-23 25-61.5 58T680-520Z"/></svg>
                    <span>Preferences</span>
                </a>
                <a href="#security">
                    <svg class="size-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor"><path d="M240-80q-33 0-56.5-23.5T160-160v-400q0-33 23.5-56.5T240-640h40v-80q0-83 58.5-141.5T480-920q83 0 141.5 58.5T680-720v80h40q33 0 56.5 23.5T800-560v52q-18-6-37.5-9t-42.5-3q-116 0-198 82t-82 198q0 45 13 84t37 76H240Zm480 40q-83 0-141.5-58.5T520-240q0-83 58.5-141.5T720-440q83 0 141.5 58.5T920-240q0 83-58.5 141.5T720-40ZM360-640h240v-80q0-50-35-85t-85-35q-50 0-85 35t-35 85v80Zm360 400q25 0 42.5-17.5T780-300q0-25-17.5-42.5T720-360q-25 0-42.5 17.5T660-300q0 25 17.5 42.5T720-240Zm0 120q30 0 56-14t43-39q-23-14-48-20.5t-51-6.5q-26 0-51 6.5T621-173q17 25 43 39t56 14Z"/></svg>
                    <span>Security</span>
                </a>
                <a href="#account-control">
                    <svg class="size-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor"><path d="m640-120-12-60q-12-5-22.5-10.5T584-204l-58 18-40-68 46-40q-2-14-2-26t2-26l-46-40 40-68 58 18q11-8 21.5-13.5T628-460l12-60h80l12 60q12 5 22.5 11t21.5 15l58-20 40 70-46 40q2 12 2 25t-2 25l46 40-40 68-58-18q-11 8-21.5 13.5T732-180l-12 60h-80ZM80-160v-112q0-33 17-62t47-44q51-26 115-44t141-18h14q6 0 12 2-29 72-24 143t48 135H80Zm600-80q33 0 56.5-23.5T760-320q0-33-23.5-56.5T680-400q-33 0-56.5 23.5T600-320q0 33 23.5 56.5T680-240ZM400-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47Z"/></svg>
                    <span>Account Control</span>
                </a>
                <a onclick="logOut()" class="text-red-700">
                    <svg class="size-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z"/></svg>
                    <span>Log out</span>
                </a>
            </div>
        </div>
        <div class="w-85"></div>

        <!-- ACCOUNT INFORMATION -->
        <div id="account-information" class="flex-1 relative flex flex-col">
            <button onclick="toggleEditIntro()" class="absolute top-5 right-5 z-1 hover:brightness-125 cursor-pointer"><svg class="size-5 max-big-phone:size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><!-- Icon from Material Symbols by Google - https://github.com/google/material-design-icons/blob/master/LICENSE --><path fill="currentColor" d="M10 15q-.425 0-.712-.288T9 14v-2.425q0-.4.15-.763t.425-.637l8.6-8.6q.3-.3.675-.45t.75-.15q.4 0 .763.15t.662.45L22.425 3q.275.3.425.663T23 4.4t-.137.738t-.438.662l-8.6 8.6q-.275.275-.637.438t-.763.162zm9.6-9.2l1.425-1.4l-1.4-1.4L18.2 4.4zM5 21q-.825 0-1.412-.587T3 19V5q0-.825.588-1.412T5 3h6.5q.35 0 .575.175t.35.45t.087.55t-.287.525l-4.65 4.65q-.275.275-.425.638T7 10.75V15q0 .825.588 1.412T9 17h4.225q.4 0 .763-.15t.637-.425L19.3 11.75q.25-.25.525-.288t.55.088t.45.35t.175.575V19q0 .825-.587 1.413T19 21z"/></svg></button>
            <div class="<?php echo substr($personalization, 3);?> w-full h-70 rounded-xl shadow-md"></div>
            <div class="absolute top-45 left-7.5">
                <div class="relative size-50 rounded-full border-7 border-off-white overflow-clip">
                    <img src="resources/dp/<?php echo substr($personalization, 0, 2);?>.svg" alt="" class="rounded-full absolute z-1 size-50 select-none peer">
                    <div class="absolute z-2 size-full bg-black/60 hidden peer-hover:flex hover:flex items-center justify-center gap-1 cursor-pointer text-off-white">
                        <svg class="size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor"><path d="M120-120v-170l528-527q12-11 26.5-17t30.5-6q16 0 31 6t26 18l55 56q12 11 17.5 26t5.5 30q0 16-5.5 30.5T817-647L290-120H120Zm584-528 56-56-56-56-56 56 56 56Z"/></svg>
                        <p class="font-bold select-none">Edit Profile Picture</p>
                    </div>
                </div>
            </div>
            <div class="m-2.5 ml-62.5 h-20 flex flex-col justify-center font-bold leading-none">
                <h1 class="text-2xl"><?php echo $name?> <span class="text-neutral-400 text-base italic">@<?php echo strtolower($username);?></span></h1>
                <i class="text-neutral-400">Joined in <?php echo date("F d, Y", strtotime($dateJoined)); ?></i>
            </div>
            <div class="flex-1 pt-5 px-10 text-justify flex flex-col gap-5">
                <span>
                    <h2 class="font-bold text-xl">About</h2>
                    <p class="text-neutral-400">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec a diam lectus. Sed sit amet ipsum mauris. Maecenas congue ligula ac quam viverra nec consectetur ante hendrerit. Donec et mollis dolor. Praesent et diam eget libero egestas mattis sit amet vitae augue.</p>
                </span>
                <hr class="border opacity-25">
                <div class="flex-1 flex flex-col gap-2.5">
                    <h2 class="font-bold text-xl">More Information</h2>
                    <div class="flex gap-5 *:flex-1 **:leading-4 font-semibold">
                        <div class="<?php echo empty(trim($college)) ? 'hidden' : '';?> flex flex-col gap-5">
                            <span>
                                <h3 class="text-neutral-400 text-xs">COLLEGE</h3>
                                <p class="font-vb"><?php echo $college == 'hsu' ? 'Higher School ng UMak' : ($college == 'ccis' ? 'College of Computing and Information Sciences' : '');?></p>
                            </span>
                            <span>
                                <h3 class="text-neutral-400 text-xs">YEAR AND SECTION</h3>
                                <p><?php echo $year . '-' . $section?></p>
                            </span>
                        </div>
                        <div class="flex flex-col gap-5">
                            <span>
                                <h3 class="text-neutral-400 text-xs">MEMBERSHIP</h3>
                                <p><?php echo ucfirst($membership);?></p>
                            </span>
                            <span>
                                <h3 class="text-neutral-400 text-xs">EMAIL ADDRESS</h3>
                                <p><?php echo $email;?></p>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="**:leading-none">
                <div id="overlay-edit-intro" onclick="toggleEditIntro()" class="fixed z-2 top-0 left-0 size-full bg-black/40 hidden"></div>
                <div id="edit-intro" class="fixed z-3 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 p-10 w-120 rounded-xl hidden flex-col justify-evenly gap-5 drop-shadow-lg bg-off-white text-dirty-brown **:outline-none">
                    <div class="relative">
                        <button onclick="toggleEditIntro()" type="button" class="absolute -top-2.5 -right-2.5 cursor-pointer hover:opacity-80 active:scale-95 duration-200"><svg class="size-7" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg></button>
                        <h2 class="py-5 font-bold text-2xl text-center">Edit Introduction</h2>
                    </div>    
                    <form action="update.php" method="post" class="flex flex-col gap-5 *:relative">
                        <span>
                            <input type="text" name="name" id="name" required class="peer px-2 py-1.5 w-full rounded-md border-2 border-dirty-brown text-sm">
                            <label for="name" class="absolute top-2.5 left-2 px-1 bg-slgreen leading-none select-none opacity-70 peer-valid:-translate-y-4 peer-valid:text-xs peer-valid:opacity-100 peer-focus:-translate-y-4 peer-focus:text-xs peer-focus:opacity-100 duration-200">Name</label>
                        </span>
                        <span class="flex *:flex-1 *:flex *:items-center *:justify-center *:gap-1">
                            <span>
                                <input type="radio" name="college" value="ccis" id="CCIS" class="peer" checked>
                                <label for="CCIS" class="font-semibold select-none opacity-50 peer-checked:opacity-100">CCIS</label>
                            </span>
                            <span>
                                <input type="radio" name="college" value="hsu" id="HSU" class="peer">
                                <label for="HSU" class="font-semibold select-none opacity-50 peer-checked:opacity-100">HSU</label>
                            </span>
                        </span>
                        <script>
                            const toggleInputs = (id) => {
                                document.getElementById(id).classList.toggle('flex');
                                document.getElementById(id).classList.toggle('hidden');
                            };
                        </script>
                        <!-- COLLEGE STUDENT -->
                        <div id="ccis-input" class="flex gap-2.5 *:relative">
                            <span class="flex-1">
                                <label for="year" class="absolute top-2.5 left-2 px-1 bg-slgreen leading-none select-none opacity-100 -translate-y-4 text-xs">Year</label>
                                <select name="year" id="year" class="px-2 py-1.5 w-full rounded-md border-2 border-dirty-brown text-sm">
                                    <option value="I">I</option>
                                    <option value="II">II</option>
                                    <option value="III">III</option>
                                    <option value="IV">IV</option>
                                </select>  
                            </span>
                            <span class="flex-3">
                                <input type="text" name="section" id="section" required class="peer px-2 py-1.5 w-full rounded-md border-2 border-dirty-brown text-sm">
                                <label for="section" class="absolute top-2.5 left-2 px-1 bg-slgreen leading-none select-none opacity-70 peer-valid:-translate-y-4 peer-valid:text-xs peer-valid:opacity-100 peer-focus:-translate-y-4 peer-focus:text-xs peer-focus:opacity-100 duration-200">Section</label>
                            </span>
                        </div>
                        <!-- SENIOR HIGH STUDENT -->
                        <div id="hsu-input" class="hidden gap-2.5 *:relative">
                            <span class="flex-1">
                                <label for="shs-grade" class="absolute top-2.5 left-2 px-1 bg-slgreen leading-none select-none opacity-100 -translate-y-4 text-xs">Year</label>
                                <select name="shs-grade" id="shs-grade" class="px-2 py-1.5 w-full rounded-md border-2 border-dirty-brown text-sm">
                                    <option value="G11">Grade 11</option>
                                    <option value="G12">Grade 12</option>
                                </select>  
                            </span>
                            <span class="flex-3">
                                <input type="text" name="shs-section" id="shs-section" required class="peer px-2 py-1.5 w-full rounded-md border-2 border-dirty-brown text-sm">
                                <label for="shs-section" class="absolute top-2.5 left-2 px-1 bg-slgreen leading-none select-none opacity-70 peer-valid:-translate-y-4 peer-valid:text-xs peer-valid:opacity-100 peer-focus:-translate-y-4 peer-focus:text-xs peer-focus:opacity-100 duration-200">Section</label>
                            </span>
                        </div>
                        <span>
                            <textarea type="text" name="bio" id="bio" maxlength="200" required class="peer px-2 py-1.5 w-full h-20 rounded-md border-2 border-dirty-brown text-sm resize-none"></textarea>
                            <label for="bio" class="absolute top-2.5 left-2 px-1 bg-slgreen leading-none select-none opacity-70 peer-valid:-translate-y-4 peer-valid:text-xs peer-valid:opacity-100 peer-focus:-translate-y-4 peer-focus:text-xs peer-focus:opacity-100 duration-200">Bio</label>
                            <i class="text-sm select-none">*maximum of 200 characters</i>
                        </span>
                        <div class="flex justify-center">
                            <input type="text" name="id" id="id" value="<?php echo $_COOKIE['id'];?>" hidden>
                            <button type="submit" name="update" class="px-5 py-1 w-30 rounded-md bg-lgreen font-bold hover:opacity-80 duration-200 cursor-pointer">Save</button>
                        </div>
                    </form>
                </div>
                <script>
                    const toggleCollege = () => {
                        const ccis = document.getElementById('CCIS').checked;

                        const ccisInput = document.getElementById('ccis-input');
                        const hsuInput = document.getElementById('hsu-input');

                        // Toggle CCIS section
                        ccisInput.classList.remove('flex', 'hidden');
                        ccisInput.classList.add(ccis ? 'flex' : 'hidden');
                        ccisInput.querySelectorAll('input, select').forEach(el => {
                            el.disabled = !ccis;
                            if (el.name === 'section') {
                                el.required = ccis;
                            }
                        });

                        // Toggle HSU section
                        hsuInput.classList.remove('flex', 'hidden');
                        hsuInput.classList.add(!ccis ? 'flex' : 'hidden');
                        hsuInput.querySelectorAll('input, select').forEach(el => {
                            el.disabled = ccis;
                            if (el.name === 'shs-section') {
                                el.required = !ccis;
                            }
                        });
                    };


                    const toggleEditIntro = () => {
                        const overlay = document.getElementById('overlay-edit-intro');
                        const panel = document.getElementById('edit-intro');
                        overlay.classList.toggle('hidden');
                        panel.classList.toggle('hidden');
                        panel.classList.toggle('flex');

                        document.getElementById('name').value = "<?php echo $name; ?>";
                        document.getElementById('bio').value = "<?php echo $bio; ?>";

                        const college = "<?php echo $college ?>";
                        document.getElementById(college).checked = true;

                        if (college === 'CCIS') {
                            document.getElementById('year').value = "<?php echo $year; ?>";
                            document.getElementById('section').value = "<?php echo $section; ?>";
                        } else {
                            document.getElementById('shs-grade').value = "<?php echo $year; ?>";
                            document.getElementById('shs-section').value = "<?php echo $section; ?>";
                        }

                        toggleCollege();
                    };

                    document.addEventListener('DOMContentLoaded', () => {
                        ['CCIS', 'HSU'].forEach(id =>
                            document.getElementById(id).addEventListener('change', toggleCollege)
                        );
                    });
                </script>
            </div>
        </div>

        <!-- PREFERENCES -->
        <div id="preferences" class="hidden w-200">
            <div class="size-full flex flex-col gap-5">
                <h2 class="text-3xl font-bold select-none">Preferences</h2>
                <div>
                    <h3 class="text-xl font-bold select-none flex items-center gap-2">
                        <svg class="size-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor"><path d="M120-574v-85l181-181h85L120-574Zm0-196v-70h70l-70 70Zm527 67q-10-11-21.5-21.5T602-743l97-97h85L647-703ZM220-361l77-77q7 11 14.5 20t16.5 17q-28 7-56.5 17.5T220-361Zm480-197v-2q0-19-3-37t-9-35l152-152v86L700-558ZM436-776l65-64h85l-64 64q-11-2-21-3t-21-1q-11 0-22 1t-22 3ZM120-375v-85l144-144q-2 11-3 22t-1 22q0 11 1 21t3 20L120-375Zm709 83q-8-12-18.5-23T788-335l52-52v85l-11 10Zm-116-82q-7-3-14-5.5t-14-4.5q-9-3-17.5-6t-17.5-5l190-191v86L713-374Zm-233-26q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-120v-71q0-34 17-63t47-44q51-26 115.5-44T480-360q76 0 140.5 18T736-298q30 15 47 44t17 63v71H160Z"/></svg>
                        <span>Customize Profile</span>
                    </h3>
                    <div class="w-full flex justify-between gap-15 *:flex-1">
                        <form action="customize.php" method="POST">
                            <h4 class="font-semibold">Profile Picture</h4>
                            <div class="w-full flex items-center flex-wrap gap-2.5 select-none">
                                <label for="dp01">
                                    <input type="radio" name="dp" id="dp01" value="dp01" hidden class="peer" <?php echo substr($personalization, 0, 2) == "01" ? 'checked' : '';?>>
                                    <img src="resources/dp/01.svg" alt="" class="size-15 rounded-full border-2 border-transparent peer-checked:border-dirty-brown cursor-pointer shadow-lg">
                                </label>
                                <label for="dp02">
                                    <input type="radio" name="dp" id="dp02" value="dp02" hidden class="peer" <?php echo substr($personalization, 0, 2) == "02" ? 'checked' : '';?>>
                                    <img src="resources/dp/02.svg" alt="" class="size-15 rounded-full border-2 border-transparent peer-checked:border-dirty-brown cursor-pointer shadow-lg">
                                </label>
                                <label for="dp03">
                                    <input type="radio" name="dp" id="dp03" value="dp03" hidden class="peer" <?php echo substr($personalization, 0, 2) == "03" ? 'checked' : '';?>>
                                    <img src="resources/dp/03.svg" alt="" class="size-15 rounded-full border-2 border-transparent peer-checked:border-dirty-brown cursor-pointer shadow-lg">
                                </label>
                                <label for="dp04">
                                    <input type="radio" name="dp" id="dp04" value="dp04" hidden class="peer" <?php echo substr($personalization, 0, 2) == "04" ? 'checked' : '';?>>
                                    <img src="resources/dp/04.svg" alt="" class="size-15 rounded-full border-2 border-transparent peer-checked:border-dirty-brown cursor-pointer shadow-lg">
                                </label>
                                <label for="dp05">
                                    <input type="radio" name="dp" id="dp05" value="dp05" hidden class="peer" <?php echo substr($personalization, 0, 2) == "05" ? 'checked' : '';?>>
                                    <img src="resources/dp/05.svg" alt="" class="size-15 rounded-full border-2 border-transparent peer-checked:border-dirty-brown cursor-pointer shadow-lg">
                                </label>
                                <label for="dp06">
                                    <input type="radio" name="dp" id="dp06" value="dp06" hidden class="peer" <?php echo substr($personalization, 0, 2) == "06" ? 'checked' : '';?>>
                                    <img src="resources/dp/06.svg" alt="" class="size-15 rounded-full border-2 border-transparent peer-checked:border-dirty-brown cursor-pointer shadow-lg">
                                </label>
                                <label for="dp07">
                                    <input type="radio" name="dp" id="dp07" value="dp07" hidden class="peer" <?php echo substr($personalization, 0, 2) == "07" ? 'checked' : '';?>>
                                    <img src="resources/dp/07.svg" alt="" class="size-15 rounded-full border-2 border-transparent peer-checked:border-dirty-brown cursor-pointer shadow-lg">
                                </label>
                                <label for="dp08">
                                    <input type="radio" name="dp" id="dp08" value="dp08" hidden class="peer" <?php echo substr($personalization, 0, 2) == "08" ? 'checked' : '';?>>
                                    <img src="resources/dp/08.svg" alt="" class="size-15 rounded-full border-2 border-transparent peer-checked:border-dirty-brown cursor-pointer shadow-lg">
                                </label>
                                <label for="dp09">
                                    <input type="radio" name="dp" id="dp09" value="dp09" hidden class="peer" <?php echo substr($personalization, 0, 2) == "09" ? 'checked' : '';?>>
                                    <img src="resources/dp/09.svg" alt="" class="size-15 rounded-full border-2 border-transparent peer-checked:border-dirty-brown cursor-pointer shadow-lg">
                                </label>
                                <label for="dp10">
                                    <input type="radio" name="dp" id="dp10" value="dp10" hidden class="peer" <?php echo substr($personalization, 0, 2) == "10" ? 'checked' : '';?>>
                                    <img src="resources/dp/10.svg" alt="" class="size-15 rounded-full border-2 border-transparent peer-checked:border-dirty-brown cursor-pointer shadow-lg">
                                </label>
                            </div>
                            <br class="select-none">
                            <h4 class="font-semibold">Background Gradient</h4>
                            <div class="w-full flex items-center flex-wrap gap-2.5 select-none">
                                <label for="snowy-mint">
                                    <input type="radio" name="bg" id="snowy-mint" value="snowy-mint" hidden class="peer" <?php echo substr($personalization, 3) == "bg-snowy-mint" ? 'checked' : '';?>>
                                    <div class="h-15 w-20 rounded-lg bg-snowy-mint shadow-lg border-2 border-white peer-checked:border-dirty-brown cursor-pointer"></div>
                                </label>
                                <label for="pink-panther">
                                    <input type="radio" name="bg" id="pink-panther" value="pink-panther" hidden class="peer" <?php echo substr($personalization, 3) == "bg-pink-panther" ? 'checked' : '';?>>
                                    <div class="h-15 w-20 rounded-lg bg-pink-panther shadow-lg border-2 border-white peer-checked:border-dirty-brown cursor-pointer"></div>
                                </label>
                                <label for="dusty-grass">
                                    <input type="radio" name="bg" id="dusty-grass" value="dusty-grass" hidden class="peer" <?php echo substr($personalization, 3) == "bg-dusty-grass" ? 'checked' : '';?>>
                                    <div class="h-15 w-20 rounded-lg bg-dusty-grass shadow-lg border-2 border-white peer-checked:border-dirty-brown cursor-pointer"></div>
                                </label>
                                <label for="golden-blush">
                                    <input type="radio" name="bg" id="golden-blush" value="golden-blush" hidden class="peer" <?php echo substr($personalization, 3) == "bg-golden-blush" ? 'checked' : '';?>>
                                    <div class="h-15 w-20 rounded-lg bg-golden-blush shadow-lg border-2 border-white peer-checked:border-dirty-brown cursor-pointer"></div>
                                </label>
                            </div>
                            <button name="save-cp" class="mt-5 px-5 py-0.5 rounded-md text-sm bg-dirty-brown disabled:bg-neutral-400 text-off-white disabled:cursor-not-allowed enabled:cursor-pointer enabled:hover:opacity-85 enabled:active:scale-95">Save</button>
                        </form>
                        <div class="relative">
                            <div id="bg-preview" class="h-35 rounded-lg shadow-lg <?php echo substr($personalization, 3)?>"></div>
                            <img id="dp-preview" src="resources/dp/<?php echo substr($personalization, 0, 2)?>.svg" alt="" class="absolute size-20 rounded-full border-4 border-off-white top-25 left-5">
                            <div class="ml-30 py-2.5">
                                <b>User Preview</b>
                            </div>
                        </div>
                    </div>
                    <script>
                        document.querySelectorAll('input[name="bg"]').forEach(input => {
                            let bg = "bg-" + input.value;
                            input.addEventListener('change', function() {
                                document.getElementById('bg-preview').className = `w-90 h-35 rounded-lg shadow-lg ${bg}`;
                            });
                        });
                        document.querySelectorAll('input[name="dp"]').forEach(input => {
                            let dp = input.value.slice(2, 4);
                            input.addEventListener('change', function() {
                                document.getElementById('dp-preview').src = `resources/dp/${dp}.svg`;
                            });
                        });
                    </script>
                </div>
                <br class="select-none">
                <form action="customize.php" method="post" class="flex gap-15 *:flex-1">
                    <div class="flex flex-col gap-2.5">
                        <h3 class="text-xl font-bold select-none flex items-center gap-2">
                            <svg class="size-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor"><path d="M160-200v-80h80v-280q0-83 50-147.5T420-792v-28q0-25 17.5-42.5T480-880q25 0 42.5 17.5T540-820v28q80 20 130 84.5T720-560v280h80v80H160ZM480-80q-33 0-56.5-23.5T400-160h160q0 33-23.5 56.5T480-80Z"/></svg>
                            <span>Notifications</span>
                        </h3>
                        <div class="flex flex-col gap-2.5 items-start">
                            <div class="grid grid-cols-[auto_1fr] gap-2">
                                <input type="checkbox" name="new-theses" id="new-theses" value="notify-new-thesis" <?php echo !empty($preferences) ? (in_array('notify-new-thesis', $preferences, true) ? 'checked' : '') : ''?>>
                                <span><label for="new-theses" class="cursor-pointer">Notify me whenever a new thesis is added.</label></span>
                                <input type="checkbox" name="updates" id="updates" value="notify-updates" <?php echo !empty($preferences) ? (in_array('notify-updates', $preferences, true) ? 'checked' : '') : ''?>>
                                <span><label for="updates" class="cursor-pointer">Notify me about any new updates or features on the website.</label></span>
                                <input type="checkbox" name="usage" id="usage" value="notify-usage" <?php echo !empty($preferences) ? (in_array('notify-usage', $preferences, true) ? 'checked' : '') : ''?>>
                                <span><label for="usage" class="cursor-pointer">Send me weekly or monthly usage reports.</label></span>
                            </div>
                            <button id="notify-save" name="notify-save" class="px-5 py-0.5 rounded-md text-sm bg-dirty-brown text-off-white cursor-pointer hover:opacity-85 active:scale-95 duration-100">Save</button>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2.5">
                        <h3 class="text-xl font-bold select-none flex items-center gap-2">
                            <svg class="size-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor"><path d="M240-280h280v-80H240v80Zm400 0h80v-400h-80v400ZM240-440h280v-80H240v80Zm0-160h280v-80H240v80Zm-80 480q-33 0-56.5-23.5T80-200v-560q0-33 23.5-56.5T160-840h640q33 0 56.5 23.5T880-760v560q0 33-23.5 56.5T800-120H160Z"/></svg>
                            <span>Interface Preferences</span>
                        </h3>
                        <div class="flex flex-col gap-2.5 items-start">
                            <div class="grid grid-cols-[auto_1fr] items-center gap-2.5">
                                <select name="default-layout" id="default-layout" class="px-5 bg-neutral-200 border border-neutral-400 rounded-md select-none">
                                    <option value="grid" <?php echo !empty($preferences) ? (in_array('grid-layout', $preferences, true) ? 'selected' : '') : ''?>>Grid</option>
                                    <option value="list" <?php echo !empty($preferences) ? (in_array('list-layout', $preferences, true) ? 'selected' : '') : ''?>>List</option>
                                </select>
                                <span><label for="" class="cursor-pointer">Set default layout style.</label></span>
                                <div class="col-span-2">
                                    <input type="checkbox" name="music" id="music" value="disabled-music" class="m-1.5 ml-0" <?php echo !empty($preferences) ? (in_array('disabled-music', $preferences, true) ? 'checked' : '') : ''?>>
                                    <span><label for="music" class="cursor-pointer">Disable background music.</label></span>
                                </div>
                            </div>
                            <button id="interface-save" name="interface-save" class="px-5 py-0.5 rounded-md text-sm bg-dirty-brown text-off-white cursor-pointer hover:opacity-85 active:scale-95 duration-100">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- SECURITY -->
        <div id="security" class="flex-1 hidden">
            <div class="size-full flex flex-col">
                <h2 class="mb-5 text-3xl font-bold select-none">Security</h2>
                <!-- VERIFICATION -->
                <div>
                    <h3 class="font-bold text-xl flex items-center gap-2.5">
                        <svg class="size-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor"><path d="M702-480 560-622l57-56 85 85 170-170 56 57-226 226Zm-342 0q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM40-160v-112q0-34 17.5-62.5T104-378q62-31 126-46.5T360-440q66 0 130 15.5T616-378q29 15 46.5 43.5T680-272v112H40Z"/></svg>
                        <span>Verification</span>
                    </h3>
                    <form action="" class="pr-2.5 w-1/2 flex flex-col gap-1 *:grid *:grid-cols-2 *:items-center *:gap-5 font-semibold">
                        <span>
                            <label for="email">Email Address:</label>
                            <input type="text" name="email" id="email" class="px-2.5 py-0.5 rounded-md bg-neutral-300 border border-neutral-400" value="<?php echo $email;?>" disabled>
                        </span>
                        <span>
                            <div></div>
                            <p class="text-sm text-right select-none font-bold <?php echo $verified == 0 ? 'text-dgreen' : 'text-red-700';?>"><?php echo $verified == 0 ? 'Your account is already verified' : 'Your account is not yet verified.'; ?></p>
                        </span>
                        <span class="<?php echo $verified == 0 ? 'hidden!' : ''?>">
                            <label for="verification-code">Verification Code:</label>
                            <span class="flex items-center justify-between gap-1 *:whitespace-nowrap">
                                <input type="text" name="verification-code" id="verification-code" class="px-1 py-0.5 rounded-md bg-white border border-neutral-400 flex-1">
                                <span class="relative">
                                    <svg class="size-6.5 cursor-pointer peer" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor"><path d="M440-280h80v-240h-80v240Zm40-320q17 0 28.5-11.5T520-640q0-17-11.5-28.5T480-680q-17 0-28.5 11.5T440-640q0 17 11.5 28.5T480-600Zm0 520q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Z"/></svg>
                                    <div class="hidden peer-hover:block absolute top-8 left-1/2 -translate-x-1/2 px-1 rounded-sm bg-zinc-700 text-off-white text-xs text-center select-none"">
                                        Please check your email to complete your verification.
                                    </div>
                                </span>
                            </span>
                        </span>
                        <span class="<?php echo $verified == 0 ? 'hidden!' : ''?>">
                            <div></div>
                            <button class="px-5 py-0.5 rounded-md text-sm bg-dirty-brown disabled:bg-neutral-400 text-off-white disabled:cursor-not-allowed enabled:cursor-pointer enabled:hover:opacity-85 enabled:active:scale-95" disabled>Verify</button>
                        </span>
                    </form>
                </div>
                <br class="select-none">
                <!-- CHANGE PASSWORD -->
                <div>
                    <h3 class="font-bold text-xl flex items-center gap-2.5">
                        <svg class="size-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor"><path d="M240-80q-33 0-56.5-23.5T160-160v-400q0-33 23.5-56.5T240-640h40v-80q0-83 58.5-141.5T480-920q83 0 141.5 58.5T680-720v80h40q33 0 56.5 23.5T800-560v52q-18-6-37.5-9t-42.5-3q-116 0-198 82t-82 198q0 45 13 84t37 76H240Zm480 40q-83 0-141.5-58.5T520-240q0-83 58.5-141.5T720-440q83 0 141.5 58.5T920-240q0 83-58.5 141.5T720-40ZM360-640h240v-80q0-50-35-85t-85-35q-50 0-85 35t-35 85v80Zm360 400q25 0 42.5-17.5T780-300q0-25-17.5-42.5T720-360q-25 0-42.5 17.5T660-300q0 25 17.5 42.5T720-240Zm0 120q30 0 56-14t43-39q-23-14-48-20.5t-51-6.5q-26 0-51 6.5T621-173q17 25 43 39t56 14Z"/></svg>
                        <span>Change Password</span>
                    </h3>
                    <form class="w-full flex gap-5 *:flex-1 font-semibold">
                        <div class="flex flex-col gap-1 *:grid *:grid-cols-2 *:items-center *:gap-5">
                            <span>
                                <label for="current-password">Current Password:</label>
                                <input type="text" name="current-password" id="current-password" class="px-2.5 py-0.5 rounded-md bg-white border border-neutral-400">
                            </span>
                            <hr class="my-2 border opacity-20">
                            <span>
                                <label for="new-password">New Password:</label>
                                <input type="text" name="new-password" id="new-password" class="px-2.5 py-0.5 rounded-md bg-white border border-neutral-400">
                            </span>
                            <span>
                                <label for="confirm-password">Confirm Password:</label>
                                <input type="text" name="change-password" id="change-password" class="px-2.5 py-0.5 rounded-md bg-white border border-neutral-400">
                            </span>
                            <div>
                                <div></div>
                                <button class="px-5 py-0.5 rounded-md text-sm bg-dirty-brown disabled:bg-neutral-400 text-off-white disabled:cursor-not-allowed enabled:cursor-pointer enabled:hover:opacity-85 enabled:active:scale-95" disabled>Change Password</button>
                            </div>
                        </div>
                        <div class="flex items-center justify-center">
                            <div class="p-2.5 rounded-md bg-yellow/70 border border-yellow-500 shadow-md">
                                <p class="font-extrabold">We recommend using a strong password that includes:</p>
                                <ul class="list-disc *:ml-5">
                                    <li id="length-pattern">Having more than 8 characters</li>
                                    <li id="uppercase-pattern">Having at least 1 uppercase</li>
                                    <li id="alphanumeric-pattern">Having at least 1 number or special characters</li>
                                </ul>
                            </div>
                        </div>
                    </form>
                </div>
                
            </div>
        </div>

        <!-- ACCOUNT CONTROL -->
        <div id="account-control" class="flex-1 hidden">
            <div class="size-full flex flex-col gap-5">
                <h2 class="text-3xl font-bold select-none">Account Control</h2>
                <div>
                    <h3 class="text-xl text-red-800 font-semibold flex items-center gap-1">
                        <svg class="size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor"><path d="m40-120 440-760 440 760H40Zm440-120q17 0 28.5-11.5T520-280q0-17-11.5-28.5T480-320q-17 0-28.5 11.5T440-280q0 17 11.5 28.5T480-240Zm-40-120h80v-200h-80v200Z"/></svg>
                        Danger Zone
                    </h3>
                    <div>
                        <p><b>Delete your account </b> | Permanently remove your account and all associated data.</p>
                        <form action="customize.php" method="post">
                            <input type="password" name="delete-password" id="delete-password" class="px-2.5 py-0.5 w-80 rounded-md bg-white border border-neutral-400" placeholder="Enter your password to confirm" required>
                            <p class="text-sm text-red-800">This action cannot be undone.</p>
                            <button name="delete-account" class="px-5 py-0.5 rounded-md bg-red-800 text-off-white text-sm shadow-md cursor-pointer hover:opacity-85 active:scale-95">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function updateSection() {
                const current = location.hash.slice(1) || 'account-information';
                ['account-information', 'preferences', 'security', 'account-control'].forEach(id => {
                    const el = document.getElementById(id);
                    const elhref = document.querySelector(`a[href="#${id}"]`);
                    console.log(elhref);
                    elhref?.classList.toggle('bg-off-white', id == current);
                    el?.classList.toggle('hidden', id !== current);
                });
            }

            updateSection();
            window.addEventListener('hashchange', updateSection);
        </script>


    </main>
</body>
</html>