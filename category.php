<?php 

if(!isset($_SESSION)){ 
    session_start();
}

require 'app/start.php';

$currentPage = 'category';
$itemTotal = 0;
$subTotal = '0.0';

if(empty($_GET['cat'])){
    $cat = false;
}else{   
    
    if(isset($_SESSION['itemTotal']) && isset($_SESSION['subTotal']) ){
        $itemTotal = $_SESSION['itemTotal'];
        $subTotal = $_SESSION['subTotal'];
    }
    
    $slug = $_GET['cat'];
    
    $statement = $db->prepare('
        SELECT * FROM category
        WHERE catSlug = :slug
        LIMIT 1
    ');
    
    $statement->execute(['slug' => $slug]);
    $category = $statement->fetch(PDO::FETCH_ASSOC);
    
    if($category){
        $statement = $db->prepare('
            SELECT * FROM items 
            WHERE categoryId = :id
            ORDER BY `itemId`
         ');        
        
        $statement->execute(['id' => $category['catId']]);

        $items = $statement->fetchALL(PDO::FETCH_ASSOC);
        
        if($items){
            require VIEW_ROOT . '/category/show.php';
        }
    }
}