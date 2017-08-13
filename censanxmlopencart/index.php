<body bgcolor="#ccc">
<?php
  require('db.php');
  require('censan.php');

  $CatsandProducts = new Censan;
  $data = "../../../../Users/ertuozdenli/Desktop/XmlRecords.aspx.xml"; // URL or .xml file path
  $categories = $CatsandProducts->setxml($data);

  $catcheck = $CatsandProducts->catdbCheck($categories); // Kategoriler Kontrol Ediliyolor

  if (isset($catcheck["notexists"])) {
    $result = $CatsandProducts->addCategories($catcheck["notexists"]);
    if ($result) {
      echo "Kategori Ekleme işlemi tamamlandı.";
    }else{
      echo "Kategori Ekleme işlemi sırasında hata oluştu.";
    }
  }

  if (isset($catcheck["exists"])) {
    echo "Kategoriler eşleşti";
  }

  $results = $CatsandProducts->productChecks($categories);
  $toplamg=0; $toplamd=0;

  foreach ($results as $result) {
    $toplamg += $result[0][0];
    $toplamd += $result[0][1];
  }
  echo "<br>";
  echo "Giriş: ". $toplamg."<br>" ;
  echo "Güncelleme: ". $toplamd;

  ?>
</body>
