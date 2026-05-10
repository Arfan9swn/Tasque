<?php

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-800 text-white min-h-screen">

    <?php
        include "./component/navbar.component.php";
        echo NavBar();
    ?>

    <div class="w-full flex justify-center px-[5%]">
        <div class="w-full max-w-[520px] border border-slate-700 rounded-[8px] bg-slate-700/40 mt-[40px] p-[20px]">
            <div class="flex flex-col gap-2 mb-[15px]">
                <div class="font-bold text-[36px] leading-none">Tasque</div>
                <div class="text-slate-200">Masuk untuk melanjutkan</div>
            </div>

            <form action="" method="post" class="flex flex-col gap-[12px]">
                <div class="flex flex-col gap-2">
                    <label for="username" class="text-sm text-slate-200">Username</label>
                    <input
                        id="username"
                        name="username"
                        type="text"
                        class="w-full rounded-[6px] bg-slate-800 border border-slate-600 px-[12px] py-[10px] focus:outline-none focus:ring-2 focus:ring-slate-400"
                        required
                    >
                </div>

                <div class="flex flex-col gap-2">
                    <label for="password" class="text-sm text-slate-200">Password</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="w-full rounded-[6px] bg-slate-800 border border-slate-600 px-[12px] py-[10px] focus:outline-none focus:ring-2 focus:ring-slate-400"
                        required
                    >
                </div>

                <button
                    type="submit"
                    class="mt-[8px] w-full rounded-[6px] bg-slate-600 hover:bg-slate-500 transition-colors px-[14px] py-[10px] font-semibold"
                >
                    Log in
                </button>
            </form>

            <div class="mt-[14px] text-center text-sm">
                <span class="text-slate-200">Belum punya akun? </span>
                <a href="./signin.php" class="text-slate-200 hover:text-white underline underline-offset-2">Daftar</a>
            </div>
        </div>
    </div>

    <?php 
        include "./component/footer.component.php";
        echo Footer();
    ?>

</body>
</html>

