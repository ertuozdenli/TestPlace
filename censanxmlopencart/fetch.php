<?php
if ($_GET["pass"]=="123") {
    require('db.php');
    require('censan.php');

    $data = $_GET["url"];
    $status = false;
    if ($_GET["stok"] == "true") {
        $status = true;
    }


    $CatsandProducts = new Censan;
    // URL or .xml file path
    $categories = $CatsandProducts->setxml($data);

    $catcheck = $CatsandProducts->catdbCheck($categories); // Kategoriler Kontrol Ediliyolor

    if (isset($catcheck["notexists"])) {
        $result = $CatsandProducts->addCategories($catcheck["notexists"]);
        if ($result) {
            echo "Kategori Ekleme işlemi tamamlandı.";
        } else {
            echo "Kategori Ekleme işlemi sırasında hata oluştu.";
        }
    }

    if (isset($catcheck["exists"])) {
        echo "Kategoriler eşleşti. <br>";
    }

    $results = $CatsandProducts->productChecks($categories, $status);
    $disabled = $CatsandProducts->productNotExists();

    $toplamg=0;
    $toplamd=0;

    foreach ($results as $result) {
        $toplamg += $result[0][0];
        $toplamd += $result[0][1];
    }

    echo "Giriş: ". $toplamg."<br>" ;
    echo "Güncelleme: ". $toplamd."<br><br>" ;

    if ($disabled) {
        echo PRODUCT_PREFIX."ile başlayan ve Censan verisiyle uyuşmayan ürünler devre dışı bırakıldı.";
        foreach ($disabled as $key) {
            echo "<li>".$key."</li>";
        }
    }
} else {
    echo "Wrong Pass";
}
