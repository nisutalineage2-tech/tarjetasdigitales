<?php
$pass = "123456";
$hash = '$2y$10$6cOi3z3m6.RxlOiaQSA8aOD9jopnLXYTNXN6hFgW68KdRB2sYyBr2';

if (password_verify($pass, $hash)) {
    echo "EL HASH FUNCIONA EN ESTE SERVIDOR";
} else {
    echo "ERROR DE LIBRERÍA O HASH";
}
?>
