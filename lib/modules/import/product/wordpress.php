<?php

    require_once LOGIN_CHECK;

    while(($dataLines=fgetcsv($openFile)) !== false) {

        if($i!=0) {

            if($dataLines[0]!='') {

                $productName=mysqli_real_escape_string($connection, $dataLines[0]);
                $productURL=mysqli_real_escape_string($connection, $dataLines[1]);
                $productHighlights=mysqli_real_escape_string($connection, $dataLines[3]);
                $productDescription=mysqli_real_escape_string($connection, $dataLines[4]);
                $productStatus=mysqli_real_escape_string($connection, (($dataLines[5]=='publish') ? 1 : 0 ));
                $productDate=mysqli_real_escape_string($connection, date('Y-m-d', strtotime($dataLines[7])));
                $productSKU=mysqli_real_escape_string($connection, $dataLines[10]);
                $productMRP=mysqli_real_escape_string($connection, $dataLines[14]);
                
                $productSalePrice=mysqli_real_escape_string($connection, $dataLines[15]);
                $productSalePrice=(($productSalePrice!=0) ? $productSalePrice : $productMRP );

                $productWeight=mysqli_real_escape_string($connection, $dataLines[16]);
                $productLenght=mysqli_real_escape_string($connection, $dataLines[17]);
                $productWidth=mysqli_real_escape_string($connection, $dataLines[18]);
                $productHeight=mysqli_real_escape_string($connection, $dataLines[19]);
                $productInStock=mysqli_real_escape_string($connection, (($dataLines[22]=='instock') ? 1 : 0 ));
                $productBackOrder=mysqli_real_escape_string($connection, (($dataLines[23]=='yes') ? 1 : 0 ));
                $productManageStock=mysqli_real_escape_string($connection, (($dataLines[24]=='yes') ? 1 : 0 ));
                $productTaxStatus=mysqli_real_escape_string($connection, (($dataLines[25]=='taxable') ? 1 : 0));
                $productTaxRate=mysqli_real_escape_string($connection, $dataLines[20]);
                $productIsFeatured=mysqli_real_escape_string($connection, $dataLines[28]);
                $productImages=mysqli_real_escape_string($connection, $dataLines[38]);
                $productTags=str_replace('|', ',', mysqli_real_escape_string($connection, $dataLines[43]));

                if($productImages!='') {

                    $galleryImages=explode(' | ', $productImages);

                    $localSavedGallery=array();
                    $imageGallerySize=sizeof($galleryImages);

                    if($imageGallerySize!=1) {

                        // for($j=0; $j<$imageGallerySize; $j++) {

                        //     if(isset($galleryImages[$j])) {

                        //         echo "Product ID : {$i} -> {$productName} this product has ".$imageGallerySize." of images || ";

                        //     }

                        // }

                        echo "Product ID : {$i} -> {$productName} this product has ".$imageGallerySize." of images || ";

                    } else {

                        echo "Product ID : {$i} -> {$productName} this product has ".$imageGallerySize." of images || ";

                    }

                } else {

                    $gallery='';
                    $addNewMedia=true;
                    $mediaLink=true;

                }

            }

            
        }
        
        $i++;

    }