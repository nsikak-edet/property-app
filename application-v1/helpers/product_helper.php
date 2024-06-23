<?php

function calculatePrice($cost){
    $prices = array();

    if($cost < 5){
        $prices[] = $cost * (1.6*1.21) + 12.8;
    }else{
        $prices[] = $cost * (0) + 12.8;
    }

    if($cost < 10){
        $prices[] = $cost * (1.5*1.21) + 12.8;
    }else{
        $prices[] = $cost * (0) + 12.8;
    }

    if($cost < 20){
        $prices[] = $cost * (1.4*1.21) + 12.8;
    }else{
        $prices[] = $cost * (0) + 12.8;
    }

    if($cost < 30){
        $prices[] = $cost * (1.3*1.21) + 12.8;
    }else{
        $prices[] = $cost * (0) + 12.8;
    }

    if($cost < 40){
        $prices[] = $cost * (1.28*1.21) + 12.8;
    }else{
        $prices[] = $cost * (0) + 12.8;
    }

    if($cost < 50){
        $prices[] = $cost * (1.26*1.21) + 12.8;
    }else{
        $prices[] = $cost * (0) + 12.8;
    }

    if($cost < 60){
        $prices[] = $cost * (1.24*1.21) + 12.8;
    }else{
        $prices[] = $cost * (0) + 12.8;
    }

    if($cost < 70){
        $prices[] = $cost * (1.23*1.21) + 12.8;
    }else{
        $prices[] = $cost * (0) + 12.8;
    }

    if($cost < 80){
        $prices[] = $cost * (1.22*1.21) + 12.8;
    }else{
        $prices[] = $cost * (0) + 12.8;
    }

    if($cost < 150){
        $prices[] = $cost * (1.2*1.21) + 12.8;
    }else{
        $prices[] = $cost * (0) + 12.8;
    }

    if($cost < 500){
        $prices[] = $cost * (1.18*1.21) + 12.8;
    }else{
        $prices[] = $cost * (0) + 12.8;
    }

    if($cost > 500){
        $prices[] = $cost * (1.2*1.2) + 12.8;
    }else{
        $prices[] = $cost * (0) + 12.8;
    }

    return round(max($prices),2);

}

function calculatePrice2($cost){
    $prices = array();

    $prices[] = $cost * (($cost < 5) ? (1.5 * 1.21) : 0) + (($cost < 5) ? (6.75) : 0);
    $prices[] = $cost * (($cost < 10) ? (1.4 * 1.21) : 0) + (($cost < 10) ? (6.5) : 0);
    $prices[] = $cost * (($cost < 30) ? (1.35 * 1.21) : 0) + (($cost < 30) ? (6) : 0);
    $prices[] = $cost * (($cost < 50) ? (1.35 * 1.21) : 0) + (($cost < 50) ? (5) : 0);
    $prices[] = $cost * (($cost < 80) ? (1.3 * 1.21) : 0) + 4;
    $prices[] = $cost * (($cost < 150) ? (1.25 * 1.21) : 0) + 6;
    $prices[] = $cost * (($cost < 500) ? (1.23 * 1.21) : 0) + 7;
    $prices[] = $cost * (($cost < 500000) ? (1.21 * 1.21) : 0) + 7;

    return round(max($prices),2);

}

function calculatePrice3($cost){
    $prices = array();

    if($cost < 5){
        $prices[] = $cost * (1.5*1.21) + 8;
    }else{
        $prices[] = $cost * (0) + 8;
    }

    if($cost < 10){
        $prices[] = $cost * (1.45*1.21) + 8;
    }else{
        $prices[] = $cost * (0) + 8;
    }

    if($cost < 30){
        $prices[] = $cost * (1.4*1.21) + 6;
    }else{
        $prices[] = $cost * (0) + 6;
    }

    if($cost < 50){
        $prices[] = $cost * (1.35*1.21) + 3;
    }else{
        $prices[] = $cost * (0) + 3;
    }

    if($cost < 80){
        $prices[] = $cost * (1.3*1.21) + 2;
    }else{
        $prices[] = $cost * (0) + 2;
    }

    if($cost < 150){
        $prices[] = $cost * (1.28*1.21) + 1;
    }else{
        $prices[] = $cost * (0) + 1;
    }

    if($cost < 500){
        $prices[] = $cost * (1.25*1.21) + 1;
    }else{
        $prices[] = $cost * (0) + 1;
    }

    if($cost > 500){
        $prices[] = $cost * (1.23*1.21) + 1;
    }else{
        $prices[] = $cost * (0) + 1;
    }

    return round(max($prices),2);

}

?>
