<?php
require_once 'DataAdapter.php'; 
use App\Infrastructure\DataAdapter;

$dataAdapter = new DataAdapter();

if (isset($_POST['productId'])) {
    $productId = $_POST['productId'];
    $tariff = $dataAdapter->getTariff($productId);
    echo $tariff ? $tariff : 'Тариф не найден';
} else {
    echo 'ID продукта не передан';
}
?>