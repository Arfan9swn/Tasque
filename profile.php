<?php

require_once "./dbconn.php";
require_once "./auth.php";

require_login();

$conn = dbConnect();
$userId = current_user_id();

$user = null;
if ($userId !== null) {
    $userIdEsc = (int)$userId;
    $res = $conn->query("SELECT * FROM user WHERE user_id = $userIdEsc LIMIT 1");
    if ($res && $res->num_rows === 1) {
        $user = $res->fetch_assoc();
    }
}

$stats = [
        'task_count' => 0,
        'subtask_count' => 0,
        'status_label' => '—',
    ];

// Stats per user
if ($userId !== null) {
    $userIdEsc = (int)$userId;

    $resTask = $conn->query("SELECT COUNT(*) AS cnt FROM task WHERE user_id = $userIdEsc");
    if ($resTask && $row = $resTask->fetch_assoc()) {
        $stats['task_count'] = (int)($row['cnt'] ?? 0);
    }

    $resSubtask = $conn->query(
        "SELECT COUNT(*) AS cnt
         FROM subtask s
         JOIN task t ON t.task_id = s.task_id
         WHERE t.user_id = $userIdEsc"
    );
    if ($resSubtask && $row = $resSubtask->fetch_assoc()) {
        $stats['subtask_count'] = (int)($row['cnt'] ?? 0);
    }

    $resStatus = $conn->query(
        "SELECT status, COUNT(*) AS cnt
         FROM task
         WHERE user_id = $userIdEsc
         GROUP BY status
         ORDER BY cnt DESC
         LIMIT 1"
    );
    if ($resStatus && $resStatus->num_rows > 0) {
        $row = $resStatus->fetch_assoc();
        $statusVal = (int)($row['status'] ?? -1);
        if ($statusVal === 1) $stats['status_label'] = 'Aktif';
        else if ($statusVal === 2) $stats['status_label'] = 'Selesai';
        else $stats['status_label'] = 'Belum';
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

                <div class="w-full flex justify-center mt-[25px]">
        <div class="w-[86%] max-w-[860px] border border-slate-700 rounded-[8px] bg-slate-700/40 p-[20px]">
            <div class="text-[20px] font-bold">Stats Profil</div>
                <div class="mt-[12px] grid grid-cols-1 sm:grid-cols-3 gap-[14px]">
                <div class="rounded-[10px] bg-slate-800/40 border border-slate-700 p-[14px]">
                    <div class="text-slate-300 text-sm">Total Pekerjaan</div>
                    <div class="text-[22px] font-bold mt-[6px]"> <?php echo htmlspecialchars((string)$stats['task_count']); ?> </div>
                </div>
                <div class="rounded-[10px] bg-slate-800/40 border border-slate-700 p-[14px]">
                    <div class="text-slate-300 text-sm">Status</div>
                    <div class="text-[22px] font-bold mt-[6px]"> <?php echo htmlspecialchars((string)$stats['status_label']); ?> </div>
                </div>
            </div>

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


