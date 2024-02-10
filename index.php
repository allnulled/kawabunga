<?php

include("./kawabunga.php");

try {
    get_kawabunga()->dispatch();
} catch (Exception $ex) {
    if(PHP_ENV === "test") {
        var_dump($ex->getMessage());
    } else {
        echo json_encode($ex, JSON_UNESCAPED_UNICODE);
        exit;
    }
}


?>
