<?php
namespace App\Infrastructure;

require_once 'sdbh.php';
use sdbh\sdbh;

class DataAdapter {
    private $dbh;

    public function __construct() {
        $this->dbh = new sdbh(); 
    }

    public function getProductById($productId) {
        $product = $this->dbh->make_query("SELECT * FROM a25_products WHERE ID = ?", [$productId]);
        return $product ? $product[0] : null;
    }

    public function getProducts() {
        return $this->dbh->make_query('SELECT * FROM a25_products');
    }

    public function getServices() {
        $services = unserialize($this->dbh->mselect_rows('a25_settings', ['set_key' => 'services'], 0, 1, 'id')[0]['set_value']);
        return $services;
    }

    public function getTariff($productId) {
        $result = $this->dbh->make_query("SELECT TARIFF FROM a25_products WHERE ID = ?", [$productId]);
        return $result ? $result[0]['TARIFF'] : null;
    }
}
