<?php
session_start();

$_SESSION['id'] = null;
$_SESSION['admin'] = false;

$pdo = null;
echo "<script>window.location.href='login.html'</script>"; 
?>