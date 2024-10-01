<?php
namespace App;

require_once 'Infrastructure/DataAdapter.php';

class Calculate {
    private $dataAdapter;

    public function __construct() {
        $this->dataAdapter = new \App\Infrastructure\DataAdapter();
    }

    public function calculate1() {
        $days = $_POST['days'] ?? 0;
        $product_id = $_POST['product'] ?? 0;
        $selected_services = $_POST['services'] ?? [];

        $product = $this->dataAdapter->getProductById($product_id);
        if ($product) {
            $price = $product['PRICE'];
            $tarif = $product['TARIFF'];
        } else {
            echo "Ошибка, товар не найден!";
            return;
        }

        $tarifs = unserialize($tarif);
        $product_price = $price;

        if (is_array($tarifs)) {
            foreach ($tarifs as $day_count => $tarif_price) {
                if ($days >= $day_count) {
                    $product_price = $tarif_price;
                }
            }
        }

        $total_price = $product_price * $days;

        $services_price = array_reduce($selected_services, function($carry, $service) use ($days) {
            return $carry + ((float)$service * $days);
        }, 0);

        $total_price += $services_price;

        echo $total_price;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $instance = new Calculate();
    $instance->calculate1();
}
