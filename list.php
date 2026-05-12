<?php
    require_once "./auth.php";
    require_once "./dbconn.php";

    $conn = dbConnect();
    require_login();

    $userId = current_user_id();
    $userIdEsc = (int)($userId ?? 0);

    function statusLabel(int $status): string {
        if ($status === 1) return 'Aktif';
        if ($status === 2) return 'Selesai';
        return 'Belum';
    }

    function flashTypeClasses(string $type): string {
        return match ($type) {
            'success' => 'border-emerald-700 bg-emerald-900/20 text-emerald-200',
            'danger'  => 'border-red-700 bg-red-900/20 text-red-200',
            default    => 'border-slate-700 bg-slate-700/40 text-slate-200'
        };
    }

    // ! any mention of subtasks are deprecated

    $flash = null;
    $flashType = 'default';

    // * CRUD actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userIdEsc >= 0 && isset($_POST['action'])) { // >=0 to allow debug
        $action = (string)($_POST['action'] ?? '');

        try {
            if ($action === 'delete_task') {
                $task_id = (int)($_POST['task_id'] ?? 0);
                if ($task_id > 0) {
                    $conn->query("DELETE FROM task WHERE task_id = $task_id AND user_id = $userIdEsc");
                }
            }

            if ($action === 'toggle_subtask') {
                $subtask_id = (int)($_POST['subtask_id'] ?? 0);
                $newVal = isset($_POST['is_complete']) ? 1 : 0;
                if ($subtask_id > 0) {
                    $conn->query(
                        "UPDATE subtask s
                        JOIN task t ON t.task_id = s.task_id
                        SET s.is_complete = $newVal
                        WHERE s.subtask_id = $subtask_id AND t.user_id = $userIdEsc"
                    );
                    $flash = 'Status subtask diperbarui.';
                    $flashType = 'success';
                }
            }

            if ($action === 'update_task_status') {
                $task_id = (int)($_POST['task_id'] ?? 0);
                $status = (int)($_POST['status'] ?? -1);

                if ($task_id > 0 && in_array($status, [0,1,2], true)) {
                    $conn->query(
                        "UPDATE task t
                        SET t.status = $status
                        WHERE t.task_id = $task_id AND t.user_id = $userIdEsc"
                    );

                    $flash = 'Status task diperbarui.';
                    $flashType = 'success';
                }
            }


            if ($action === 'update_subtask') {
                $subtask_id = (int)($_POST['subtask_id'] ?? 0);
                $title = trim((string)($_POST['subtask_title'] ?? ''));
                $description = trim((string)($_POST['subtask_description'] ?? ''));

                if ($subtask_id > 0 && $title !== '') {
                    $titleEsc = $conn->real_escape_string($title);
                    $descEsc = $conn->real_escape_string($description);

                    $conn->query(
                        "UPDATE subtask s
                        JOIN task t ON t.task_id = s.task_id
                        SET s.title = '$titleEsc',
                            s.description = '$descEsc'
                        WHERE s.subtask_id = $subtask_id AND t.user_id = $userIdEsc"
                    );
                    $flash = 'Subtask berhasil diperbarui.';
                    $flashType = 'success';
                } else {
                    $flash = 'Judul subtask wajib diisi.';
                    $flashType = 'danger';
                }
            }
        } catch (Throwable $e) {
            $flash = 'Terjadi kesalahan saat memproses aksi.';
            $flashType = 'danger';
        }
    }
    
    $tasks = [];
    if ($userIdEsc > 0) {
        $res = $conn->query("SELECT t.task_id, t.title AS task_title, t.description AS task_description, t.due_date, t.priority, t.status, c.name AS category_name
                            FROM task t
                            JOIN category c ON c.cat_id = t.cat_id
                            WHERE t.user_id = $userIdEsc
                            ORDER BY t.created_at DESC");
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $tasks[] = $row;
            }
        }
    }

    $taskSubtasks = [];
    if (!empty($tasks)) {
        $taskIds = array_map(fn($t) => (int)$t['task_id'], $tasks);
        $taskIdsSql = implode(',', $taskIds);

        $subRes = $conn->query("SELECT s.subtask_id, s.task_id, s.title AS subtask_title, s.description AS subtask_description, s.is_complete
                                FROM subtask s
                                WHERE s.task_id IN ($taskIdsSql)");
        if ($subRes) {
            while ($sr = $subRes->fetch_assoc()) {
                $tid = (int)$sr['task_id'];
                if (!isset($taskSubtasks[$tid])) $taskSubtasks[$tid] = [];
                $taskSubtasks[$tid][] = $sr;
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List pekerjaan</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-800 text-white min-h-screen">

    <?php
        include "./component/navbar.component.php";
        echo NavBar();
    ?>

    <?php if (!empty($flash)): ?>
        <div class="ml-[5%] mr-[5%] mt-[15px] rounded-[8px] border px-[14px] py-[10px] text-sm <?php echo flashTypeClasses($flashType); ?>">
            <?php echo htmlspecialchars((string)$flash); ?>
        </div>
    <?php endif; ?>

    <div class="ml-[5%] mr-[5%] mt-[25px]">
        <div class="w-full border border-slate-700 rounded-[8px] bg-slate-700/40 p-[14px]">
            <div class="flex items-start justify-between flex-wrap gap-[10px]">
                <div>
                    <div class="text-[20px] font-bold">Daftar Pekerjaan</div>
                    <div class="text-slate-200 text-sm mt-[4px]">Task dan subtask milik akun kamu</div>
                </div>
            </div>

            <?php if (empty($tasks)): ?>
                <div class="mt-[18px] text-slate-200">Belum ada task.</div>
            <?php else: ?>
                <div class="mt-[18px] flex flex-col gap-[12px]">
                    <?php foreach ($tasks as $t): ?>
                        <?php
                            $tid = (int)$t['task_id'];
                            $subtasks = $taskSubtasks[$tid] ?? [];
                        ?>

                        <div class="border border-slate-700 rounded-[8px] bg-slate-800/30 p-[14px]">
                            <div class="flex items-start justify-between gap-[12px] flex-wrap">
                                <div>
                                    <div class="text-[18px] font-bold"><?php echo htmlspecialchars((string)$t['task_title']); ?></div>
                                    <div class="text-slate-200 text-sm mt-[6px]">Kategori: <?php echo htmlspecialchars((string)$t['category_name']); ?></div>
                                    <div class="text-slate-200 text-sm mt-[6px]">Due: <?php echo htmlspecialchars((string)$t['due_date']); ?></div>
                                    <div class="text-slate-200 text-sm mt-[6px]">Priority: <?php echo htmlspecialchars((string)$t['priority']); ?></div>
                                    <div class="text-slate-200 text-sm mt-[6px]">Status: <?php echo statusLabel((int)$t['status']); ?></div>

                                    <form method="post" action="" class="mt-[8px]">
                                        <input type="hidden" name="action" value="update_task_status">
                                        <input type="hidden" name="task_id" value="<?php echo $tid; ?>">
                                        <div class="flex items-center gap-[10px] flex-wrap">
                                            <label class="text-slate-200 text-sm">Ubah status:</label>
                                            <select name="status" class="rounded-[6px] bg-slate-800 border border-slate-600 px-[10px] py-[6px] focus:outline-none">
                                                <option value="0" <?php echo ((int)$t['status'] === 0) ? 'selected' : ''; ?>>Inaktif (Belum)</option>
                                                <option value="1" <?php echo ((int)$t['status'] === 1) ? 'selected' : ''; ?>>Aktif</option>
                                                <option value="2" <?php echo ((int)$t['status'] === 2) ? 'selected' : ''; ?>>Done</option>
                                            </select>
                                            <button type="submit" class="rounded-[6px] bg-slate-600 hover:bg-slate-500 transition-colors px-[12px] py-[8px] text-sm font-semibold">
                                                Simpan
                                            </button>
                                        </div>
                                    </form>

                                    <div class="text-slate-200 text-sm mt-[8px]">
                                        <?php echo nl2br(htmlspecialchars((string)$t['task_description'])); ?>
                                    </div>

                                    <form method="post" action="" class="mt-[10px]" onsubmit="return confirm('Hapus task ini? Task beserta subtask akan ikut terhapus.');">
                                        <input type="hidden" name="action" value="delete_task">
                                        <input type="hidden" name="task_id" value="<?php echo $tid; ?>">
                                        <button type="submit" class="w-fit rounded-[8px] border border-red-700 bg-red-900/20 hover:bg-red-900/30 text-red-200 px-[12px] py-[8px] text-sm font-semibold">
                                            Hapus Task
                                        </button>
                                    </form>

                                </div>

                            </div>

                            <div class="mt-[12px]">
                                <div class="flex items-center justify-between gap-[10px]">
                                    <div class="text-slate-200 text-sm font-semibold mb-[8px]">Subtask</div>
                                </div>

                                <?php if (empty($subtasks)): ?>
                                    <div class="text-slate-200 text-sm">Belum ada subtask.</div>
                                <?php else: ?>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-[10px]">
                                        <?php foreach ($subtasks as $s): ?>
                                            <?php $sid = (int)$s['subtask_id']; ?>

                                            <div class="rounded-[8px] border border-slate-700 bg-slate-700/40 p-[12px]">
                                                <div class="flex items-start justify-between gap-[10px]">
                                                    <div class="flex-1">
                                                        <div class="font-semibold"><?php echo htmlspecialchars((string)$s['subtask_title']); ?></div>
                                                        <div class="text-slate-200 text-sm mt-[6px]">
                                                            <?php echo nl2br(htmlspecialchars((string)$s['subtask_description'])); ?>
                                                        </div>

                                                        <div class="mt-[10px] flex flex-col gap-[8px]">
                                                            <form method="post" action="" class="flex items-center gap-[10px]">
                                                                <input type="hidden" name="action" value="toggle_subtask">
                                                                <input type="hidden" name="subtask_id" value="<?php echo $sid; ?>">
                                                                <label class="flex items-center gap-[10px] text-sm text-slate-200">
                                                                    <input
                                                                        type="checkbox"
                                                                        name="is_complete"
                                                                        value="1"
                                                                        <?php echo ((int)$s['is_complete'] === 1) ? 'checked' : ''; ?>
                                                                        class="w-[16px] h-[16px]"
                                                                        onchange="this.form.submit()"
                                                                    >
                                                                    Tandai selesai
                                                                </label>
                                                            </form>

                                                            <form method="post" action="" class="grid grid-cols-1 gap-[8px]">
                                                                <input type="hidden" name="action" value="update_subtask">
                                                                <input type="hidden" name="subtask_id" value="<?php echo $sid; ?>">

                                                                <div class="flex flex-col gap-[6px]">
                                                                    <label class="text-sm text-slate-200">Judul</label>
                                                                    <input
                                                                        type="text"
                                                                        name="subtask_title"
                                                                        value="<?php echo htmlspecialchars((string)$s['subtask_title']); ?>"
                                                                        class="rounded-[6px] bg-slate-800 border border-slate-600 px-[12px] py-[8px] focus:outline-none"
                                                                        required
                                                                    >
                                                                </div>

                                                                <div class="flex flex-col gap-[6px]">
                                                                    <label class="text-sm text-slate-200">Deskripsi</label>
                                                                    <textarea
                                                                        name="subtask_description"
                                                                        class="rounded-[6px] bg-slate-800 border border-slate-600 px-[12px] py-[8px] min-h-[70px] focus:outline-none"
                                                                    ><?php echo htmlspecialchars((string)$s['subtask_description']); ?></textarea>
                                                                </div>

                                                                <button type="submit" class="w-fit rounded-[8px] bg-slate-600 hover:bg-slate-500 transition-colors px-[12px] py-[8px] text-sm font-semibold">
                                                                    Update
                                                                </button>
                                                            </form>

                                                        </div>
                                                    </div>

                                                    <div class="flex-shrink-0">
                                                        <?php if ((int)$s['is_complete'] === 1): ?>
                                                            <div class="rounded-[999px] px-[10px] py-[4px] text-sm bg-emerald-900/30 border border-emerald-700 text-emerald-200">Selesai</div>
                                                        <?php else: ?>
                                                            <div class="rounded-[999px] px-[10px] py-[4px] text-sm bg-slate-700/40 border border-slate-700 text-slate-200">Belum</div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php
        include "./component/footer.component.php";
        echo Footer();
    ?>

</body>
</html>

