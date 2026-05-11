<?php

    // * jangan dihapus
    require_once "./auth.php";
    require_once "./dbconn.php";
    $conn = dbConnect();

    require_login();

    $userId = current_user_id();

    $flash = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        $action = (string)($_POST['action'] ?? '');
        $userIdEsc = (int)($userId ?? 0);

        if ($userIdEsc > 0) {
            if ($action === 'add_category') {
                $name = trim((string)($_POST['cat_name'] ?? ''));
                $description = trim((string)($_POST['cat_description'] ?? ''));

                if ($name !== '' && $description !== '') {
                    $nameEsc = $conn->real_escape_string($name);
                    $descEsc = $conn->real_escape_string($description);

                    $conn->query("INSERT INTO category (name, description) VALUES ('$nameEsc', '$descEsc')");
                    $flash = 'Kategori berhasil ditambahkan.';
                } else {
                    $flash = 'Nama dan deskripsi kategori wajib diisi.';
                }
            }

            if ($action === 'add_task') {
                $title = trim((string)($_POST['task_title'] ?? ''));
                $description = trim((string)($_POST['task_description'] ?? ''));
                $due_date = trim((string)($_POST['due_date'] ?? ''));
                $priority = (int)($_POST['priority'] ?? 0);
                $status = (int)($_POST['status'] ?? 0);
                $cat_id = (int)($_POST['cat_id'] ?? 0);

                if ($title !== '' && $description !== '' && $due_date !== '' && $cat_id > 0) {
                    $titleEsc = $conn->real_escape_string($title);
                    $descEsc = $conn->real_escape_string($description);

                    $conn->query("INSERT INTO task (user_id, cat_id, title, description, due_date, priority, status, created_at) VALUES ($userIdEsc, $cat_id, '$titleEsc', '$descEsc', '$due_date', $priority, $status, CURDATE())");
                    $flash = 'Task berhasil ditambahkan.';
                } else {
                    $flash = 'Lengkapi data task (judul, deskripsi, due date, kategori).';
                }
            }

            if ($action === 'add_subtask') {
                $task_id = (int)($_POST['task_id'] ?? 0);
                $title = trim((string)($_POST['subtask_title'] ?? ''));
                $is_complete = isset($_POST['is_complete']) ? 1 : 0;

                if ($task_id > 0 && $title !== '') {
                    $titleEsc = $conn->real_escape_string($title);
                    $conn->query("INSERT INTO subtask (task_id, title, is_complete) VALUES ($task_id, '$titleEsc', $is_complete)");
                    $flash = 'Subtask berhasil ditambahkan.';
                } else {
                    $flash = 'Lengkapi data subtask (task dan judul).';
                }
            }
        }
    }

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
            // asumsi: 0= belum, 1= aktif, 2= selesai
            // TODO : bikin ui untuk 3 state tsb
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
    <!--width jangan 100%, entah napa ngebug-->
    <div class="flex w-[80%] ml-[5%] mt-[25px]">
        <div class="w-[50%] border border-slate-700 rounded-[5px] p-[5px] bg-slate-700/40">
            <p class="border-b border-slate-700 pb-[5px]">Kategori :</p>

            <div class="mt-[10px] flex flex-wrap gap-[8px]">

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
            <p class="border-b border-slate-700 pb-[5px]">Tambah Task :</p>

            <form action="" method="post" class="mt-[10px] flex flex-col gap-[10px]">
                <input type="hidden" name="action" value="add_task">

                <div class="flex flex-col gap-[6px]">
                    <label class="text-sm text-slate-200">Judul</label>
                    <input
                        type="text"
                        name="task_title"
                        required
                        class="rounded-[6px] bg-slate-800 border border-slate-600 px-[12px] py-[8px] focus:outline-none"
                    >
                </div>

                <div class="flex flex-col gap-[6px]">
                    <label class="text-sm text-slate-200">Deskripsi</label>
                    <textarea
                        name="task_description"
                        required
                        class="rounded-[6px] bg-slate-800 border border-slate-600 px-[12px] py-[8px] min-h-[80px] focus:outline-none"
                    ></textarea>
                </div>

                <div class="flex flex-col gap-[6px]">
                    <label class="text-sm text-slate-200">Due date</label>
                    <input
                        type="date"
                        name="due_date"
                        required
                        class="rounded-[6px] bg-slate-800 border border-slate-600 px-[12px] py-[8px] focus:outline-none"
                    >
                </div>

                <div class="flex gap-[10px]">
                    <div class="flex flex-col gap-[6px] flex-1">
                        <label class="text-sm text-slate-200">Priority</label>
                        <select
                            name="priority"
                            class="rounded-[6px] bg-slate-800 border border-slate-600 px-[12px] py-[8px] focus:outline-none"
                        >
                            <option value="0">Pengingat saja</option>
                            <option value="1">Segera dikerjakan</option>
                            <option value="2">Perlu dikerjakan</option>
                            <option value="3">Bisa kapan saja</option>
                        </select>
                    </div>

                    <div class="flex flex-col gap-[6px] flex-1">
                        <label class="text-sm text-slate-200">Status</label>
                        <select
                            name="status"
                            class="rounded-[6px] bg-slate-800 border border-slate-600 px-[12px] py-[8px] focus:outline-none"
                        >
                            <option value="0">Tidak aktif</option>
                            <option value="1">Aktif</option>
                            <option value="2">Sudah Diselesaikan</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-col gap-[6px]">
                    <label class="text-sm text-slate-200">Kategori</label>
                    <select
                        name="cat_id"
                        required
                        class="rounded-[6px] bg-slate-800 border border-slate-600 px-[12px] py-[8px] focus:outline-none"
                    >
                        <?php
                            $catSelectRes = $conn->query("SELECT cat_id, name FROM category WHERE cat_id IS NOT NULL ORDER BY name ASC");
                            if ($catSelectRes && $catSelectRes->num_rows > 0) {
                                while ($catRow = $catSelectRes->fetch_assoc()) {
                                    $catId = (int)$catRow['cat_id'];
                                    $catName = htmlspecialchars((string)$catRow['name']);
                        ?>
                                    <option value="<?php echo $catId; ?>"><?php echo $catName; ?></option>
                        <?php
                                }
                            } else {
                        ?>
                                <option value="0">Belum ada kategori</option>
                        <?php
                            }
                        ?>
                    </select>
                </div>

                <button
                    type="submit"
                    class="w-fit rounded-[6px] bg-slate-600 hover:bg-slate-500 transition-colors px-[14px] py-[10px] font-semibold"
                >
                    Simpan Task
                </button>
            </form>
        </div>
<!--subtask-->
        <div class="w-[40%] border border-slate-700 rounded-[5px] p-[5px] bg-slate-700/40">
            <p class="border-b border-slate-700 pb-[5px]">Tambah Subtask :</p>

            <form action="" method="post" class="mt-[10px] flex flex-col gap-[10px]">
                <input type="hidden" name="action" value="add_subtask">

                <div class="flex flex-col gap-[6px]">
                    <label class="text-sm text-slate-200">Task</label>
                    <select
                        name="task_id"
                        required
                        class="rounded-[6px] bg-slate-800 border border-slate-600 px-[12px] py-[8px] focus:outline-none"
                    >
                        <?php
                            $taskSelectRes = $conn->query("SELECT task_id, title FROM task WHERE user_id = $userIdEsc ORDER BY created_at DESC");
                            if ($taskSelectRes && $taskSelectRes->num_rows > 0) {
                                while ($taskRow = $taskSelectRes->fetch_assoc()) {
                                    $tId = (int)$taskRow['task_id'];
                                    $tTitle = htmlspecialchars((string)$taskRow['title']);
                        ?>
                                    <option value="<?php echo $tId; ?>"><?php echo $tTitle; ?></option>
                        <?php
                                }
                            } else {
                        ?>
                                <option value="0">Belum ada task</option>
                        <?php
                            }
                        ?>
                    </select>
                </div>

                <div class="flex flex-col gap-[6px]">
                    <label class="text-sm text-slate-200">Judul Subtask</label>
                    <input
                        type="text"
                        name="subtask_title"
                        required
                        class="rounded-[6px] bg-slate-800 border border-slate-600 px-[12px] py-[8px] focus:outline-none"
                    >
                </div>

                <label class="flex items-center gap-[10px] text-sm text-slate-200">
                    <input type="checkbox" name="is_complete" value="1" class="w-[16px] h-[16px]">
                    Tandai selesai
                </label>

                <button
                    type="submit"
                    class="w-fit rounded-[6px] bg-slate-600 hover:bg-slate-500 transition-colors px-[14px] py-[10px] font-semibold"
                >
                    Simpan Subtask
                </button>
            </form>
        </div>
    </div>

    <?php 
        include "./component/footer.component.php";
        echo Footer();
    ?>

</body>
</html>