<?php

    require_once "./dbconn.php";
    $conn = dbConnect();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in</title>
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
                <div class="text-slate-200">Buat akun baru</div>
            </div>

<?php

require_once "./dbconn.php";
require_once "./auth.php";

$err = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $err = 'Username dan password wajib diisi.';
    } else {
        $conn = dbConnect();

        $usernameEsc = $conn->real_escape_string($username);
        $hash = md5($password);
        $hashEsc = $conn->real_escape_string($hash);
        $nameEsc = $conn->real_escape_string($fullName);

        $check = $conn->query("SELECT user_id FROM user WHERE username = '$usernameEsc' LIMIT 1");
        if ($check && $check->num_rows > 0) {
            $err = 'Username sudah digunakan.';
        } else {
            $insert = $conn->query("INSERT INTO user (username, password, name) VALUES ('$usernameEsc', '$hashEsc', '$nameEsc')");
            if ($insert) {
                header('Location: ./login.php');
                exit;
            }
            $err = 'Gagal membuat akun.';
        }
    }
}

?>

            <form action="" method="post" class="flex flex-col gap-[12px]">
                <?php if ($err): ?>
                    <div class="rounded-[8px] border border-red-700 bg-red-900/20 text-red-200 px-[12px] py-[10px] text-sm">
                        <?= htmlspecialchars($err) ?>
                    </div>
                <?php endif; ?>

                <div class="flex flex-col gap-2">
                    <label for="name" class="text-sm text-slate-200">Nama</label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        class="w-full rounded-[6px] bg-slate-800 border border-slate-600 px-[12px] py-[10px] focus:outline-none focus:ring-2 focus:ring-slate-400"
                        required
                    >
                </div>

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
                    Daftar
                </button>
            </form>


            <div class="mt-[14px] text-center text-sm">
                <span class="text-slate-200">Sudah punya akun? </span>
                <a href="./login.php" class="text-slate-200 hover:text-white underline underline-offset-2">Masuk</a>
            </div>
        </div>
    </div>

    <?php 
        include "./component/footer.component.php";
        echo Footer();
    ?>

</body>
</html>

