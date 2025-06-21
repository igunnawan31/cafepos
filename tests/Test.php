<?php

require_once "api/Product.php";

$products = Product::all();
print_r($products);