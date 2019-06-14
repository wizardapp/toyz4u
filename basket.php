<?php

  header("Content-Type: application/json");

    if(!isset($_SESSION)){ 
        session_start();
    }  
    $sessionData = array();
    require 'app/start.php';
    
    
   if(empty($_GET['item'])){
        $item = false;
    }else{
        
        $slug = $_GET['item'];
       
        if($slug){
            $item = $db->prepare('
                    SELECT items.itemId, items.title, items.subTitle, items.categoryId, items.picturelink,
                    items.slug, items.description, items.price, items.rating, items.stock,
                    category.catTitle, category.catSlug
                    FROM items, category 
                    WHERE items.slug = :slug and category.catId = items.categoryId
                    LIMIT 1
                 ');
                
            $item->execute(['slug' => $slug]);
            $item = $item->fetch(PDO::FETCH_ASSOC);
            
            if($item){
                if(!isset($_SESSION['basket']) ){
                    $_SESSION['basket'] = array();
                    $_SESSION['itemTotal'] = 0;
                    $_SESSION['subTotal'] =  0.0 ;
                }else{
                    foreach($_SESSION['basket'] as $id=>$data){
                        $sessionData[] = json_decode($data,true);
                    }
                }
                
                $basket = new Basket($slug, $item['title'], $item['subTitle'], $item['picturelink'], 1, $item['price']);
                //$key = array_search( $slug, array_column($sessionData,"id"));
                $key = array_search($slug, array_map(function($data){
                    return $data['id'];
                }, $sessionData), "id");
                if(empty($key) && !is_numeric($key)){
                    array_push($_SESSION['basket'], json_encode($basket));
                }else{
                   $_SESSION['basket'] = array();
                    foreach($sessionData as $id=>$data){
                        if($id == $key){
                            $data['qty'] = $data['qty'] + 1;
                        }
                        array_push($_SESSION['basket'], json_encode($data));
                    }
                }
                
                $_SESSION['itemTotal'] += 1;
                $_SESSION['subTotal'] +=  $item['price'];
                $jsonData = [
                    'success' => true,
                        'basket' => [
                        'itemTotal' => $_SESSION['itemTotal'],
                        'subTotal' => $_SESSION['subTotal']
                    ]
                ];
            }else{
                $jsonData = ['success'=>false];
            }
            echo json_encode($jsonData); 
        }
    }