<?php
    
    dbConnect()

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
        <input type="text" class="w-[40vw] focus:outline-none px-[10px] py-[3px]">
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