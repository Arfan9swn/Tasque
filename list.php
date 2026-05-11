<?php

    // * jgn dihapus
    require_once "./dbconn.php";
    $conn = dbConnect();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List pekerjaan</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-800 text-white">
    <?php
    require "./component/navbar.component.php";
    echo NavBar();
    ?>

    <?php
    require "./component/footer.component.php";
    echo Footer();
    ?>
</body>
</html>