<?php 

if(!isset($_SESSION)){ 
    session_start();
}

require 'app/start.php';
$sessionData = array();
$currentPage = 'shopCart';
$itemTotal = 0;
$subTotal = '0.0';

if(isset($_SESSION['itemTotal']) && isset($_SESSION['subTotal']) ){
        $itemTotal = $_SESSION['itemTotal'];
        $subTotal = $_SESSION['subTotal'];
}

if(empty($_GET['update'])){    
    
    echo "not update";
    
    //if(isset($_SESSION['basket'])){
        //$baskets = json_decode($_SESSION['basket']);
        require VIEW_ROOT . '/cart/show.php';
    //}
    
}else{
    echo "update process";
    $dataArray[] = array();
    
        foreach($_SESSION['basket'] as $id=>$data){
            $sessionData[] = json_decode($data,true);
        }

        $dataArray = json_decode($_GET['update']);
        var_dump($dataArray);
        $_SESSION['basket'] = array();

        foreach($sessionData as $id=>$data){
            foreach($dataArray as $value){
                if($data['id'] == $value->title){
                    echo $value->title . $value->qty;
                    if($value->qty > 0){
                        $data['qty'] = $value->qty;
                        array_push($_SESSION['basket'], json_encode($data));
                        if($value->qty > $data['qty']){
                            $subTotal += ($value->qty - $data['qty']) * $data['price'];
                        }else{
                            $subTotal -= ($data['qty'] - $value->qty) * $data['price'];
                        }
                        $_SESSION['subTotal'] = $subTotal;
                    }else{
                        $newValue = $data['price'] * $value->qty;
                        $orgValue = $data['price'] * $data['qty'];
                        $subTotal -= ($orgValue - $newValue);
                        $itemTotal -= $data['qty'];
                        $_SESSION['subTotal'] = $subTotal;
                        $_SESSION['itemTotal'] = $itemTotal;
                    }
                }
            }
        }

        $jsonData = [
                    'success' => true,
                     'msg' => 'Update Data'
                    ];

        echo json_encode($jsonData);    
}