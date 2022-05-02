<?php


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 404 Not Found');
    $_GET['e'] = 404; 
    include 'err.php';
    exit;
}


$EMPTY = "Не заполнены поля: ";
$NAME = "имя ";
$PHONE = "номер телефона ";
$SUCCESS = "Сообщение отправлено по адресу: ";
$PRODUCT_ERROR = "Ндостаточно товаров для покупки.";
$SUCCESSBUT = "Сообщение не удалось отправить что то пошло не так попробуйте позже.";
$EMAIL = "Hiroto322@mail.ru";

if (!$_POST) {
    $_POST = json_decode(file_get_contents('php://input'), true);
}

$name = !empty($_POST['name']) ? $_POST['name'] : '';
$phoneNumber = !empty($_POST['phone']) ? $_POST['phone'] : '';
$products = !empty($_POST['products']) ? $_POST['products'] : '';

$errorResponse = "";
if (empty($name) || empty($phoneNumber)) {

    $errorResponse .= $EMPTY;

    if (empty($name)) {
        $errorResponse .= $NAME;
    }

    if (empty($phoneNumber)) {
        $errorResponse .= $PHONE;
    }

    $errorResponse[strlen($errorResponse)-1] = '.';
}

if (empty($products)){
    header('HTTP/1.1 400 Bad Request');
    echo json_encode([
        'msg' => $PRODUCT_ERROR
    ]);
    exit;
}

if (!empty($errorResponse)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode([
        'msg' => $errorResponse
    ]);
    exit;
}

header('HTTP/1.1 200 OK');
ini_set('display_errors', 1);

$productsForSend = "\nСостав заказа:\n";


foreach ($products as $product) {
    $productsForSend .= $product["title"] . " " . $product["price"] . "\n" ;
}

$result = mail($EMAIL, $name, $phoneNumber . $productsForSend);
if ($result) {
    echo json_encode([
        'msg' => $SUCCESS
    ]);
} else {
    echo json_encode([
        'msg' => $SUCCESSBUT . $EMAIL . "."
    ]);
}

