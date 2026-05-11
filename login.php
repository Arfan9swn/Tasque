<?php


// * jangan dihapus
    require_once "./dbconn.php";
    $conn = dbConnect();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<?php

require_once "./dbconn.php";
require_once "./auth.php";

$err = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $err = 'Username dan password wajib diisi.';
    } else {
        $conn = dbConnect();

        $usernameEsc = $conn->real_escape_string($username);
        $hash = md5($password);
        $hashEsc = $conn->real_escape_string($hash);

        $res = $conn->query("SELECT user_id, username FROM user WHERE username = '$usernameEsc' AND password = '$hashEsc' LIMIT 1");
        if ($res && $res->num_rows === 1) {
            $row = $res->fetch_assoc();
            $_SESSION['user_id'] = (int)$row['user_id'];
            $_SESSION['username'] = (string)$row['username'];
            header('Location: ./index.php');
            exit;
        }

        $err = 'Username atau password salah.';
    }
}

?>
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
                <?php if ($err): ?>
                    <div class="rounded-[8px] border border-red-700 bg-red-900/20 text-red-200 px-[12px] py-[10px] text-sm">
                        <?= htmlspecialchars($err) ?>
                    </div>
                <?php endif; ?>

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

