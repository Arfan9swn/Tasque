<?php

    require_once "./dbconn.php";
    $conn = dbConnect();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-800 text-white min-h-screen">

    <?php
        include "./component/navbar.component.php";
        echo NavBar();
    ?>

    <div class="w-full flex justify-center px-[5%] mt-[25px]">
        <div class="w-full max-w-[900px] border border-slate-700 rounded-[8px] bg-slate-700/40 p-[20px]">
            <div class="text-[32px] font-bold leading-none mb-[8px]">Tentang Tasque</div>
            <div class="text-slate-200 mb-[18px] max-w-[650px]">
                Tasque adalah aplikasi manajemen pekerjaan untuk membantu kamu mengatur task dan subtask secara rapi.
                Dengan tampilan yang sederhana namun terstruktur, kamu bisa fokus menyelesaikan pekerjaan tanpa kehilangan konteks.
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-[14px]">
                <div class="rounded-[10px] border border-slate-700 bg-slate-800/40 p-[14px]">
                    <div class="font-semibold">Tugas Terstruktur</div>
                    <div class="text-slate-200 text-sm mt-[6px]">
                        Buat kategori, tambah task, dan pantau informasi penting seperti tanggal jatuh tempo, prioritas, dan status.
                    </div>
                </div>
                <div class="rounded-[10px] border border-slate-700 bg-slate-800/40 p-[14px]">
                    <div class="font-semibold">Subtask</div>
                    <div class="text-slate-200 text-sm mt-[6px]">
                        Pecah pekerjaan besar menjadi langkah yang lebih kecil agar lebih mudah dikerjakan dan dilacak progresnya.
                    </div>
                </div>
                <div class="rounded-[10px] border border-slate-700 bg-slate-800/40 p-[14px]">
                    <div class="font-semibold">Tampilan Konsisten</div>
                    <div class="text-slate-200 text-sm mt-[6px]">
                        Menggunakan Tailwind CSS agar UI seragam di seluruh halaman, mulai dari komponen navigasi hingga layout konten.
                    </div>
                </div>
            </div>

            <div class="mt-[16px] rounded-[10px] border border-slate-700 bg-slate-800/40 p-[14px]">
                <div class="font-semibold">Kenapa Tasque?</div>
                <div class="text-slate-200 text-sm mt-[6px] leading-relaxed">
                    Karena pekerjaan tidak selalu selesai dalam sekali langkah. Tasque membantu kamu memecah pekerjaan,
                    mengelola urutan prioritas, serta membuat progres terlihat jelas dari waktu ke waktu.
                    Tujuannya: pekerjaan lebih terarah, stres lebih rendah, dan kamu selalu tahu apa yang harus dilakukan berikutnya.
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
