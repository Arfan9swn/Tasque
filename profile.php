<?php

require_once "./dbconn.php";
require_once "./auth.php";

require_login();

$conn = dbConnect();
$userId = current_user_id();

// Fetch current user
$user = null;
if ($userId !== null) {
    $userIdEsc = (int)$userId;
    $res = $conn->query("SELECT * FROM user WHERE user_id = $userIdEsc LIMIT 1");
    if ($res && $res->num_rows === 1) {
        $user = $res->fetch_assoc();
    }
}

$username = $user['username'] ?? (current_username() ?? '');
$name = $user['name'] ?? (current_name() ?? '');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-800 text-white min-h-screen">

    <?php
        include "./component/navbar.component.php";
        echo NavBar();
    ?>

    <div class="w-full flex justify-center px-[5%]">
        <div class="w-full max-w-[860px] mt-[40px]">
            <div class="border border-slate-700 rounded-[8px] bg-slate-700/40 p-[20px]">
                <div class="flex items-start gap-[18px]">

                    <div class="flex flex-col gap-[6px] flex-1">
                        <div class="text-[26px] font-bold leading-none"><?php echo htmlspecialchars($name); ?></div>
                        <div class="text-slate-200">@<?php echo htmlspecialchars($username); ?></div>
                    </div>
                </div>

                <div class="mt-[18px] grid grid-cols-1 sm:grid-cols-3 gap-[14px]">
                    <div class="rounded-[10px] bg-slate-800/40 border border-slate-700 p-[14px]">
                        <div class="text-slate-300 text-sm">Pekerjaan</div>
                        <div class="text-[22px] font-bold mt-[6px]">12</div>
                    </div>
                    <div class="rounded-[10px] bg-slate-800/40 border border-slate-700 p-[14px]">
                        <div class="text-slate-300 text-sm">Subtask</div>
                        <div class="text-[22px] font-bold mt-[6px]">48</div>
                    </div>
                    <div class="rounded-[10px] bg-slate-800/40 border border-slate-700 p-[14px]">
                        <div class="text-slate-300 text-sm">Status</div>
                        <div class="text-[22px] font-bold mt-[6px]">Aktif</div>
                    </div>
                </div>

                <div class="mt-[18px] flex flex-col sm:flex-row gap-[10px] sm:justify-end">
                    <a href="./logout.php" class="text-center rounded-[8px] bg-slate-600 hover:bg-slate-500 transition-colors px-[14px] py-[10px] font-semibold">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php
        include "./component/footer.component.php";
        echo Footer();
    ?>

</body>
</html>


