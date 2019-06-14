<?php 

if(!isset($_SESSION)){ 
        session_start();
}

require 'app/start.php';

$currentPage = 'item';
$itemTotal = 0;
$subTotal = '0.0';

if(empty($_GET['item'])){
    $item = false;
}else{
    
    if(isset($_SESSION['itemTotal']) && isset($_SESSION['subTotal']) ){
        $itemTotal = $_SESSION['itemTotal'];
        $subTotal = $_SESSION['subTotal'];
    }
    
    $slug = $_GET['item'];
    $item = $db->prepare('
        SELECT items.itemId, items.title, items.subTitle, items.categoryId, items.picturelink,
        items.slug, items.description, items.price, items.rating, items.stock, items.features,  
        category.catTitle, category.catSlug
        FROM items, category 
        WHERE items.slug = :slug and category.catId = items.categoryId
        LIMIT 1
     ');
    
    $item->execute(['slug' => $slug]);
    
    $item = $item->fetch(PDO::FETCH_ASSOC);
    
    if($item){
        require VIEW_ROOT . '/item/show.php';
    }
}