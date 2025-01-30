<!DOCTYPE html>
<html lang="cs">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Skladová zásoba | Vento Bohemia</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap');
body { font-family: 'Montserrat', sans-serif; margin: 0; padding: 0; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1); font-weight: 400; min-height: 100vh; display: flex; flex-direction: column; }
.container { width: 70%; margin: 0 auto; text-align: center; padding-top: 210px; flex: 1; }
table { width: 100%; border-collapse: collapse; box-shadow: 0 0 30px rgba(0, 0, 0, 0.4); font-size: 1.1em; border-radius: 15px; }
th, td { padding: 8px; border: 1px solid #ddd; }
th { background-color: #f2f2f2; }
th, td {text-align: center;}
th:nth-child(1), td:nth-child(1) { width: 15%; }
th:nth-child(2), td:nth-child(2) { width: 10%; }
th:nth-child(3), td:nth-child(3) { width: 60%; }
th:nth-child(4), td:nth-child(4) { width: 15%; }
.page-header { display: flex; align-items: center; justify-content: center; position: fixed; top: 0; width: 100%; background-color: #fff; padding: 10px 0; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.4); }
.page-title { margin-left: 10px; font-size: 24px; }
#search-form { position: absolute; top: calc(100% + 60px); left: 50%; transform: translateX(-50%); display: flex; border: 1px solid #ccc; border-radius: 25px; overflow: hidden; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4); }
#search-input { padding: 10px; border: none; border-radius: 25px 0 0 25px; width: 300px; }
#search-button { padding: 10px 20px; border: none; border-radius: 0 25px 25px 0; cursor: pointer; background-color: #f2f2f2; color: #000; transition: background-color 0.3s ease; }
#search-button:hover { background-color: #cd2026; color: #fff; }
footer { position: fixed; bottom: 0; width: 100%; background-color: #f2f2f2; padding: 20px; text-align: center; font-size: 14px; color: #333; z-index: 10; }
</style>
</head>
<body>
<div class="page-header">
<a href="https://eshop.ventobohemia.cz/zasoba.php"><img src="/img/import/img/Vento.png" alt="Vento logo" height="50"></a>
<h1 class="page-title">SKLADOVÁ ZÁSOBA</h1>
<form id="search-form" method="POST">
    <input type="text" id="search-input" name="search" placeholder="Zadej KÓD nebo EAN" required style="font-style: italic">
    <button type="submit" id="search-button">VYHLEDAT</button>
</form>
</div>
<div class="container">
<?php
// Načtení konfigurace a inicializace PrestaShopu
include(dirname(__FILE__) . '/config/config.inc.php');
include(dirname(__FILE__) . '/init.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $searchText = pSQL(trim($_POST['search']));
    $query = "SELECT p.ean13 AS ean_code, p.reference AS product_code, UPPER(pl.name) AS product_name, sa.quantity, i.id_image
              FROM " . _DB_PREFIX_ . "product AS p
              LEFT JOIN " . _DB_PREFIX_ . "product_lang AS pl ON p.id_product = pl.id_product
              LEFT JOIN " . _DB_PREFIX_ . "stock_available AS sa ON p.id_product = sa.id_product
              LEFT JOIN " . _DB_PREFIX_ . "image AS i ON p.id_product = i.id_product
              WHERE (p.reference = '$searchText' OR p.ean13 = '$searchText')
              AND pl.id_lang = 1 AND p.active = 1
              LIMIT 1";

    $result = Db::getInstance()->executeS($query);

    if (!empty($result)) {
        echo "<table><tr><th>EAN</th><th>Kód</th><th>Název</th><th>Množství na skladě</th></tr>";
        foreach ($result as $row) {
            $eanCode = $row['ean_code'];
            $productCode = $row['product_code'];
            $productName = $row['product_name'];
            $quantity = $row['quantity'];
            $idImage = $row['id_image'];
            $imageUrl = $idImage ? "https://eshop.ventobohemia.cz/img/p/" . implode('/', str_split($idImage)) . "/$idImage.jpg" : "";

            echo "<tr><td>$eanCode</td><td>$productCode</td><td>$productName</td><td>$quantity ks</td></tr>";
            if ($imageUrl) {
                echo "</table><img src='$imageUrl' alt='Obrázek produktu' style='margin-top: 50px; max-width: 100%; max-height: 400px; height: auto;'>";
            }
        }
    } else {
        echo "<p>Produkt nebyl nalezen.</p>";
    }
}
?>
</div>
<footer>
&copy; <?php echo date("Y"); ?> Vento Bohemia spol. s.r.o., vytvořil Radim Ručil 2025
</footer>
</body>
</html>
