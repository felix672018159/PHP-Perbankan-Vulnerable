<?php
require_once "database.php";
//set_session("");
//delete_session();
//echo get_session();
delete_session();
//set_session("kjqncanhpd");
//echo get_session();
//delete_session();
echo "Proses Logout ...";
header( "refresh:3;url=login.php");
//die("Proses logout, Menghapus sesi");
//show_all_user();
//echo get_user_by_session("iholaylfnb",3);
//echo dekripsi_caesar($kunci,1,"abcdefg");
//echo delete_user("sanhok");
?>