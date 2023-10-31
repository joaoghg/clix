<?php

function phoneMask($phone){
    if(empty($phone)){
        return;
    }

    $phone = preg_replace("/\D/", '', $phone);
    $phone = preg_replace("/(\d{2})(\d{8,9})/", '($1) $2', $phone);
    $phone = preg_replace("/(\d{4,5})(\d{4})/", '$1-$2', $phone);

    return $phone;
}