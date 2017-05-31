<?php

/* 
 * 2D Imagick Interface
 * Le Quan Ha, 18/Dec/2014 
 * 
 */

function drawEmptySheet($startX, $startY, $W, $H, &$draw) {
    $draw->setFillColor('wheat');    // Set up some colors to use for fill and outline
    $draw->setStrokeColor( new ImagickPixel( 'green' ) );
    $draw->rectangle( $startX, $startY, $startX+$W, $startY+$H);    // Draw the rectangle 
    $draw->setFontSize(14);	
}

function drawSkyLine($startX, $startY, $sheet, $sky, &$draw, &$skyLeft) {
    $skyLen = count($sky);
    for ($key=0; $key<$skyLen; $key++) {    
        if ($sky[$key]["sheet_no"] == $sheet) {
            $draw->setFillColor('yellow');   
            $draw->setStrokeColor( new ImagickPixel( 'brown' ) );
            if ($sky[$key]["rotate"] == false)
                $draw->rectangle( $startX+$sky[$key]["posX"], $startY+$sky[$key]["posY"], $startX+$sky[$key]["posX"]+$sky[$key]["width"], $startY+$sky[$key]["posY"]+$sky[$key]["height"]);    // Draw the rectangle 
            else
                $draw->rectangle( $startX+$sky[$key]["posX"], $startY+$sky[$key]["posY"], $startX+$sky[$key]["posX"]+$sky[$key]["height"], $startY+$sky[$key]["posY"]+$sky[$key]["width"]);    // Draw the rectangle 
            $skyLeft--;
        }
    }	
}

function drawGaps($startX, $startY, $gap, &$draw) {
    $gapLen = count($gap);
    $draw->setFillColor('green');   
    $draw->setStrokeColor( new ImagickPixel( 'blue' ) );
    for ($gI = 0; $gI < $gapLen; $gI++) {         
        $draw->rectangle( $startX+$gap[$gI]["x1"], $startY+$gap[$gI]["y1"], $startX+$gap[$gI]["x2"], $startY+$gap[$gI]["y2"]);    // Draw the rectangle    
    }
}

function drawProblem($sizes, &$draw) {
    $startY = 800;
    $draw->setFontSize(32);

    foreach ($sizes as $key => $size) {
        $draw->setFillColor('yellow');    // Set up some colors to use for fill and outline
        $draw->setStrokeColor( new ImagickPixel( 'brown' ) );
        $draw->annotation ( 3000, $startY , "Quantity: ".$size["quantity"].", Size: ".$size["width"]." * ".$size["height"] );
        $draw->rectangle( 3000, $startY+18, $size["width"]+3000, $size["height"]+$startY+18);    // Draw the rectangle 
        $startY += $size["height"] + 80;
    }
}

function drawPolicy($policy, &$draw) {
    $startY = 1600;
    $draw->setFillColor('green');   
    $draw->setStrokeColor( new ImagickPixel( 'blue' ) );
    $draw->setFontSize(49);
    $draw->annotation ( 2900, $startY+80 , $policy);
}

function drawRemaining($startX, $startY, $H, $sheet, $skyLeft, $skyLen, &$draw) {
    $draw->setFillColor('green');   
    $draw->setStrokeColor( new ImagickPixel( 'blue' ) );
    $draw->setFontSize(27);
    $draw->annotation ( $startX+50, $startY+$H+45 , "Sheet ".$sheet.". Remaining ".$skyLeft."/".$skyLen." rectangles" );
}