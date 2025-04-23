<?php
if (isset($_COOKIE['id'])) {
    header("Location: profile.php");
    exit();
}

session_start();
$activeForm = $_SESSION['activeForm'] ?? 'loginform';

function activeForm($form, $activeForm) {
    return $form === $activeForm ? "flex" : "hidden";
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
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link href="./output.css" rel="stylesheet">
</head>
<body class="bg-neutral-950 max-big-phone:bg-neutral-900 font-nunito text-white flex">
    <!-- ================================================== MAIN ================================================== -->
    <main class="relative z-1 w-full h-screen flex items-center justify-center">
        <?php
            $error = $_SESSION['log-error'] ?? $_SESSION['reg-error'] ?? '';
            $success = $_SESSION['fpw-success'] ?? '';

            if ($success) {
                echo "<div class='absolute top-5 left-1/2 -translate-x-1/2 p-2 w-100 h-10 rounded-xl bg-green-900 select-none z-5 animate-downfadeinout'>" . $success . "</div>";
            }
            if ($error) {
                echo "<div class='absolute top-5 left-1/2 -translate-x-1/2 p-2 w-100 h-10 rounded-xl bg-[#7f1d1d] select-none z-5 animate-downfadeinout'>" . $error . "</div>";
            }

            session_unset();
        ?>
        <div class="absolute top-5 max-big-phone:top-2.5 left-1/2 -translate-x-1/2 flex flex-col gap-2 **:select-none **:leading-none max-tablet:scale-85 z-2 **:whitespace-nowrap text-off-white max-big-phone:text-sm">
            <span class="flex gap-1 items-center justify-center *:size-10">
                <img src="resources/umak.svg" alt="">
                <img src="resources/ccis.svg" alt="">
                <img src="resources/sf-logo.svg" alt="">
            </span>
            <span class="flex flex-col items-center text-center">
                <h1>University of Makati</h1>
                <h2>College of Computing and Information Sciences</h2>
                <h3>Scholar Finds</h3>
            </span>
        </div>
        <div class="relative z-1 p-10 py-12.5 max-big-phone:p-5 w-120 big-phone:rounded-tl-4xl rounded-br-4xl bg-neutral-900 big-phone:border-2 border-neutral-800 max-big-phone:drop-shadow-none drop-shadow-[0_0_10px_rgb(0,0,0,0.5)] drop-shadow-black overflow-clip duration-100 max-tablet:scale-85">
            <div class="absolute -right-45 -top-45 size-75 bg-radial-[closest-side] from-off-white/10 max-big-phone:hidden"></div>
    
            <!-- LOGIN -->
            <form action="login.php" method="post" id="login" class="<?php echo activeForm('loginform', $activeForm);?> flex-col gap-10 *:relative animate-fadeIn">
                <div class="flex flex-col font-semibold select-none">
                    <h1 class="text-3xl">Log In</h1>
                    <p class="text-neutral-600 text-sm">Login to start browsing</p>
                </div>
                <div class="flex flex-col gap-3">
                    <span>
                        <label for="lemail" class="pl-2 text-neutral-400 select-none">Email Address</label>
                        <input type="email" name="lemail" id="lemail" class="w-full px-2.5 py-1 rounded-lg bg-neutral-800 border-1 border-neutral-700 shadow-[0_2px_10px_rgb(0,0,0,0.5)] caret-neutral-500 outline-none" required>
                    </span>
                    <span>
                        <label for="lpassword" class="pl-2 text-neutral-400 select-none">Password</label>
                        <span class="relative">
                            <input type="password" name="lpassword" id="lpassword" class="w-full px-2.5 py-1 rounded-lg bg-neutral-800 border-1 border-neutral-700 shadow-[0_2px_10px_rgb(0,0,0,0.5)] caret-neutral-500 outline-none" required>
                            <button onclick="togglePw('l')" type="button" class="absolute top-1/2 -translate-y-1/2 right-2.5 text-neutral-400 hover:text-neutral-200 duration-200 cursor-pointer">
                                <!-- OPEN --> <svg id="lopen" class="size-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><!-- Icon from MingCute Icon by MingCute Design - https://github.com/Richard9394/MingCute/blob/main/LICENSE --><g fill="none"><path d="m12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035q-.016-.005-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093q.019.005.029-.008l.004-.014l-.034-.614q-.005-.018-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z"/><path fill="currentColor" d="M12 5c3.679 0 8.162 2.417 9.73 5.901c.146.328.27.71.27 1.099c0 .388-.123.771-.27 1.099C20.161 16.583 15.678 19 12 19s-8.162-2.417-9.73-5.901C2.124 12.77 2 12.389 2 12c0-.388.123-.771.27-1.099C3.839 7.417 8.322 5 12 5m0 3a4 4 0 1 0 0 8a4 4 0 0 0 0-8m0 2a2 2 0 1 1 0 4a2 2 0 0 1 0-4"/></g></svg>
                                <!-- CLOSE --> <svg id="lclose" class="size-6 hidden" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><!-- Icon from MingCute Icon by MingCute Design - https://github.com/Richard9394/MingCute/blob/main/LICENSE --><g fill="none"><path d="m12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035q-.016-.005-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093q.019.005.029-.008l.004-.014l-.034-.614q-.005-.018-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z"/><path fill="currentColor" d="M3.05 9.31a1 1 0 1 1 1.914-.577c2.086 6.986 11.982 6.987 14.07.004a1 1 0 1 1 1.918.57a9.5 9.5 0 0 1-1.813 3.417L20.414 14A1 1 0 0 1 19 15.414l-1.311-1.311a9.1 9.1 0 0 1-2.32 1.269l.357 1.335a1 1 0 1 1-1.931.518l-.364-1.357c-.947.14-1.915.14-2.862 0l-.364 1.357a1 1 0 1 1-1.931-.518l.357-1.335a9.1 9.1 0 0 1-2.32-1.27l-1.31 1.312A1 1 0 0 1 3.585 14l1.275-1.275c-.784-.936-1.41-2.074-1.812-3.414Z"/></g></svg>
                            </button>
                        </span>
                        <p onclick="window.location.href = 'fpw.php'" class="py-1 text-right text-sm text-neutral-600 hover:text-off-white duration-200 select-none cursor-pointer">Forgotten your password?</p>
                    </span>
                    <span class="pt-5 flex flex-col gap-2 **:select-none">
                        <button type="submit" name="login" class="w-full px-2.5 py-1 rounded-lg bg-green-900 border-1 border-green-950 shadow-[0_2px_10px_rgb(0,0,0,0.5)] outline-none hover:bg-green-800 transition-all duration-200 cursor-pointer">Login</button>
                        <p class="text-neutral-600 font-semibold text-sm">Don't have an account? <a onclick="toggleFlex2('login', 'register')" class="text-green-700 hover:text-green-600 duration-200 font-semibold cursor-pointer">Register</a></p>             
                    </span>
                </div>
            </form>

            <!-- REGISTER -->
            <form action="login.php" method="post" id="register" class="<?php echo activeForm('regisform', $activeForm);?> flex-col gap-5 *:relative animate-fadeIn">
                <div class="flex flex-col font-semibold select-none">
                    <h1 class="text-3xl">Register</h1>
                    <p class="text-neutral-600 text-sm">Create an account to start browsing</p>
                </div>
                <div class="flex flex-col gap-3">
                    <span>
                        <label for="rname" class="pl-2 text-neutral-400 select-none">Name</label>
                        <input type="text" name="rname" id="rname" class="w-full px-2.5 py-1 rounded-lg bg-neutral-800 border-1 border-neutral-700 shadow-[0_2px_10px_rgb(0,0,0,0.5)] caret-neutral-500 outline-none" required>
                    </span>
                    <span>
                        <label for="rusername" class="pl-2 text-neutral-400 select-none">Username</label>
                        <input type="text" name="rusername" id="rusername" class="w-full px-2.5 py-1 rounded-lg bg-neutral-800 border-1 border-neutral-700 shadow-[0_2px_10px_rgb(0,0,0,0.5)] caret-neutral-500 outline-none" required>
                    </span>
                    <span>
                        <label for="remail" class="pl-2 text-neutral-400 select-none">Email Address</label>
                        <input type="email" name="remail" id="remail" class="w-full px-2.5 py-1 rounded-lg bg-neutral-800 border-1 border-neutral-700 shadow-[0_2px_10px_rgb(0,0,0,0.5)] caret-neutral-500 outline-none" required>
                    </span>
                    <span>
                        <p class="pl-2 text-neutral-400 select-none">Membership</p>
                        <span class="w-full flex gap-2.5 text-center text-sm *:outline-none select-none *:cursor-pointer">
                            <input type="radio" name="membership" value="student" id="student" class="peer/student" checked hidden>
                            <input type="radio" name="membership" value="alumni" id="alumni" class="peer/alumni" hidden>
                            <input type="radio" name="membership" value="guest" id="guest" class="peer/guest" hidden>
                            <label for="student" class="flex-1 px-2.5 py-2 rounded-lg bg-neutral-800 peer-checked/student:bg-green-900 border-1 border-neutral-700 shadow-[0_2px_10px_rgb(0,0,0,0.5)] text-neutral-400 peer-checked/student:text-off-white duration-200">Student</label>
                            <label for="alumni" class="flex-1 px-2.5 py-2 rounded-lg bg-neutral-800 peer-checked/alumni:bg-green-900 border-1 border-neutral-700 shadow-[0_2px_10px_rgb(0,0,0,0.5)] text-neutral-400 peer-checked/alumni:text-off-white duration-200">Alumni</label>
                            <label for="guest" class="flex-1 px-2.5 py-2 rounded-lg bg-neutral-800 peer-checked/guest:bg-green-900 border-1 border-neutral-700 shadow-[0_2px_10px_rgb(0,0,0,0.5)] text-neutral-400 peer-checked/guest:text-off-white duration-200">Guest</label>
                        </span>
                    </span>
                    <span>
                        <label for="rpassword" class="pl-2 text-neutral-400 select-none">Password</label>
                        <span class="relative flex gap-1">
                            <span class="flex-1 relative">
                                <input type="password" name="rpassword" id="rpassword" class="w-full px-2.5 py-1 rounded-lg bg-neutral-800 border-1 border-neutral-700 shadow-[0_2px_10px_rgb(0,0,0,0.5)] caret-neutral-500 outline-none" required>
                                <button onclick="togglePw('r')" type="button" class="absolute top-1/2 -translate-y-1/2 right-2.5 text-neutral-400 hover:text-neutral-200 duration-200 cursor-pointer">
                                    <!-- OPEN --> <svg id="ropen" class="size-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><!-- Icon from MingCute Icon by MingCute Design - https://github.com/Richard9394/MingCute/blob/main/LICENSE --><g fill="none"><path d="m12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035q-.016-.005-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093q.019.005.029-.008l.004-.014l-.034-.614q-.005-.018-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z"/><path fill="currentColor" d="M12 5c3.679 0 8.162 2.417 9.73 5.901c.146.328.27.71.27 1.099c0 .388-.123.771-.27 1.099C20.161 16.583 15.678 19 12 19s-8.162-2.417-9.73-5.901C2.124 12.77 2 12.389 2 12c0-.388.123-.771.27-1.099C3.839 7.417 8.322 5 12 5m0 3a4 4 0 1 0 0 8a4 4 0 0 0 0-8m0 2a2 2 0 1 1 0 4a2 2 0 0 1 0-4"/></g></svg>
                                    <!-- CLOSE  --> <svg id="rclose" class="size-6 hidden" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><!-- Icon from MingCute Icon by MingCute Design - https://github.com/Richard9394/MingCute/blob/main/LICENSE --><g fill="none"><path d="m12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035q-.016-.005-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093q.019.005.029-.008l.004-.014l-.034-.614q-.005-.018-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z"/><path fill="currentColor" d="M3.05 9.31a1 1 0 1 1 1.914-.577c2.086 6.986 11.982 6.987 14.07.004a1 1 0 1 1 1.918.57a9.5 9.5 0 0 1-1.813 3.417L20.414 14A1 1 0 0 1 19 15.414l-1.311-1.311a9.1 9.1 0 0 1-2.32 1.269l.357 1.335a1 1 0 1 1-1.931.518l-.364-1.357c-.947.14-1.915.14-2.862 0l-.364 1.357a1 1 0 1 1-1.931-.518l.357-1.335a9.1 9.1 0 0 1-2.32-1.27l-1.31 1.312A1 1 0 0 1 3.585 14l1.275-1.275c-.784-.936-1.41-2.074-1.812-3.414Z"/></g></svg>
                                </button>
                            </span>
                            <svg id="shield" class="p-0.5 size-8 text-neutral-400 drop-shadow-[0_2px_10px_rgb(0,0,0,0.5)] peer" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><!-- Icon from MingCute Icon by MingCute Design - https://github.com/Richard9394/MingCute/blob/main/LICENSE --><g fill="none" fill-rule="evenodd"><path d="m12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035q-.016-.005-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093q.019.005.029-.008l.004-.014l-.034-.614q-.005-.018-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z"/><path fill="currentColor" d="M11.298 2.195a2 2 0 0 1 1.404 0l7 2.625A2 2 0 0 1 21 6.693v5.363a9 9 0 0 1-4.975 8.05l-3.354 1.677a1.5 1.5 0 0 1-1.342 0l-3.354-1.677A9 9 0 0 1 3 12.056V6.693A2 2 0 0 1 4.298 4.82z"/></g></svg>
                            <div class="absolute top-0 translate-y-10 right-0 hidden p-2 rounded-lg bg-neutral-950 border border-neutral-900 peer-hover:flex flex-col text-sm z-2">
                                <input type="text" name="secure-level" id="secure-level" hidden>
                                <div class="flex flex-col items-center font-light">
                                    <p>Your password is <span id="security-msg" class="font-bold text-red-800">WEAK</span>.</p>
                                    <ul class="list-disc *:ml-5">
                                        To stregthen your password, consider:
                                        <li id="length-pattern">Having more than 8 characters</li>
                                        <li id="uppercase-pattern">Having at least 1 uppercase</li>
                                        <li id="alphanumeric-pattern">Having at least 1 number or special characters</li>
                                    </ul>
                                </div>
                            </div>
                        </span>
                    </span>                    
                    <span class="flex flex-col items-center gap-2 **:select-none">
                        <div class="g-recaptcha scale-70" data-sitekey="6LdD-hkrAAAAAAXPIS8-nGgCyL_pEsnwT5BoHs3V" data-theme="dark"></div>
                        <button type="submit" name="register" class="w-full px-2.5 py-1 rounded-lg bg-green-900 border-1 border-green-950 shadow-[0_2px_10px_rgb(0,0,0,0.5)] outline-none hover:bg-green-800 transition-all duration-200 cursor-pointer">Register</button>
                        <p class="text-left text-neutral-600 font-semibold text-sm">Already have an account? <a onclick="toggleFlex2('login', 'register')" class="text-green-700 hover:text-green-600 duration-200 font-semibold cursor-pointer">Login</a></p>             
                    </span>
                </div>
            </form>

            <script>
                if (document.cookie.includes('gotoform=')) {
                    document.cookie = "gotoform=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                    toggleFlex2('login', 'register');
                }

                function toggleFlex2(id, id2) {
                    document.getElementById(id).classList.toggle('hidden');
                    document.getElementById(id).classList.toggle('flex');
                    document.getElementById(id2).classList.toggle('hidden');
                    document.getElementById(id2).classList.toggle('flex');
                }

                function togglePw(mode) {
                    document.getElementById(mode + 'open').classList.toggle('hidden');
                    document.getElementById(mode + 'close').classList.toggle('hidden');
                    document.getElementById(mode + 'password').type = document.getElementById(mode + 'password').type === 'password' ? 'text' : 'password';
                }

                const rpw = document.getElementById('rpassword');
                const shield = document.getElementById('shield');
                const securityMsg = document.getElementById('security-msg');

                let lengthPattern = /^.{8,}$/;
                let alphanumericPattern = /^(?=.*[\d\W]).+$/;
                let uppercasePattern = /[A-Z]/;

                rpw.addEventListener('input', () => {
                    let secureLevel = 0;
                    lengthPattern.test(rpw.value) ? ++secureLevel : '';
                    document.getElementById('length-pattern').classList.toggle('line-through', lengthPattern.test(rpw.value));
                    document.getElementById('length-pattern').classList.toggle('text-neutral-400', lengthPattern.test(rpw.value));
                    alphanumericPattern.test(rpw.value) ? ++secureLevel : '';
                    document.getElementById('alphanumeric-pattern').classList.toggle('line-through', alphanumericPattern.test(rpw.value));
                    document.getElementById('alphanumeric-pattern').classList.toggle('text-neutral-400', alphanumericPattern.test(rpw.value));
                    uppercasePattern.test(rpw.value) ? ++secureLevel : '';
                    document.getElementById('uppercase-pattern').classList.toggle('line-through', uppercasePattern.test(rpw.value));
                    document.getElementById('uppercase-pattern').classList.toggle('text-neutral-400', uppercasePattern.test(rpw.value));

                    shield.classList.remove('text-neutral-400', 'text-red-900', 'text-yellow-600', 'text-green-800')
                    shield.classList.add(rpw.value.length === 0 ? 'text-neutral-400' : secureLevel <= 1 ? 'text-red-900' : secureLevel === 2 ? 'text-yellow-600' : 'text-green-800');

                    securityMsg.classList.remove('text-red-800', 'text-yellow-500', 'text-green-700')
                    securityMsg.classList.add(secureLevel <= 1 ? 'text-red-800' : secureLevel === 2 ? 'text-yellow-500' : 'text-green-700');
                    securityMsg.innerText = secureLevel <= 1 ? 'WEAK' : secureLevel === 2 ? 'MEDIUM' : 'STRONG';
                    document.getElementById('secure-level').value = secureLevel;
                });

            </script>
        </div>
    </main>
</body>
</html>