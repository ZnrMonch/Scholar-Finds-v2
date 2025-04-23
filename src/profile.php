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
        $bookmarks = $user['bookmarks'];
        $personalization = $user['personalization'];
        $dp = substr($personalization, 0, 2);
        $bg = substr($personalization, 3);
        setcookie("personalization", $personalization, time() + (86400 * 30), "/");
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
    <main class="ml-25 m-5 p-15 w-[calc(100vw-135px)] min-h-[calc(100vh-40px)] h-auto rounded-4xl flex flex-col bg-[#eeeeee] z-2 text-dirty-brown drag-none
    max-tablet:m-0 max-tablet:p-5 max-tablet:min-h-screen max-tablet:size-full max-tablet:rounded-none">
        <div id="overlay-customize-profile" onclick="toggleFlex('customize-profile')" class="fixed top-0 left-0 z-2 size-full bg-black/40 hidden"></div>
        <div id="overlay-edit-intro" onclick="toggleEditIntro()" class="fixed z-2 top-0 left-0 size-full bg-black/40 hidden"></div>
        <div class="relative w-full h-70 max-big-phone:h-50 rounded-2xl <?php echo $bg; ?>">
            <button onclick="toggleFlex('customize-profile')" class="absolute top-4 right-5 hover:brightness-125 cursor-pointer"><svg class="size-5 max-big-phone:size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><!-- Icon from Material Symbols by Google - https://github.com/google/material-design-icons/blob/master/LICENSE --><path fill="currentColor" d="M10 15q-.425 0-.712-.288T9 14v-2.425q0-.4.15-.763t.425-.637l8.6-8.6q.3-.3.675-.45t.75-.15q.4 0 .763.15t.662.45L22.425 3q.275.3.425.663T23 4.4t-.137.738t-.438.662l-8.6 8.6q-.275.275-.637.438t-.763.162zm9.6-9.2l1.425-1.4l-1.4-1.4L18.2 4.4zM5 21q-.825 0-1.412-.587T3 19V5q0-.825.588-1.412T5 3h6.5q.35 0 .575.175t.35.45t.087.55t-.287.525l-4.65 4.65q-.275.275-.425.638T7 10.75V15q0 .825.588 1.412T9 17h4.225q.4 0 .763-.15t.637-.425L19.3 11.75q.25-.25.525-.288t.55.088t.45.35t.175.575V19q0 .825-.587 1.413T19 21z"/></svg></button>     
        </div>
        <form action="customize.php" method="post" id="customize-profile" class="fixed z-3 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 p-10 w-120 rounded-xl hidden flex-col gap-5 drop-shadow-lg bg-off-white text-dirty-brown **:outline-none">
            <div class="relative">
                <button onclick="toggleFlex('customize-profile')" type="button" class="absolute -top-2.5 -right-2.5 cursor-pointer hover:opacity-80 active:scale-95 duration-200"><svg class="size-7" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg></button>
                <h2 class="py-5 font-bold text-2xl text-center">Customize Profile</h2>
            </div>
            <div class="flex flex-col gap-1">
                <h2 class="font-semibold select-none">Background Cover:</h2>
                <div class="h-15 flex gap-1 *:flex-1 *:h-full *:rounded-md font-semibold text-sm select-none **:cursor-pointer">
                    <input type="radio" name="bg" id="golden-blush" value="golden-blush" class="peer/gb" checked hidden>
                    <input type="radio" name="bg" id="pink-panther" value="pink-panther" class="peer/pp" hidden>
                    <input type="radio" name="bg" id="dusty-grass" value="dusty-grass" class="peer/dg" hidden>
                    <input type="radio" name="bg" id="snowy-mint" value="snowy-mint" class="peer/sm" hidden>
                    <div class="bg-golden-blush group opacity-40 peer-checked/gb:opacity-100">
                        <label for="golden-blush" class="size-full items-center justify-center hidden group-hover:flex">Golden Blush</label>
                    </div>
                    <div class="bg-pink-panther group opacity-40 peer-checked/pp:opacity-100">
                        <label for="pink-panther" class="size-full items-center justify-center hidden group-hover:flex">Pink Panther</label>
                    </div>
                    <div class="bg-dusty-grass group opacity-40 peer-checked/dg:opacity-100">
                        <label for="dusty-grass" class="size-full items-center justify-center hidden group-hover:flex">Dusty Grass</label>
                    </div>
                    <div class="bg-snowy-mint group opacity-40 peer-checked/sm:opacity-100">
                        <label for="snowy-mint" class="size-full items-center justify-center hidden group-hover:flex">Snowy Mint</label>
                    </div>
                </div>
            </div>
            <div class="w-full h-35">
                <h2 class="font-semibold select-none">Profile Picture:</h2>
                <div class="size-full grid grid-cols-5 items-center justify-center ">
                    <input type="radio" name="dp" id="dp1" value="01" class="peer/dp1" checked hidden>
                    <input type="radio" name="dp" id="dp2" value="02" class="peer/dp2" hidden>
                    <input type="radio" name="dp" id="dp3" value="03" class="peer/dp3" hidden>
                    <input type="radio" name="dp" id="dp4" value="04" class="peer/dp4" hidden>
                    <input type="radio" name="dp" id="dp5" value="05" class="peer/dp5" hidden>
                    <input type="radio" name="dp" id="dp6" value="06" class="peer/dp6" hidden>
                    <input type="radio" name="dp" id="dp7" value="07" class="peer/dp7" hidden>
                    <input type="radio" name="dp" id="dp8" value="08" class="peer/dp8" hidden>
                    <input type="radio" name="dp" id="dp9" value="09" class="peer/dp9" hidden>
                    <input type="radio" name="dp" id="dp10" value="10" class="peer/dp10" hidden>
                    <div class="peer-checked/dp1:opacity-100 opacity-40">
                        <label for="dp1" class="size-15"><img src="resources/dp/01.svg" alt="" class="size-15 rounded-full border-2 border-dirty-brown cursor-pointer"></label>
                    </div>
                    <div class="peer-checked/dp2:opacity-100 opacity-40">
                        <label for="dp2" class="size-15"><img src="resources/dp/02.svg" alt="" class="size-15 rounded-full border-2 border-dirty-brown cursor-pointer"></label>
                    </div>
                    <div class="peer-checked/dp3:opacity-100 opacity-40">
                        <label for="dp3" class="size-15"><img src="resources/dp/03.svg" alt="" class="size-15 rounded-full border-2 border-dirty-brown cursor-pointer"></label>
                    </div>
                    <div class="peer-checked/dp4:opacity-100 opacity-40">
                        <label for="dp4" class="size-15"><img src="resources/dp/04.svg" alt="" class="size-15 rounded-full border-2 border-dirty-brown cursor-pointer"></label>
                    </div>
                    <div class="peer-checked/dp5:opacity-100 opacity-40">
                        <label for="dp5" class="size-15"><img src="resources/dp/05.svg" alt="" class="size-15 rounded-full border-2 border-dirty-brown cursor-pointer"></label>
                    </div>
                    <!-- lower -->
                    <div class="peer-checked/dp6:opacity-100 opacity-40">
                        <label for="dp6" class="size-15"><img src="resources/dp/06.svg" alt="" class="size-15 rounded-full border-2 border-dirty-brown cursor-pointer"></label>
                    </div>
                    <div class="peer-checked/dp7:opacity-100 opacity-40">
                        <label for="dp7" class="size-15"><img src="resources/dp/07.svg" alt="" class="size-15 rounded-full border-2 border-dirty-brown cursor-pointer"></label>
                    </div>
                    <div class="peer-checked/dp8:opacity-100 opacity-40">
                        <label for="dp8" class="size-15"><img src="resources/dp/08.svg" alt="" class="size-15 rounded-full border-2 border-dirty-brown cursor-pointer"></label>
                    </div>
                    <div class="peer-checked/dp9:opacity-100 opacity-40">
                        <label for="dp9" class="size-15"><img src="resources/dp/09.svg" alt="" class="size-15 rounded-full border-2 border-dirty-brown cursor-pointer"></label>
                    </div>
                    <div class="peer-checked/dp10:opacity-100 opacity-40">
                        <label for="dp10" class="size-15"><img src="resources/dp/10.svg" alt="" class="size-15 rounded-full border-2 border-dirty-brown cursor-pointer"></label>
                    </div>
                </div>
            </div>
            <div class="my-5 flex justify-center">
                <input type="text" name="userid" id="userid" value="<?php echo $userId;?>" hidden>
                <button type="submit" name="save-cp" class="px-5 py-1 w-30 rounded-md bg-lgreen font-bold hover:opacity-80 duration-100 cursor-pointer">Save</button>
            </div>
        </form>
        <div class="flex-1 relative w-full flex flex-col gap-5 max-big-phone:gap-10">
            <div class="group absolute -top-25 max-big-phone:-top-20 left-10 max-big-phone:left-1/2 max-big-phone:-translate-x-1/2 size-50 max-big-phone:size-40 rounded-full border-6 border-off-white overflow-clip">
                <img src="resources/dp/<?php echo $dp;?>.svg" alt="" class="relative z-1 select-none">
            </div>
            <div class="pl-70 pr-0 py-3 max-big-phone:p-0 max-big-phone:pt-37.5 w-full h-25 flex justify-between">
                <div class="h-full flex flex-col max-big-phone:items-center justify-end gap-1 **:leading-none">
                    <h1 class="font-bold text-2xl max-big-phone:text-xl"><?php echo $name;?> <i class="text-lg">(<?php echo '@' . strtolower($username);?>)</i></h1>
                    <h2 class="opacity-80 flex items-center gap-2 max-big-phone:text-sm"><?php echo ucfirst($membership);?><span>•</span><span><?php echo $college == 'hsu' ? 'Higher School ng UMak' : ($college == 'ccis' ? 'College of Computing and Information Sciences' : ''); ?></span></h2>
                    <i class="opacity-80 text-sm max-big-phone:text-xs">Joined in <span class="font-bold">January 2023</span></i>
                </div>
                <div>
                    <button onclick="logOut()" class="px-5 py-0.5 rounded-md bg-red-900 text-off-white text-sm select-none cursor-pointer hover:opacity-80 active:scale-95 duration-200">
                        Sign out
                    </button>
                </div>
            </div>
            <div class="flex-1 w-full flex max-big-phone:flex-col gap-5">
                <div class="relative p-5 w-100 max-tablet:w-70 max-big-phone:w-full rounded-xl bg-zinc-200 border border-zinc-300 flex flex-col gap-5 **:leading-none">
                    <button onclick="toggleEditIntro()" class="absolute top-5 right-5 hover:brightness-125 cursor-pointer"><svg class="size-5 max-big-phone:size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><!-- Icon from Material Symbols by Google - https://github.com/google/material-design-icons/blob/master/LICENSE --><path fill="currentColor" d="M10 15q-.425 0-.712-.288T9 14v-2.425q0-.4.15-.763t.425-.637l8.6-8.6q.3-.3.675-.45t.75-.15q.4 0 .763.15t.662.45L22.425 3q.275.3.425.663T23 4.4t-.137.738t-.438.662l-8.6 8.6q-.275.275-.637.438t-.763.162zm9.6-9.2l1.425-1.4l-1.4-1.4L18.2 4.4zM5 21q-.825 0-1.412-.587T3 19V5q0-.825.588-1.412T5 3h6.5q.35 0 .575.175t.35.45t.087.55t-.287.525l-4.65 4.65q-.275.275-.425.638T7 10.75V15q0 .825.588 1.412T9 17h4.225q.4 0 .763-.15t.637-.425L19.3 11.75q.25-.25.525-.288t.55.088t.45.35t.175.575V19q0 .825-.587 1.413T19 21z"/></svg></button>
                    <h2 class="font-bold text-xl max-big-phone:text-lg select-none">Introduction</h2>
                    <p class="text-justify max-big-phone:text-sm">
                        <?php echo $bio ?>
                    </p>
                    <div id="profile-info" class="flex big-phone:flex-col gap-5 max-big-phone:text-sm">
                        <p class="max-big-phone:flex-1 flex flex-col gap-1">
                            <b class="select-none whitespace-nowrap">Year and Section:</b>
                            <span><?php echo !empty($yearsection) ? implode(' - ', $yearsection) : ''?></span>
                        </p>
                        <p class="flex flex-col gap-1">
                            <b class="select-none whitespace-nowrap">Email Address:</b>
                            <span class="break-all"><?php echo $email ?></span>
                        </p>
                    </div>
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
                                    <input type="radio" name="college" value="ccis" id="CCIS" onchange="toggleInputs('ccis-input'); toggleInputs('hsu-input');" class="peer" checked>
                                    <label for="CCIS" class="font-semibold select-none opacity-50 peer-checked:opacity-100">CCIS</label>
                                </span>
                                <span>
                                    <input type="radio" name="college" value="hsu" id="HSU" onchange="toggleInputs('ccis-input'); toggleInputs('hsu-input');" class="peer">
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
                            ['ccis-input', 'year', 'section'].forEach(id => {
                                const el = document.getElementById(id);
                                el.classList?.toggle('hidden', !ccis);
                                el.disabled = !ccis;
                            });
                            ['hsu-input', 'shs-grade', 'shs-section'].forEach(id => {
                                const el = document.getElementById(id);
                                el.classList?.toggle('hidden', ccis);
                                el.disabled = ccis;
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
                <div class="relative flex-1 mt-5 flex flex-col gap-2.5 **:leading-none">
                    <div class="flex items-center justify-between select-none">
                        <h2 class="text-xl max-big-phone:text-lg font-bold">Bookmarks</h2>
                        <a href="library.php" class="text-sm font-bold">Show More</a>
                    </div>
                    <div class="flex-1 relative">
                        <div class="absolute z-1 bottom-0 w-full h-1/2 max-tablet:h-3/4 bg-linear-to-b from-transparent to-off-white"></div>
                        <div class="relative z-1 flex flex-col gap-2 *:h-20 max-tablet:*:h-27 max-big-phone:*:h-20">
                        <?php
                            $allBookmarks = array_slice(explode('-', $bookmarks), 0, 4);
                            $placeholders = implode(',', array_fill(0, count($allBookmarks), '?'));

                            $stmt = $conn->prepare("SELECT * FROM theses WHERE thesis_id IN ($placeholders)");
                            $stmt->bind_param(str_repeat('s', count($allBookmarks)), ...$allBookmarks);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            $theses = $result->fetch_all(MYSQLI_ASSOC);
                            
                            foreach ($theses as $thesis):
                                $textColor = $thesis['course'] === "BSCS-AD" ? "text-violet-950" : ($thesis['course'] === "BSIT-NS" ? "text-sky-950" : "text-yellow-950"); ?>
                                <div class="relative w-full px-5 p-2.5 h-25 max-big-phone:h-27.5 rounded-xl bg-neutral-100 border border-neutral-300 hover:border-dirty-brown/70 flex gap-2.5">
                                    <div class="flex-1 flex flex-col items-start justify-center select-text">
                                        <h3 class="font-bold text-base leading-4 max-big-phone:text-sm max-big-phone:leading-3 <?= $textColor ?>">
                                            <?= htmlspecialchars($thesis['title']) ?>
                                            <?php if (trim($thesis['course']) !== "BSCS-AD" && trim($thesis['course']) !== "BSIT-NS"): ?>
                                                <i class="max-big-phone:mb-0.5 px-2 rounded-full bg-yellow-800/80 text-xs text-off-white select-none">
                                                    <?= htmlspecialchars($thesis['course']) ?>
                                                </i>
                                            <?php endif; ?>
                                        </h3>
                                        <i class="leading-none text-sm max-big-phone:text-xs">
                                            <?php
                                                $authors = explode("-", $thesis['authors']);
                                                echo htmlspecialchars(implode(", ", $authors));
                                            ?>
                                        </i>
                                    </div>
                                </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function toggleFlex(targetElement) {
                let element = document.getElementById(targetElement);
                let overlay = document.getElementById('overlay-' + targetElement);
                overlay.classList.toggle('hidden');
                element.classList.toggle('hidden');
                element.classList.toggle('flex');
            }
        </script>
    </main>
</body>
</html>