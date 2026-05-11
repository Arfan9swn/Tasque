<?php

    function NavBar() {
        return '
            <div class="flex w-[100%] content-center justify-center p-[20px] bg-slate-600">
                <div class="flex w-[100%] gap-[50px]">
                    <div class="font-bold text-lg justify-center">
                        Tasque
                    </div>
                        <div class="font-semibold flex gap-[50px] justify-center text-center">
                            <div class="justify-center">
                                <a href="./index.php">List</a>
                            </div>
                            <div class="justify-center">
                                <a href="./about.php">Tentang</a>
                            </div>
                            <div class="justify-center">
                                <a href="./profile.php">Akun</a>
                            </div>
                        </div>
                </div>
            </div>
        ';
    }

?>