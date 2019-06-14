<?php
    if(!isset($_SESSION)){ 
        session_start();
    }

    use PayPal\Api\Payer;
    use PayPal\Api\Item;
    use PayPal\Api\ItemList;
    use PayPal\Api\Details;
    use PayPal\Api\Amount;
    use PayPal\Api\Transaction;
    use PayPal\Api\RedirectUrls;
    use PayPal\Api\Payment;

    require 'app/start.php';
    
    $sessionData = array();
    $basketArray = array();
    $price = 0;
    $qty = 0;

    if(!isset($_SESSION['basket'])){
        echo 'die the page';
        die();
    }else{
        foreach($_SESSION['basket'] as $id=>$data){
            $sessionData[] = json_decode($data,true);
        }
        
        foreach($sessionData as $id=>$data){
            foreach($data as $key=>$item){
                if($key == "id"){
                    $id = $item;
                }else if($key == "title"){
                    $title = $item;
                }else if($key == "subTitle"){
                    $subTitle = $item;
                }else if($key == "imageLink"){
                    $imgLink = $item;
                }else if($key == "qty"){
                    $qty = $item;
                }else if($key == "price"){
                    $price = $item;
                }
            }
            
            $basket = new Basket($id, $title, $subTitle, $imgLink, $qty, $price); 
            $price += $price;
            $qty += $qty;
            $basketArray[] = $basket;
            
        }

        //$total = $price + $shipping; 
        $shipping = 2.00;
        $product = $basket->getTitle();
        $total = ($price * $qty) + $shipping;

        echo "product =" . $product . " price = $" . $price . " and shipping $" . $shipping . " and total is $" . $total;
        
        
        $successPage = BASE_URL .  '/payment.php?success=true';
        $cancelPage = BASE_URL .  '/payment.php?success=false';
        
        echo $cancelPage;

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $item = new Item();
        $item->setName($product)
            ->setCurrency('SGD')
            ->setQuantity($qty)
            ->setPrice($price);

        $itemList = new ItemList();
        $itemList->setItems([$item]);

        $details = new Details();
        $details->setShipping($shipping)
            ->setSubTotal($price * $qty);

        $amount = new Amount();
        $amount->setCurrency('SGD')
            ->setTotal($total)
            ->setDetails($details);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($itemList)            
            ->setDescription('Pay for toyz')
            ->setInvoiceNumber(uniqid());

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl('$successPage')
            ->setCancelUrl('$cancelPage');

        $payment = new Payment();
        $payment->setIntent('sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions([$transaction]);

        try{
            $payment->create($paypal);
        } catch (PayPal\Exception\PayPalConnectionException $pce) {
            // Don't spit out errors or use "exit" like this in production code
            echo '<pre>';print_r(json_decode($pce->getData()));exit;
        }

        $approvalUrl = $payment->getApprovalLink();

        header("Location:{$approvalUrl}");
    }

    
    