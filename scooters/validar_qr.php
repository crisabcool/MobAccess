<?php

session_start();

include('config/conexao.php');

$codigo = $_GET['codigo'];

$sql = "
SELECT *
FROM scooters
WHERE qr_code='$codigo'
";

$result = $conn->query($sql);

if($result->num_rows > 0){

    $scooter = $result->fetch_assoc();

    echo "
    <script>
        alert('Pronto para uso!');
        window.location='dashboard.php';
    </script>
    ";

}else{

    echo "
    <script>
        alert('QR Code inválido!');
        history.back();
    </script>
    ";
}
?>
