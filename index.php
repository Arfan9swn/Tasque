<?php
    
    require_once "./auth.php";
    require_once "./dbconn.php";
    $conn = dbConnect();

    require_login();

    $userId = current_user_id();

    $stats = [
        'task_count' => 0,
        'subtask_count' => 0,
        'status_label' => '—',
    ];

    if ($userId !== null) {
        $userIdEsc = (int)$userId;

        $resTask = $conn->query("SELECT COUNT(*) AS cnt FROM task WHERE user_id = $userIdEsc");
        if ($resTask) {
            $row = $resTask->fetch_assoc();
            $stats['task_count'] = (int)($row['cnt'] ?? 0);
        }

        $resSub = $conn->query("SELECT COUNT(*) AS cnt
                                 FROM subtask
                                 WHERE task_id IN (SELECT task_id FROM task WHERE user_id = $userIdEsc)");
        if ($resSub) {
            $row = $resSub->fetch_assoc();
            $stats['subtask_count'] = (int)($row['cnt'] ?? 0);
        }

        $resStatus = $conn->query("SELECT status, COUNT(*) AS cnt
                                    FROM task
                                    WHERE user_id = $userIdEsc
                                    GROUP BY status
                                    ORDER BY cnt DESC
                                    LIMIT 1");
        if ($resStatus && $resStatus->num_rows > 0) {
            $row = $resStatus->fetch_assoc();
            $statusVal = (int)($row['status'] ?? -1);
            if ($statusVal === 1) $stats['status_label'] = 'Aktif';
            else if ($statusVal === 2) $stats['status_label'] = 'Selesai';
            else $stats['status_label'] = 'Belum';
        }
    }

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasque</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-800 text-white">

    <?php
        include "./component/navbar.component.php";
        echo NavBar();
    ?>

    <div class="ml-[5%] border border-slate-700 rounded-[5px] w-[40vw] mt-[25px] bg-slate-700/40">
        <input type="text" class="w-[40vw] focus:outline-none px-[10px] py-[3px]" placeholder="Cari tugas...">
    </div>

    <!-- category -->
    <div class="flex w-[100%] ml-[5%] mt-[25px]">
        <div class="w-[40%] border border-slate-700 rounded-[5px] p-[5px] bg-slate-700/40">
            <p class="border-b border-slate-700 pb-[5px]">Kategori :</p>

            <div class="mt-[10px] flex flex-wrap gap-[8px]">
                <div class="border border-slate-600 rounded-[5px] w-fit px-[12px] py-[4px] bg-slate-600 text-slate-200 text-sm">
                    Tambah Kategori +
                </div>

                <?php
                    $categories = [];
                    $catRes = $conn->query("SELECT cat_id, name FROM category ORDER BY name ASC");
                    if ($catRes) {
                        while ($row = $catRes->fetch_assoc()) {
                            $categories[] = $row;
                        }
                    }

                    if (count($categories) === 0) {
                ?>
                        <div class="border border-slate-700 rounded-[999px] w-fit px-[12px] py-[4px] bg-slate-700/40 text-slate-200 text-sm">
                            Belum ada kategori
                        </div>
                <?php
                    } else {
                        foreach ($categories as $cat) {
                ?>
                        <div class="border border-slate-700 rounded-[999px] w-fit px-[12px] py-[4px] bg-slate-700/40 text-slate-200 text-sm">
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </div>
                <?php
                        }
                    }
                ?>
            </div>
        </div>
    </div>

    <div class="flex w-[100%] justify-around mt-[25px]">

<!--task-->
        <div class="w-[40%] border border-slate-700 rounded-[5px] p-[5px] bg-slate-700/40">
            <p class="border-b border-slate-700 pb-[5px]">Kumpulan Pekerjaan :</p>
            <div class="border border-slate-700 rounded-[5px] w-fit mt-[10px] p-[5px] bg-slate-600 text-slate-200">
                Tambahkan Pekerjaan +
            </div>
        </div>
<!--subtask-->
        <div class="w-[40%] border border-slate-700 rounded-[5px] p-[5px] bg-slate-700/40">
            <p class="border-b border-slate-700 pb-[5px]">List Pekerjaan :</p>
            <div class="border border-slate-700 rounded-[5px] w-fit mt-[10px] p-[5px] bg-slate-600 text-slate-200">
                Tambahkan Pekerjaan +
            </div>
        </div>
    </div>

    <?php 
        include "./component/footer.component.php";
        echo Footer();
    ?>

</body>
</html>