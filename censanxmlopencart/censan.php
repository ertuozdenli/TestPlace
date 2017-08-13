<?php
class Censan
{
    public $xmldata;
    public $kategoriler = [];

    public function connect()
    {
        try {
            $db = new PDO("mysql:host=".DB_HOSTNAME.";dbname=".DB_DATABASE.";charset=utf8", DB_USERNAME, DB_PASSWORD);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $db;
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function setCat()
    {
        if ($this->xmldata) {
            foreach ($this->xmldata as $veri) {
                $this->kategoriler[] = trim($veri->UrunKategoriAdi);
            }
            $this->kategoriler = array_unique($this->kategoriler);
            return true;
        } else {
            echo 'First use setXml("your_file_path"); function.';
            return;
        }
    }

    public function setXml($xml, $setkat = true)
    {
        $this->xmldata = @simplexml_load_file($xml) or die("Error: Cannot access XML file");
        if ($setkat) {
            $this->setCat();
            return $this->kategoriler;
        }
        return true;
    }

    public function catlist()
    {
        return $this->kategoriler;
    }

    public function getProduct($kat)
    {
        $urunler;
        $i=0;
        if ($kat) {
            foreach ($this->xmldata as $veri) {
                if (trim($veri->UrunKategoriAdi) == trim($kat)) {
                    $urunler[$i]["adi"] = $veri->UrunAdi;
                    $urunler[$i]["kodu"] = $veri->UrunKodu;
                    $urunler[$i]["kdv"] = $veri->UrunKDV;
                    $urunler[$i]["adet"] = $veri->UrunStokDurum;
                    $urunler[$i]["fiyat_satis"] = $veri->PerakendeSatisFiyat;
                    $urunler[$i]["fiyat_birim"] = $veri->PerakendeSatisParaBirimi;
                    $urunler[$i]["desc"] = $veri->UrunAciklama;
                    $urunler[$i]["img_thumb"] = $veri->UrunKucukResim;
                    $urunler[$i]["img_large"] = $veri->UrunBuyukResim;
                    $i++;
                }
            }
            return $urunler;
        } else {
            return false;
        }
    }

    public function catdbCheck($catlist)
    {
        $result=[];
        $db = $this->connect();
        foreach ($catlist as $cat) {
            $query = $db->prepare('SELECT * FROM '. DB_PREFIX .'category_description where `language_id`=1 AND `name`=:cat');
            $query->bindParam(':cat', $cat);
            $query->execute();
            if ($query->rowCount()) {
                $result["exists"][] = $cat;
            } else {
                $result["notexists"][] = $cat;
            }
        }
        return $result;
    }

    public function addCategories($catlist)
    {
        $lcat=[];
        $db = $this->connect();
        $catid;
        $hata = false;
        foreach ($catlist as $cat) {
            $eklecat = $db->query('INSERT INTO ' . DB_PREFIX . 'category SET parent_id = 0, `top` = 0, `column` = 0, sort_order = 0, status = 1, date_modified = NOW(), date_added = NOW()');

            $catid = $db->lastInsertId();

            $ekledesc = $db->query('INSERT INTO ' . DB_PREFIX . 'category_description SET `category_id` = '.$catid.',
      `language_id` = 1,
      `name` = "'.$cat.'",
      `meta_title` = "'.$cat.'"
      ');

            $eklestore = $db->query('INSERT INTO ' . DB_PREFIX . 'category_to_store SET `category_id` = '.$catid.',
      `store_id`=0
      ');

            $eklepath = $db->query('INSERT INTO ' . DB_PREFIX . 'category_path SET `category_id` = '.$catid.',
      `path_id`= '.$catid.',
      `level` = 1
      ');

            if (!$eklecat or !$ekledesc or !$eklestore) {
                $hata=true;
            }
        }
        if ($hata) {
            return false;
        }
        return true;
    }

    public function getCatId($cat)
    {
        $db = $this->connect();
        $catId = $db->prepare('SELECT category_id FROM ' . DB_PREFIX . 'category_description WHERE `name` = :katad');
        $catId->bindParam(':katad', $cat);
        $catId->execute();
        $catId = $catId->fetch(PDO::FETCH_ASSOC);
        if ($catId) {
            return $catId["category_id"];
        } else {
            return false;
        }
    }

    public function getQuantity($status)
    {
        $adet=25;
        if ($status=="Stokta Değil") {
            $adet = 0;
        }
        return $adet;
    }

    public function productAddandUpdate($products=[], $catId, $status)
    {
        $db = $this->connect();
        $product[] = $products;
        $added=0;
        $updated=0;

        foreach ($products as $product) {
            $exists = $db->prepare('SELECT * FROM '. DB_PREFIX .'product WHERE `model`= :model');
            $exists->bindParam(":model", $product["kodu"]);
            $exists->execute();
            $result = $exists->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $productId = $result["product_id"];

                $adet = $this->getQuantity($product["adet"]);
                $fiyat = floatval($product["fiyat_satis"]);

                $pstatus = 1;

                if ($adet==0 && $status) {
                    $pstatus = 0;
                }
                $query = $db->prepare('UPDATE '. DB_PREFIX .'product SET
          `quantity` = :adet,
          `image` = :photourl,
          `price` = :fiyat,
          `status` = :status,
          `date_modified` = NOW()
           WHERE `product_id` = :pid
        ');

                $query->bindParam(":adet", $adet);
                $query->bindParam(":pid", $productId);
                $query->bindParam(":photourl", $product["img_large"]);
                $query->bindParam(":status", $pstatus);
                $query->bindParam(":fiyat", $fiyat);
                $query->execute();


                $query = $db->prepare(
            'UPDATE '. DB_PREFIX .'product_description SET
          `name` = :pname,
          `description` = :ldescription,
          `meta_title`= :mpname,
          `meta_description`= :shdesc
           WHERE `product_id` = :pid'
        );

                $aciklama = $product["desc"];
                $saciklama = substr($aciklama, 0, 170);

                $query->bindParam(":pid", $productId);
                $query->bindParam(":pname", $product["adi"]);
                $query->bindParam(":mpname", $product["adi"]);
                $query->bindParam(":ldescription", $aciklama);
                $query->bindParam(":shdesc", $saciklama);
                $query->execute();
                $updated++;
            } else {
                $adet = $this->getQuantity($product["adet"]);
                $fiyat = doubleval($product["fiyat_satis"]);
                $pstatus = 1;
                if ($adet==0 && $status) {
                    $pstatus = 0;
                }
                $query = $db->prepare('INSERT INTO '. DB_PREFIX .'product SET
          `model` = :pkodu,
          `quantity` = :adet,
          `stock_status_id` = 5,
          `image` = :photourl,
          `manufacturer_id` = 0,
          `shipping` = 1,
          `price` = :fiyat,
          `tax_class_id` = 0,
          `date_available` = NOW(),
          `minimum` = 1,
          `sort_order` = 0,
          `status`= :status,
          `viewed` = 0,
          `date_added` = NOW(),
          `date_modified` = NOW()
        ');

                $query->bindParam(":pkodu", $product["kodu"]);
                $query->bindParam(":adet", $adet);
                $query->bindParam(":photourl", $product["img_large"]);
                $query->bindParam(":fiyat", $fiyat);
                $query->bindParam(":status", $pstatus);
                $query->execute();

                $productId = $db->lastInsertId();
                // var_dump($db->lastInsertId());
                // die();
                $query = $db->prepare('INSERT INTO '. DB_PREFIX .'product_description SET
          `product_id` = :pid,
          `language_id` = 1,
          `name` = :pname,
          `description` = :description,
          `tag` = "",
          `meta_title` = :mname,
          `meta_description` = :mdesc,
          `meta_keyword`= ""
          ');

                $aciklama = $product["desc"];
                $saciklama = substr($aciklama, 0, 170);

                $query->bindParam(":pid", $productId);
                $query->bindParam(":description", $product["desc"]);
                $query->bindParam(":pname", $product["adi"]);
                $query->bindParam(":mname", $product["adi"]);
                $query->bindParam(":mdesc", $saciklama);
                $query->execute();


                $db->query('INSERT INTO '. DB_PREFIX .'product_to_store SET
          `product_id` = '.$productId.',
          `store_id` = 0
          ');
                // var_dump($catId);
                $db->query(
            'INSERT INTO '. DB_PREFIX .'product_to_category SET
          `product_id` = '.$productId.',
          `category_id` = '.$catId
        );

                $added++;
            }
        }
        return array($added,$updated);
    }

    public function productChecks($catlist, $status)
    {
        $urunler=[];
        $db = $this->connect();
        $result=[];
        $i=1;
        foreach ($catlist as $cat) {
            $catId = $this->getCatId($cat);
            if ($catId) {
                $urunler = $this->getProduct($cat);
                $result[$i][] = $this->productAddandUpdate($urunler, $catId, $status);
                $i++;
            }
        }
        $disabled = $this->productNotExists();
        return $result;
    }

    public function productNotExists()
    {
        $urunler=[];
        $codelist=[];
        $disabled=[];
        $db = $this->connect();

        foreach ($this->kategoriler as $cat) {
            $urunler[] = $this->getProduct($cat);
        }

        foreach ($urunler as $urun => $key) {
            foreach ($key as $code) {
                $codelist[] = $code["kodu"];
            }
        }

        $query = $db->prepare('SELECT product_id,model FROM '.DB_PREFIX.'product WHERE model LIKE :model');
        $modelprefix = PRODUCT_PREFIX."%";
        $query->bindParam(":model", $modelprefix);
        $query->execute();

        if ($query->rowCount()) {
            foreach ($query as $row) {
                $result = in_array($row["model"], $codelist);
                if (!$result) {
                    $query = $db->query('UPDATE '.DB_PREFIX.'product SET `status` = 0');
                    $disabled[] = $row["model"];
                }
            }
        }

        if ($disabled) {
            return $disabled;
        }

        return false;
    }
}
