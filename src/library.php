<?php
    session_start();
    if (!isset($_COOKIE['id'])) {
        $_SESSION['log-error'] = "We can't take you there yet! Please log in first.";
        header("Location: access.php");
        exit();
    }

    require_once 'config.php';
    require 'data.php';

    if (isset($_GET['view'])) {
        $view = $_GET['view'];
        setcookie('view', $view, time() + 86400, "/");

        $personalVisits = isset($_COOKIE['personal-visits']) ? explode('-', $_COOKIE['personal-visits']) : [];

        if (!in_array($view, $personalVisits)) {
            $personalVisits[] = $view;
            $updatedVisits = implode('-', $personalVisits);
            setcookie('personal-visits', $updatedVisits, time() + 86400, "/");
            $stmt = $conn->prepare('SELECT email FROM accounts where user_id = ?');
            $stmt->bind_param('s', $_COOKIE['id']);
            $stmt->execute();
            $initiator = $stmt->get_result()->fetch_assoc()['email'];
            $stmt->close();
    
            $stmt = $conn->prepare('UPDATE theses SET visits = visits + 1 WHERE thesis_id = ?');
            $stmt->bind_param('s', $view);
            $stmt->execute();
            $stmt->close();

            $result = mysqli_query($conn, 'SELECT reference_id FROM logs WHERE reference_id LIKE "VIW#%" ORDER BY CAST(SUBSTRING_INDEX(reference_id, "#", -1) AS UNSIGNED) DESC LIMIT 1');
            $row = mysqli_fetch_assoc($result);

            if ($row && isset($row['reference_id'])) {
                $lastId = (int)substr($row['reference_id'], 4);
                $newId = 'VIW#' . str_pad($lastId + 1, 6, '0', STR_PAD_LEFT);
            } else {
                $newId = 'VIW#000001';
            }

            $stmt = $conn->prepare('INSERT INTO logs (reference_id, type, operation, date, details, initiator) VALUES (?, ?, ?, ?, ?, ?)');
            $refType = 'thesis';
            $operation = 'visited';
            $date = date('Y-m-d');
            $details = 'Viewed the details of Thesis [ID#' . $view . ']';

            $stmt->bind_param('ssssss', $newId, $refType, $operation, $date, $details, $initiator);
            $stmt->execute();
            $stmt->close();
        }
    } else {
        setCookie('view', '', time() - 3600, "/");
    }
    
    $dp = substr($_COOKIE['personalization'], 0, 2);
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
    <script>
        let data = { theses: [] }, thesesPerPage = 12, selectedSet = 0, searchQuery = "", searchCategory = "title", selectedYears = [], defaultSet = true;
        
        fetch('data.json')
            .then(res => res.json())
            .then(jsonData => { data = jsonData; updateYears(); displaySets();})
            .catch(err => console.error("Error retrieving data:", err));         
    </script>
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
    <main class="relative z-2 w-screen">
        <div id="library" class="ml-25 m-5 p-15 w-[calc(100vw-135px)] min-h-[calc(100vh-40px)] h-auto rounded-4xl flex flex-col bg-off-white text-dirty-brown drag-none z-1
        max-tablet:m-0 max-tablet:p-7.5 max-phone:p-6 max-tablet:w-full max-tablet:min-h-screen max-tablet:rounded-none">
            <div class="w-full flex justify-between items-center">
                <!-- SEARCH / FILTER -->
                <div class="relative flex items-center gap-2.5 max-big-phone:gap-1">
                    <span class="relative select-none">
                        <svg class="absolute top-1/2 -translate-y-1/2 left-2 size-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="#585345"><path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
                        <input id="search-box" placeholder="Search for a thesis book..." class="px-5 pl-8 py-1 max-tablet:w-60 max-phone:w-48 w-80 border-2 rounded-2xl border-dirty-brown max-tablet:text-sm max-phone:text-xs text-dirty-brown outline-none">
                    </span>
                    <div class="relative flex gap-2.5 **:select-none">
                        <div onclick="toggleFlex('search-filters')" class="p-2 max-phone:p-1.5 rounded-lg bg-dirty-brown text-off-white cursor-pointer"><svg class="size-5 max-tablet:size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor"><path d="M440-120v-240h80v80h320v80H520v80h-80Zm-320-80v-80h240v80H120Zm160-160v-80H120v-80h160v-80h80v240h-80Zm160-80v-80h400v80H440Zm160-160v-240h80v80h160v80H680v80h-80Zm-480-80v-80h400v80H120Z"/></svg></div>
                        <div id="search-filters" class="absolute z-2 left-1/2 -translate-x-1/2 translate-y-10 hidden gap-2 p-3 w-max rounded-md drop-shadow-lg bg-neutral-200 border border-neutral-400 text-sm *:flex *:flex-col">
                            <div>
                                <b>Search by:</b>
                                <span class="grid grid-cols-[auto_1fr] gap-0.5">
                                    <input type="radio" name="query-filter" id="title" checked>
                                    <label for="title">Title</label>
                                    <input type="radio" name="query-filter" id="authors">
                                    <label for="authors">Authors</label>
                                </span>
                                <br>
                                <b>Inclusions:</b>
                                <span class="grid grid-cols-[auto_1fr] gap-0.5">
                                    <input type="radio" name="include" id="default" checked>
                                    <label for="default">Default</label>
                                    <input type="radio" name="include" id="no-info">
                                    <label for="no-info">No Information</label>
                                </span>
                            </div>
                            <div class="w-max">     
                                <b>Published Year:</b>
                                <div id="year-filters" class="flex items-start gap-3">
                                    
                                </div>                  
                            </div>
                        </div>
                        <script>
                            function updateYears() {
                                const allYears = [...new Set(data.theses.map(thesis => thesis.published_date.substring(0, 4)))];

                                const half = Math.ceil(allYears.length / 2);
                                const firstHalf = allYears.slice(0, half);
                                const secondHalf = allYears.slice(half);

                                document.getElementById('year-filters').innerHTML = `
                                    <span class="grid grid-cols-[auto_1fr] items-center gap-0.5 tablet:flex-1">
                                        ${firstHalf.map(year => `
                                            <input type="checkbox" name="year" id="${year}">
                                            <label for="${year}" class="pl-1">${year}</label>
                                        `).join('')}
                                    </span>
                                    <span class="grid grid-cols-[auto_1fr] items-center gap-0.5 tablet:flex-1">
                                        ${secondHalf.map(year => `
                                            <input type="checkbox" name="year" id="${year}">
                                            <label for="${year}" class="pl-1">${year}</label>
                                        `).join('')}
                                    </span>
                                `;

                                syncFilters();
                                filterSearch();
                                document.querySelectorAll('input[name="query-filter"], input[name="year"], input[name="include"], input[name="lib-category"], #search-box').forEach(input => {
                                    input.addEventListener('change', filterSearch);
                                });
                            }

                            function syncFilters() {
                                const filters = JSON.parse(getCookie('filters'));
                                if (filters) {
                                    document.querySelector(`input[name="query-filter"][id="${filters.searchCategory}"]`).checked = true;
                                    filters.selectedYears.forEach(year => {
                                        document.getElementById(year).checked = true;
                                    });
                                    document.querySelector(`input[name="include"][id="${filters.inclusion}"]`).checked = true;
                                    document.querySelector(`input[name="lib-category"][id="${filters.typeCategory}"]`).checked = true;
                                    document.getElementById('search-box').value = filters.searchBox || '';
                                    selectedSet = 0, searchQuery = filters.searchBox, searchCategory = filters.searchCategory, selectedYears = filters.selectedYears, defaultSet = filters.inclusion == 'default';
                                    console.log(searchQuery, searchCategory, selectedYears, defaultSet);
                                }
                                
                            }

                            function filterSearch() {
                                searchCategory = document.querySelector('input[name="query-filter"]:checked')?.id || 'title';
                                selectedYears = Array.from(document.querySelectorAll('input[name="year"]:checked')).map(input => input.id);
                                inclusion = document.querySelector('input[name="include"]:checked')?.id || 'default';
                                defaultSet = inclusion == 'default';
                                let selected = document.querySelector('input[name="lib-category"]:checked');
                                let typeCategory = selected?.id === 'category-bookmarks' 
                                    ? (filterBookmarks = true, searchCourse = () => true, 'category-bookmarks') 
                                    : (filterBookmarks = false, searchCourse = book => 
                                        selected?.id === 'category-it' ? book.course === 'BSIT-NS' : 
                                        selected?.id === 'category-cs' ? book.course === 'BSCS-AD' : 
                                        selected?.id === 'category-others' ? book.course !== 'BSIT-NS' && book.course !== 'BSCS-AD' : true,
                                    selected?.id || 'category-all');
                                searchBox = document.getElementById('search-box').value.toLowerCase();
                                setCookie('filters', JSON.stringify({ searchCategory, selectedYears, inclusion, typeCategory, searchBox }), 30);
                                displaySets();
                            }
                        </script>
                    </div>
                </div>
                <div class="flex gap-2.5 max-tablet:gap-2 max-phone:gap-1">
                    <button onclick="toggleSpotify()" class="hover:opacity-85 duration-200 cursor-pointer drop-shadow-md">
                        <svg id="spotify-button" class="p-2 size-12.5 max-tablet:size-10 max-phone:size-7.5 rounded-full border-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><!-- Icon from Solar by 480 Design - https://creativecommons.org/licenses/by/4.0/ --><path fill="currentColor" d="m12.75 12.508l8.5-3.4v5.653a3.25 3.25 0 1 0 1.5 2.74V7.945c0-1.143 0-2.101-.08-2.865a8 8 0 0 0-.04-.315c-.078-.522-.214-1.008-.479-1.415a2.2 2.2 0 0 0-.62-.63l-.007-.005c-.708-.47-1.503-.437-2.322-.228c-.792.202-1.774.613-2.978 1.117l-2.094.876c-.565.236-1.043.437-1.418.644c-.4.22-.743.48-1.001.868s-.366.805-.415 1.259c-.046.426-.046.945-.046 1.557v7.952a3.25 3.25 0 1 0 1.5 2.74z"/><path fill="currentColor" d="M7.75 2a.75.75 0 0 0-1.5 0v5.76a3.25 3.25 0 1 0 1.5 2.74V5.005c.699.504 1.53.745 2.25.745a.75.75 0 0 0 0-1.5a2.44 2.44 0 0 1-1.488-.552c-.434-.357-.762-.9-.762-1.698" opacity=".5"/></svg>
                    </button>
                    <button class="hover:opacity-85 duration-200 cursor-pointer drop-shadow-md" onclick="window.location.href='profile.php'">
                        <img src="resources/dp/<?php echo $dp;?>.svg" alt="" class="size-12.5 max-tablet:size-10 max-phone:size-7.5 border-2 rounded-full select-none">
                    </button>
                </div>
            </div>
            <div class="mt-5 pb-10 flex max-[1000px]:flex-col [1000px]:items-center justify-between gap-10"> 
                <!-- CATEGORIES -->
                <div class="relative">
                    <h2 class="absolute text-2xl font-bold max-phone:text-lg select-none">Categories</h2>
                    <div class="pt-10 h-38 max-phone:h-30 flex gap-1 max-phone:gap-3 font-semibold max-phone:text-xs select-none *:flex *:flex-col *:items-center *:justify-end *:relative *:cursor-pointer *:drop-shadow-md *:w-20 max-phone:*:w-12">
                        <label for="category-all" class="group">
                            <input type="radio" name="lib-category" id="category-all" hidden checked class="peer">
                            <img src="resources/book-all.svg" alt="" class="absolute top-0 group-hover:-translate-y-2 duration-200 size-20 max-phone:size-15">
                            <p class="opacity-60 decoration-2 underline-offset-4 peer-checked:underline peer-checked:opacity-100 duration-200">All</p>
                        </label>
                        <label for="category-cs" class="group">
                            <input type="radio" name="lib-category" id="category-cs" hidden class="peer">
                            <img src="resources/book-cs.svg" alt="" class="absolute top-0 group-hover:-translate-y-2 duration-200 size-20 max-phone:size-15">
                            <p class="opacity-60 decoration-2 underline-offset-4 peer-checked:underline peer-checked:opacity-100 duration-200">BSCS</p>
                        </label>
                        <label for="category-it" class="group">
                            <input type="radio" name="lib-category" id="category-it" hidden class="peer">
                            <img src="resources/book-it.svg" alt="" class="absolute top-0 group-hover:-translate-y-2 duration-200 size-20 max-phone:size-15">
                            <p class="opacity-60 decoration-2 underline-offset-4 peer-checked:underline peer-checked:opacity-100 duration-200">BSIT</p>
                        </label>
                        <label for="category-others" class="group">
                            <input type="radio" name="lib-category" id="category-others" hidden class="peer">
                            <img src="resources/book-others.svg" alt="" class="absolute top-0 group-hover:-translate-y-2 duration-200 size-20 max-phone:size-15">
                            <p class="opacity-60 decoration-2 underline-offset-4 peer-checked:underline peer-checked:opacity-100 duration-200">Others</p>
                        </label>
                        <label for="category-bookmarks" class="group">
                            <input type="radio" name="lib-category" id="category-bookmarks" hidden class="peer">
                            <img src="resources/book-bookmarks.svg" alt="" class="absolute top-0 group-hover:-translate-y-2 duration-200 size-20 max-phone:size-15">
                            <p class="opacity-60 decoration-2 underline-offset-4 peer-checked:underline peer-checked:opacity-100 duration-200">Bookmarks</p>
                        </label>
                    </div>
                </div>
                
                <!-- SPOTIFY PLAYLIST -->
                <div id="spotify" class="hidden w-120 max-tablet:w-100 max-[1000px]:w-full animate-fadeIn drop-shadow-md">
                    <iframe style="border-radius:12px" src="https://open.spotify.com/embed/playlist/53OLQuZ3VKjbp9SlfqKA40?utm_source=generator&theme=0" width="100%" height="152" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
                </div>
            </div>
            <div>
                <!-- CONTROLS -->
                <div class="mb-2.5 flex justify-between items-center **:select-none">
                    <h2 class="text-2xl font-bold max-phone:text-lg">Library</h2>
                    <div class="flex items-center gap-2 text-sm">
                        <i id="page-info" class="max-big-phone:hidden">
                            Showing 1 out of 1 set of results
                        </i>
                        <select id="set-per-page" class="px-10 max-phone:px-5 py-0.5 max-phone:py-0 rounded-xl bg-neutral-200 disabled:cursor-not-allowed border border-neutral-400 max-phone:text-xs outline-none cursor-pointer">
                            <option value="1">1</option>
                        </select>
                        <div class="px-5 max-phone:px-2 py-0.5 rounded-xl flex items-center gap-2 bg-neutral-200 border border-neutral-400">
                            <input type="radio" name="view" id="grid-view" hidden checked class="peer/gv">
                            <input type="radio" name="view" id="list-view" hidden class="peer/lv">
                            <label for="grid-view" class="opacity-40 peer-checked/gv:opacity-100 cursor-pointer">
                                <svg class="size-5 max-phone:size-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><!-- Icon from MingCute Icon by MingCute Design - https://github.com/Richard9394/MingCute/blob/main/LICENSE --><g fill="#585345" fill-rule="evenodd"><path d="m12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035q-.016-.005-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093q.019.005.029-.008l.004-.014l-.034-.614q-.005-.018-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z"/><path fill="currentColor" d="M9 13a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2zm10 0a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2zM9 3a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2zm10 0a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/></g></svg>
                            </label>
                            <label for="list-view" class="opacity-40 peer-checked/lv:opacity-100 cursor-pointer">
                                <svg class="size-5 max-phone:size-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><!-- Icon from MingCute Icon by MingCute Design - https://github.com/Richard9394/MingCute/blob/main/LICENSE --><g fill="none"><path d="m12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035q-.016-.005-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093q.019.005.029-.008l.004-.014l-.034-.614q-.005-.018-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z"/><path fill="currentColor" d="M7 13a2 2 0 0 1 1.995 1.85L9 15v3a2 2 0 0 1-1.85 1.995L7 20H4a2 2 0 0 1-1.995-1.85L2 18v-3a2 2 0 0 1 1.85-1.995L4 13zm9 4a1 1 0 0 1 .117 1.993L16 19h-4a1 1 0 0 1-.117-1.993L12 17zm4-4a1 1 0 1 1 0 2h-8a1 1 0 1 1 0-2zM7 3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2zm9 4a1 1 0 0 1 .117 1.993L16 9h-4a1 1 0 0 1-.117-1.993L12 7zm4-4a1 1 0 0 1 .117 1.993L20 5h-8a1 1 0 0 1-.117-1.993L12 3z"/></g></svg>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- LIST VIEW -->
                <div id="library-list" class="h-auto hidden flex-col gap-2.5">
                </div>
                
                <!-- GRID VIEW -->
                <div id="library-grid" class="grid grid-cols-[repeat(auto-fit,minmax(520px,1fr))] max-tablet:grid-cols-[repeat(auto-fit,minmax(440px,1fr))] max-big-phone:flex flex-col flex-wrap gap-5">
                </div>
            </div>
        </div>

        <!-- FULL VIEW -->
        <div id="full-view" class="ml-25 m-5 p-15 w-[calc(100vw-135px)] min-h-[calc(100vh-40px)] h-auto rounded-4xl hidden flex-col bg-off-white text-dirty-brown drag-none z-1
        max-tablet:m-0 max-tablet:p-0 max-tablet:pb-10 max-tablet:w-full max-tablet:min-h-screen max-tablet:rounded-none">
            <div class="pb-2 max-tablet:py-2 max-tablet:px-10 max-big-phone:px-5">
                <button onclick="view()" class="flex items-center gap-2.5 max-big-phone:gap-1 font-semibold text-lg max-tablet:text-sm cursor-pointer hover:opacity-60 duration-200">
                    <svg class="size-6 max-tablet:size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><!-- Icon from Material Symbols by Google - https://github.com/google/material-design-icons/blob/master/LICENSE --><path fill="currentColor" d="m7.825 13l4.9 4.9q.3.3.288.7t-.313.7q-.3.275-.7.288t-.7-.288l-6.6-6.6q-.15-.15-.213-.325T4.426 12t.063-.375t.212-.325l6.6-6.6q.275-.275.688-.275t.712.275q.3.3.3.713t-.3.712L7.825 11H19q.425 0 .713.288T20 12t-.288.713T19 13z"/></svg>
                    Return to Library
                </button> 
            </div>  

            <!-- BANNER -->
            <div id="banner" class="relative w-full h-100 max-tablet:h-70 max-big-phone:h-45 rounded-3xl max-tablet:rounded-none bg-linear-135/oklch flex gap-10 max-tablet:gap-5">        
            </div>

            <!-- INFORMATION -->
            <div id="information" class="mt-25 max-big-phone:mt-15 max-phone:mt-5 max-tablet:px-5 w-full flex flex-col gap-10">
            </div>
        </div>
    </main>
    
    <script>
        let gridView = document.getElementById("grid-view");
        let listView = document.getElementById("list-view");
        const gridContainer = document.getElementById("library-grid");
        const listContainer = document.getElementById("library-list");

        window.onload = () => {
            document.getElementById("set-per-page")?.addEventListener("change", e => { selectedSet = e.target.value - 1; displaySets(); });
            document.getElementById("search-box")?.addEventListener("input", e => { searchQuery = e.target.value; selectedSet = 0; displaySets(); });

            gridView.addEventListener("change", () => {
                displaySets();
                gridContainer.classList.remove("hidden");
                gridContainer.classList.add("max-big-phone:flex");
                gridContainer.classList.add("grid");
                listContainer.classList.remove("flex");
                listContainer.classList.add("hidden");
            });

            listView.addEventListener("change", () => {
                displaySets();
                listContainer.classList.remove("hidden")
                listContainer.classList.add("flex");
                gridContainer.classList.remove("max-big-phone:flex")
                gridContainer.classList.remove("grid");
                gridContainer.classList.add("hidden");
            });
        };

        let searchCourse = () => true;
        let filterBookmarks;

        document.querySelectorAll("[name='lib-category']").forEach(r =>
            r.addEventListener("change", e => {
                searchCourse = book => 
                    e.target.id === "category-it" ? book.course === "BSIT-NS" :
                    e.target.id === "category-cs" ? book.course === "BSCS-AD" :
                    e.target.id === "category-others" ? book.course !== "BSIT-NS" && book.course !== "BSCS-AD" :
                    true;
                
                selectedSet = 0;
                filterBookmarks = e.target.id === "category-bookmarks" && e.target.checked;
                displaySets();
            })
        );

        /* ================================================================================================================= DISPLAYING LOGIC ================================================================================================================= */
  
        function displaySets() {
            const isGrid = gridView.checked;
            const container = isGrid ? gridContainer : listContainer;
            const pageInfo = document.getElementById("page-info");
            let bookmarks = data.accounts.find(acc => acc.user_id == getCookie('id'))?.bookmarks?.split('-') ?? [];
            container.innerHTML = "";

            let filteredData = data.theses?.filter(item =>
                (!searchQuery || item[searchCategory.toLowerCase()]?.toLowerCase().includes(searchQuery.toLowerCase())) &&
                (!searchCourse || searchCourse(item)) &&
                (selectedYears.length === 0 || selectedYears.includes(item.published_date.substring(0, 4))) &&
                (defaultSet ? item.abstract?.trim() !== "" : true) &&
                (filterBookmarks ? bookmarks.includes(item.thesis_id) : true)
            ) || [];

            let totalSets = Math.ceil(filteredData.length / thesesPerPage)
            selectedSet = Math.max(0, Math.min(selectedSet, totalSets - 1));
            const tsetsSelect = document.getElementById("set-per-page");
            let prevValue = Number(tsetsSelect.value) || 1;
            tsetsSelect.innerHTML = [...Array(totalSets)].map((_, i) => `<option value="${i + 1}">${i + 1}</option>`).join("");
            tsetsSelect.value = prevValue <= totalSets ? prevValue : (totalSets > 0 ? totalSets : "1");

            let thesesSlice = filteredData.slice(selectedSet * thesesPerPage, (selectedSet + 1) * thesesPerPage);
            const userBookmarks = (data.accounts.find(u => u.user_id === getCookie('id'))?.bookmarks || '').split('-');
            const getBM = thesisId => userBookmarks.includes(String(thesisId)) ? '-bm' : '';

            container.innerHTML = thesesSlice.map(item => {
                let singularColor = item.course == "BSCS-AD" ? "bg-purple-800" : item.course == "BSIT-NS" ? "bg-sky-700" : "bg-yellow-700";          
                let textColor = item.course == "BSCS-AD" ? "text-violet-900" : item.course == "BSIT-NS" ? "text-sky-900" : "text-yellow-800"; 
                let border = item.course == "BSCS-AD" ? "border-violet-900" : item.course == "BSIT-NS" ? "border-sky-900" : "border-yellow-800";          
                
                if (isGrid) {
                    return `
                        <form class="group relative mb-2.5 tablet:mb-10 px-2.5 w-full h-45 max-tablet:h-36 max-phone:h-30 grid grid-cols-[125px_1fr] max-tablet:grid-cols-[100px_1fr] max-phone:grid-cols-[80px_1fr] gap-2.5 text-off-white">
                            <div class="absolute bottom-0 w-full h-35 max-tablet:h-30 max-phone:h-25 rounded-xl bg-neutral-100 group-hover:border ${border} drop-shadow-lg z-1"></div>
                            <div class="absolute bottom-0 w-full h-35 max-tablet:h-30 max-phone:h-25 rounded-xl bg-black/40 z-2 hidden"></div>
                            <button type="submit" name="view" value="${item.thesis_id}" class="relative z-3">
                                <img src="resources/${(item.course == "BSCS-AD" ? "book-cs" : item.course == "BSIT-NS" ? "book-it" : "book-others") + getBM(item.thesis_id)}.svg" alt="" class="absolute top-0 cursor-pointer select-none hover:-translate-y-2 duration-200 drop-shadow-md">
                            </button>
                            <div class="relative pt-12.5 max-tablet:pt-7.5 flex flex-col items-start gap-1 max-phone:gap-0.5 font-semibold max-tablet:text-sm max-phone:text-xs max-phone:leading-3 leading-4 z-3">
                                <h3 class="${textColor} font-bold">${item.title}</h3>
                                <ul class="flex flex-wrap gap-0.5 max-big-phone:hidden text-xs text-dirty-brown">
                                    <b>Authors:</b>
                                    ${item.authors .split("-").map(author => author.trim()).map((author, index, arr) => `<li>${author}${index < arr.length - 1 ? ',' : ''}</li>`).join("")}
                                </ul>
                                <ul class="absolute bottom-2.5 right-0 font-normal flex items-center gap-1 text-sm max-tablet:text-xs">
                                    ${item.abstract?.trim() == "" ? '<li class="px-2 rounded-full bg-red-900 text-xs drop-shadow-lg select-none">No Info</li>' : ''}
                                    ${item.visits != 0 ? '<li class="px-2 rounded-full bg-dirty-brown text-xs select-none drop-shadow-lg">' + item.visits + ' visit/s</li>' : ''}
                                    <li class="text-dirty-brown">${formatDate(item.published_date)}</li>
                                </ul>
                            </div>
                        </form>
                    `;
                } else {
                    return `
                        <form class="relative w-full px-5 p-2.5 h-25 max-big-phone:h-27.5 rounded-xl shadow-lg bg-neutral-100 border border-neutral-300 hover:border-dirty-brown/70 flex gap-2.5">
                            <button type="submit" name="view" value="${item.thesis_id}" class="absolute top-2.5 right-5 z-1"><svg class="size-5 cursor-pointer" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor"><path d="M120-120v-240h80v104l124-124 56 56-124 124h104v80H120Zm480 0v-80h104L580-324l56-56 124 124v-104h80v240H600ZM324-580 200-704v104h-80v-240h240v80H256l124 124-56 56Zm312 0-56-56 124-124H600v-80h240v240h-80v-104L636-580Z"/></svg></button>
                            <div class="relative flex-1 flex flex-col items-start justify-center select-text">
                                <h3 class="font-bold text-lg leading-4 max-tablet:text-base max-big-phone:text-sm max-big-phone:leading-3 ${textColor}">${item.title} ${item.course.trim() !== "BSCS-AD" && item.course.trim() !== "BSIT-NS" ? `<i class="max-big-phone:mb-0.5 px-2 rounded-full bg-yellow-800/80 text-xs text-off-white select-none">${item.course}</i>` : ""}</h3>
                                <i class="leading-none text-sm max-tablet:text-xs max-big-phone:hidden">${item.authors.split("-").map(author => author.replace("-", " "))}</i>
                                <div class="mt-0.5 flex items-center gap-2 text-off-white text-sm max-big-phone:text-xs">
                                    ${item.abstract?.trim() == "" ? '<span class="px-2 rounded-full bg-red-900 drop-shadow-lg select-none">No Info</span>' : ''}
                                    <p class="px-2 py-0.5 rounded-full ${singularColor} leading-none">${formatDate(item.published_date)}</p>
                                </div>
                                ${item.visits != 0 ? '<span class="absolute bottom-0 right-0 px-2 bg-dirty-brown text-off-white rounded-full text-xs select-none">' + item.visits + ' visit/s</span>' : ''}
                            </div>
                        </form>
                    `;
                }
            }).join("");

            if (pageInfo) pageInfo.textContent = totalSets != 0 ? `Showing ${selectedSet + 1} of ${totalSets} set/s of results` : "No results found";
            tsetsSelect.disabled = totalSets == 0;
            if (hasCookie('view')) {
                const thesisId = getCookie('view');
                const thesis = data.theses.find(item => item.thesis_id == thesisId);
                if (thesis) {
                    view(thesisId);
                }
            }
        }

        function view(thesis_id) {
            const fullView = document.getElementById("full-view");
            const library = document.getElementById("library");
            if (!thesis_id) {
                fullView.classList.add("hidden");
                library.classList.replace('hidden', 'flex');
                document.cookie = 'view=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
                history.replaceState(null, '', location.pathname);
                return;
            }
            fullView.classList.remove("hidden");
            library.classList.replace('flex', 'hidden');

            setCookie('view', thesis_id);
            
            const banner = document.getElementById("banner");
            const information = document.getElementById("information");

            let item = data.theses.find(item => item.thesis_id == thesis_id);
            let account = data.accounts.find(account => account.user_id == getCookie('id'));
        
            let a = item.authors.split("-").map(x => {
                x = x.trim();
                if (x === 'et al.') return x;

                if (x.includes(',')) { // Format: Lastname, Firstname
                    let [last, first] = x.split(',').map(s => s.trim());
                    return `${last} ${first[0]}.`;
                } else { // Format: Firstname Lastname
                    let parts = x.split(' ');
                    let lastName = parts.slice(-1).join('');
                    return `${lastName} ${parts[0][0]}.`;
                }
            });

            let citationAuthors = a.length > 1 ? a.slice(0, -1).join(", ") + " & " + a.at(-1) : a[0];
            const userBookmarks = (data.accounts.find(u => u.user_id === getCookie('id'))?.bookmarks || '').split('-');
            const getBM = thesisId => userBookmarks.includes(String(thesisId)) ? '-bm' : '';

            let darkColor = item.course == "BSCS-AD" ? "bg-violet-900" : item.course == "BSIT-NS" ? "bg-sky-900" : "bg-yellow-800";
            let textColor = item.course == "BSCS-AD" ? "text-violet-950" : item.course == "BSIT-NS" ? "text-sky-950" : "text-yellow-950";                
            let gradient = item.course == "BSCS-AD" ? "from-violet-900 via-purple-400 to-violet-700" : item.course == "BSIT-NS" ? "from-sky-900 via-sky-400 to-sky-800" : "from-yellow-800 via-yellow-500 to-amber-700";

            banner.className = `relative w-full h-100 max-tablet:h-65 max-big-phone:h-45 rounded-3xl max-tablet:rounded-none bg-linear-135/oklch ${gradient} flex gap-10 max-tablet:gap-2.5`;
            banner.innerHTML = `
                <div class="absolute top-0 size-full rounded-3xl max-tablet:rounded-none bg-black/25 z-1"></div>
                <button onclick="view()" class="relative ml-10 max-big-phone:ml-5 w-80 max-tablet:w-50 max-big-phone:w-30 max-phone:w-25 z-2">
                    <img src="resources/${(item.course == "BSCS-AD" ? "book-cs" : item.course == "BSIT-NS" ? "book-it" : "book-others") + getBM(item.thesis_id)}.svg" alt="" class="absolute top-10 max-phone:top-1/2 max-phone:-translate-y-1/2 phone:hover:-translate-y-5 duration-300 drop-shadow-xl select-none cursor-pointer">
                </button>
                <div class="relative flex-1 mr-10 max-big-phone:mr-5 h-full flex flex-col justify-center text-off-white z-2">
                    <p class="italic text-xl max-tablet:text-base max-big-phone:text-xs leading-5 max-tablet:leading-4 max-big-phone:leading-3 font-semibold ${textColor}">${item.course == "BSCS-AD" ? "Bachelor of Science in Computer Science" : item.course == "BSIT-NS" ? "Bachelor of Science in Information Technology" : item.course == "BSCNA" ? "Bachelor of Science in Computer Network Administration" : item.course}</p>
                    <h1 class="font-bold text-3xl max-tablet:text-lg max-big-phone:text-sm leading-7 max-tablet:leading-5 max-big-phone:leading-3.5">${item.title}</h1>
                    <ul class="mt-1 flex max-phone:hidden flex-wrap gap-1 max-big-phone:gap-0.5 text-sm max-tablet:text-xs *:px-2 *:rounded-full *:${darkColor}">
                        Authors:
                        ${item.authors.split("-").map(author => `<li class="drop-shadow-lg">${author.trim()}</li>`).join("")}
                    </ul>
                    <p class="absolute bottom-5 -right-2.5 max-big-phone:bottom-2.5 max-tablet:text-sm max-big-phone:text-xs">${item.visits != 0 ? item.visits + ' visit/s ' : ''}<span class="px-2 rounded-full font-semibold drop-shadow-lg ${darkColor}">${formatDate(item.published_date)}</span></p>
                    <form action="bookmark.php" method="post" id="bm-form" class="absolute top-5 -right-2.5 max-big-phone:top-2.5 max-big-phone:-right-2.5 *:select-none max-tablet:scale-80 max-big-phone:scale-60">
                        <input type="text" name="thesis_id" id="thesis_id" value="${item.thesis_id}" hidden>
                        <input type="text" name="user_id" id="user_id" value="${getCookie('id')}" hidden>
                        <button type="submit" name="bm" value="none" class="cursor-pointer">
                            ${account.bookmarks?.split('-').includes(item.thesis_id) ?
                            '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"><!-- Icon from MingCute Icon by MingCute Design - https://github.com/Richard9394/MingCute/blob/main/LICENSE --><g fill="none"><path d="m12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035q-.016-.005-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093q.019.005.029-.008l.004-.014l-.034-.614q-.005-.018-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z"/><path fill="#eeeeee" d="M4 5a3 3 0 0 1 3-3h10a3 3 0 0 1 3 3v16.028c0 1.22-1.38 1.93-2.372 1.221L12 18.229l-5.628 4.02c-.993.71-2.372 0-2.372-1.22z"/></g></svg>'
                            :
                            '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"><!-- Icon from MingCute Icon by MingCute Design - https://github.com/Richard9394/MingCute/blob/main/LICENSE --><g fill="none" fill-rule="evenodd"><path d="m12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035q-.016-.005-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093q.019.005.029-.008l.004-.014l-.034-.614q-.005-.018-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z"/><path fill="#eeeeee" d="M4 5a3 3 0 0 1 3-3h10a3 3 0 0 1 3 3v16.028c0 1.22-1.38 1.93-2.372 1.221L12 18.229l-5.628 4.02c-.993.71-2.372 0-2.372-1.22zm3-1a1 1 0 0 0-1 1v15.057l5.128-3.663a1.5 1.5 0 0 1 1.744 0L18 20.057V5a1 1 0 0 0-1-1z"/></g></svg>'
                            }                          
                        </button>
                    </form>
                </div>
            `;

            information.innerHTML = item.abstract?.trim() == "" ? "<div class='text-center'>No information available.</div>" : `
                <ul class="mt-1 flex phone:hidden flex-wrap gap-1 font-bold text-xs *:px-2 *:rounded-full *:${darkColor}">
                    Authors:
                    ${item.authors.split("-").map(author => `<li class="font-normal text-off-white">${author.trim()}</li>`).join("")}
                </ul>
                <div>
                    <h2 class="border-l-4 pl-2.5 font-bold text-2xl max-tablet:text-xl max-big-phone:text-lg">Abstract</h2>
                    <p class="mt-2.5 text-justify text-lg max-tablet:text-base max-big-phone:text-sm leading-none">
                        ${item.abstract}
                    </p>
                </div>
                <div>
                    <h2 class="border-l-4 pl-2.5 font-bold text-2xl max-tablet:text-xl max-big-phone:text-lg">Keywords</h2>
                    <p class="mt-2.5 text-justify text-lg max-tablet:text-base max-big-phone:text-sm leading-none">
                        ${item.keywords}
                    </p>
                </div>
                <div>
                    <h2 class="border-l-4 pl-2.5 font-bold text-2xl max-tablet:text-xl max-big-phone:text-lg">Citation</h2>
                    <div class="relative mt-2.5 p-5 bg-neutral-200 border border-neutral-400 rounded-xl text-lg max-tablet:text-sm leading-5 max-tablet:leading-4 max-phone:*:break-all">
                        <div id="citation" class="pl-10 pr-5 -indent-10 font-serif max-big-phone:text-sm">
                            ${citationAuthors} (${item.published_date.substring(0, 4)}). <i>${item.title}</i> [Unpublished thesis]
                        </div>
                        <button onclick="copybm('${item.thesis_id}')" class="absolute top-2.5 right-2.5 cursor-pointer active:scale-90 duration-200 hover:opacity-60 outline-none">
                            <svg class="size-5 max-tablet:size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><!-- Icon from Material Symbols by Google - https://github.com/google/material-design-icons/blob/master/LICENSE --><path fill="#585345" d="M9 18q-.825 0-1.412-.587T7 16V4q0-.825.588-1.412T9 2h9q.825 0 1.413.588T20 4v12q0 .825-.587 1.413T18 18zm-4 4q-.825 0-1.412-.587T3 20V6h2v14h11v2z"/></svg>
                        </button>
                    </div>
                </div>
            `;
        }

        function copybm(thesisId) {
            const citationBox = document.getElementById('citation');
            if (citationBox) {
                const text = citationBox.textContent.trim();

                navigator.clipboard.writeText(text).then(() => {
                    alert("Citation copied to clipboard!");
                }).catch(err => {
                    console.error("Failed to copy citation:", err);
                });
            }
        }


        const hasCookie = n => document.cookie.split('; ').some(c => c.startsWith(n + '='));

        function setCookie(name, value, days = 1) {
            const d = new Date();
            d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
            const expires = "expires=" + d.toUTCString();
            document.cookie = `${name}=${value}; ${expires}; path=/`;
        }

        function getCookie(name) {
            const decoded = decodeURIComponent(document.cookie);
            const cookies = decoded.split(';');
            for (let c of cookies) {
                c = c.trim();
                if (c.indexOf(name + "=") === 0) {
                return c.substring(name.length + 1);
                }
            }
            return null;
        }
        
        const formatDate = date => new Date(date.replace(/(\d{4})-(\d{2})/, '$1-$2-01')).toLocaleString('default', { month: 'long', year: 'numeric' });

        function toggleFlex(id) {
            document.getElementById(id).classList.toggle("hidden");
            document.getElementById(id).classList.toggle("flex");
        }

        const spotifyButton = document.getElementById('spotify-button');
        const spotify = document.getElementById('spotify');
        const cookieSpotify = getCookie('spotify');

        const hidden = cookieSpotify === 'hidden';
        spotify.classList.toggle('hidden', hidden);
        spotify.classList.toggle('flex', !hidden);

        spotifyButton.classList.toggle('bg-dirty-brown', !hidden);
        spotifyButton.classList.toggle('text-off-white', !hidden);

        function toggleSpotify() {
            const nowHidden = spotify.classList.toggle('hidden');
            spotify.classList.toggle('flex', !nowHidden);
            spotifyButton.classList.toggle('bg-dirty-brown', !nowHidden);
            spotifyButton.classList.toggle('text-off-white', !nowHidden);
            setCookie('spotify', nowHidden ? 'hidden' : '');
        }

    </script>
</body>

</html>