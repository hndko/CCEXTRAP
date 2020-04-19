<?php
require "./cc.class.php";
echo("###############################################\n");
echo("#     Auto Generate + Checker CC Extrap       #\n");
echo("###############################################\n");
echo("# Create By : Kyuoko                          #\n");
echo("# Code : PHP                                  #\n");
echo("# Thx To UKL-TEAM, GFS-TEAM, AND YOU          #\n");
echo("###############################################\n");
echo "[+] BIN : "; $bin = trim(fgets(STDIN));
echo "[+] Check Status Valid ? 1/0 : "; $check = trim(fgets(STDIN));
echo "[+] Amount/Jumlah : "; $amount = trim(fgets(STDIN));
$new = new CC($bin, $check, $amount);
echo $new->Execute();
