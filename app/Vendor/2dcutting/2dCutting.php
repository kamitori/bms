<?php

/*
 * Library for 2D-Cutting
 * Le Quan Ha, 18/Dec/2014
 */

function rotate_width($sizes) {
    foreach ($sizes as $key => $size) {
    if (floatval($size["width"]) < floatval($size["height"])) {
            $tmp = floatval($sizes[$key]["width"]);
            $sizes[$key]["width"] = floatval($sizes[$key]["height"]);
            $sizes[$key]["height"] = $tmp;
    }
    }

    return $sizes;
}

function quicksort( $array ) {
           /*     if( sizeof( $array ) < 2 ) {
                    return $array;
                }
                $left = $right = array( );
                reset( $array );
                $pivot_key  = key( $array );
                $pivot  = array_shift( $array );

                $li = 0;
                $ri = 0;
                foreach( $array as $k => $v ) {
                    if( (int)$v["width"] < (int)$pivot["width"] ) {
                        $left[$li] = $v;
                        $li++;
                    } else {
                        $right[$ri] = $v;
                        $ri++;
                    }
                }
                return array_merge(quicksort($left), array(0 => $pivot), quicksort($right)); */

    $len = sizeof($array);
    for ( $id = 0; $id < $len; $id++ ) {
        $maxv = $id;
        for ( $jd = ($id+1); $jd < $len; $jd++ ) {
            if (floatval($array[$maxv]["width"]) < floatval ($array[$jd]["width"] )) {
                $maxv = $jd;
            }
        }
        // swap
        $tempi = $array[$maxv];
        $array[$maxv] = $array[$id];
        $array[$id] = $tempi;
    }
    return $array;
}

function tall_quicksort( $array ) {
    $len = sizeof($array);
    for ( $id = 0; $id < $len; $id++ ) {
        $maxv = $id;
        for ( $jd = ($id+1); $jd < $len; $jd++ ) {
            if (floatval($array[$maxv]["height"]) < floatval ($array[$jd]["height"] )) {
                $maxv = $jd;
            }
        }
        // swap
        $tempi = $array[$maxv];
        $array[$maxv] = $array[$id];
        $array[$id] = $tempi;
    }
    return $array;
}

function smallest_quicksort( $array ) {
    $len = sizeof($array);
    for ( $id = 0; $id < $len; $id++ ) {
        $minv = $id;
        for ( $jd = ($id+1); $jd < $len; $jd++ ) {
            if ((floatval($array[$minv]["width"])*floatval($array[$minv]["height"])) > (floatval($array[$jd]["width"])*floatval($array[$jd]["height"]))) {
                $minv = $jd;
            }
        }
        // swap
        $tempi = $array[$minv];
        $array[$minv] = $array[$id];
        $array[$id] = $tempi;
    }
    return $array;
}

function skyLineInit($sizes) {
    $qI = 0;
    $skyArray = array();
    $skyArray[$qI] = array();
    foreach ($sizes as $key => $size) {
    for ($qK = 0; $qK < $size["quantity"]; $qK++) {
            $skyArray[$qI]['sheet_no'] = 0;
            $skyArray[$qI]["posX"] = 0;
            $skyArray[$qI]["posY"] = 0;
            $skyArray[$qI]["width"] = $size["width"];
            $skyArray[$qI]["height"] = $size["height"];
            $qI++;
    }
    }
    return $skyArray;
}

function gapInit($W, $H) {
    $gap = array();
    $gap[0] = array();
    $gap[0]["x1"] = 0;
    $gap[0]["y1"] = 0;
    $gap[0]["x2"] = $W;
    $gap[0]["y2"] = $H;
    return $gap;
}

function findGap($gap, &$skyElement, $sheet) {
    $gapLen = count($gap);
    for ($gI = ($gapLen-1); $gI >= 0; $gI--) {
        if ( (($gap[$gI]["x2"] - $gap[$gI]["x1"]) >= $skyElement["width"]) && (($gap[$gI]["y2"] - $gap[$gI]["y1"]) >= $skyElement["height"])) {
            $skyElement['sheet_no'] = $sheet;
            $skyElement["posX"] = $gap[$gI]["x1"];
            $skyElement["posY"] = $gap[$gI]["y1"];
            $skyElement["rotate"] = false;
            return $gI;
        } else {
            $skyElement['sheet_no'] == 0;
        }
    }
    return false;
}

function findRotatedGap($gap, &$skyElement, $sheet) {
    $gapLen = count($gap);
    for ($gI = ($gapLen-1); $gI >= 0; $gI--) {
        if ( (($gap[$gI]["x2"] - $gap[$gI]["x1"]) >= $skyElement["height"]) && (($gap[$gI]["y2"] - $gap[$gI]["y1"]) >= $skyElement["width"])) {
            $skyElement['sheet_no'] = $sheet;
            $skyElement["posX"] = $gap[$gI]["x1"];
            $skyElement["posY"] = $gap[$gI]["y1"];
            $skyElement["rotate"] = true;
            return $gI;
        } else {
            $skyElement['sheet_no'] == 0;
        }
    }
    return false;
}

function mergeGaps($gap) {
    // merging 2 gaps
    $gapLen = count($gap);
    for ($gI = 0; $gI < $gapLen; $gI++) {
        for ($gJ = ($gI+1); $gJ < $gapLen; $gJ++) {
            if (($gap[$gI]["x1"] == $gap[$gJ]["x1"]) && ($gap[$gI]["x2"] == $gap[$gJ]["x2"])) {
                $gap[$gI]["y2"] =  max($gap[$gJ]["y2"], $gap[$gI]["y2"]);
                $gap[$gI]["y1"] =  min($gap[$gJ]["y1"], $gap[$gI]["y1"]);

                for ($gK = $gJ; ($gK+1) < $gapLen; $gK++) {
                    $gap[$gK] = $gap[$gK+1];
                }
                unset($gap[$gapLen-1]);
                $gapLen--;
                $gJ--;
            }
        }
    }
    return $gap;
}

function createGap($gI, $gap, $skyElement, $W, $H) {
    $gapLen = count($gap);
    if ((($skyElement["posX"]+$skyElement["width"]) < $W) && (($skyElement["posY"]+$skyElement["height"]) < $H)) {
        $gap[$gapLen]["x1"] = $skyElement["posX"]+$skyElement["width"];
        $gap[$gapLen]["y1"] = $skyElement["posY"];
        $gap[$gapLen]["x2"] = $W;
        $gap[$gapLen]["y2"] = $skyElement["posY"]+$skyElement["height"];

        $gap[$gI]["y1"] = $gap[$gapLen]["y2"];
        $gapLen++;
    } else if (($skyElement["posX"]+$skyElement["width"]) == $W) {
        $gap[$gI]["y1"] = $skyElement["posY"]+$skyElement["height"];
    } else {
        $gap[$gI]["x1"] = $skyElement["posX"]+$skyElement["width"];
    }
    return $gap;
}

function createRotatedGap($gI, $gap, $skyElement, $W, $H) {
    $gapLen = count($gap);
    if ((($skyElement["posX"]+$skyElement["height"]) < $W) && (($skyElement["posY"]+$skyElement["width"]) < $H)) {
        $gap[$gapLen]["x1"] = $skyElement["posX"]+$skyElement["height"];
        $gap[$gapLen]["y1"] = $skyElement["posY"];
        $gap[$gapLen]["x2"] = $W;
        $gap[$gapLen]["y2"] = $skyElement["posY"]+$skyElement["width"];

        $gap[$gI]["y1"] = $gap[$gapLen]["y2"];
        $gapLen++;
    } else if (($skyElement["posX"]+$skyElement["height"]) == $W) {
        $gap[$gI]["y1"] = $skyElement["posY"]+$skyElement["width"];
    } else {
        $gap[$gI]["x1"] = $skyElement["posX"]+$skyElement["height"];
    }
    return $gap;
}

function arrangeSkyLine(&$sky, &$gap, $W, $H, $sheet, &$sheets, &$skyLeft) {
    $skyLen = count($sky);
    for ($key=0; $key<$skyLen; $key++) {
        if ($sky[$key]['sheet_no'] == 0) {
            // find gap
            $gI = findGap($gap, $sky[$key], $sheet);
            if ($sky[$key]['sheet_no'] == 0) {
                continue;
            }
            $skyLeft--;

            $sheets[$sheet-1][] = $sky[$key];

            $gap = createGap($gI, $gap, $sky[$key], $W, $H);
            $gap = mergeGaps($gap);
        }
    }
}

function arrangeRotatedSkyLine(&$sky, &$gap, $W, $H, $sheet, &$sheets, &$skyLeft) {
    $skyLen = count($sky);

    // optimization: rotation
    for ($key=0; $key<$skyLen; $key++) {
        if ($sky[$key]['sheet_no'] == 0) {
            // find gap
            $gI = findRotatedGap($gap, $sky[$key], $sheet);
            if ($sky[$key]['sheet_no'] == 0) {
                continue;
            }
            $skyLeft--;

            $sheets[$sheet-1][] = $sky[$key];

            $gap = createRotatedGap($gI, $gap, $sky[$key], $W, $H);
            $gap = mergeGaps($gap);
        }
    }
}

function rotateMaterial(&$W, &$H) {
    $W = floatval($W);
    $H = floatval($H);
    $W1 = max($W, $H);
    $H1 = min($W, $H);
    $W = $W1;
    $H = $H1;
}

// main function
function cutting(&$W, &$H, $osizes, &$skyArray, &$bestPolicy, &$leastSheets, &$sheets, &$gaps) {
    rotateMaterial($W, $H);
    $sizes = rotate_width($osizes);
    $width_sizes =  quicksort($sizes);
    $tall_sizes =  tall_quicksort($sizes);
    $smallest_sizes =  smallest_quicksort($sizes);

    $skyArray[0]["policy"] = "Widthest";
    $skyArray[0]["sky"] = skyLineInit($width_sizes);
    $skyArray[1]["policy"] = "Tallest";
    $skyArray[1]["sky"] = skyLineInit($tall_sizes);
    $skyArray[2]["policy"] = "Smallest";
    $skyArray[2]["sky"] = skyLineInit($smallest_sizes);

    $skyArray2 = $skyArray;

    $bestPolicy = 0;
    $leastSheets = PHP_INT_MAX;
    $sheets = array();
    $gaps = array();
    for ($pI = 0; $pI < 3; $pI++) {
            $sky = $skyArray[$pI]["sky"];
            $skyLen = count($sky);
            $skyLeft = $skyLen;
            $sheetStop = 0;

            $sheets[$pI] = array();
            $gaps[$pI] = array();

            // van con rectangle
            while ($skyLeft > 0) {
                $sheets[$pI][$sheetStop] = array();
                $gap = gapInit($W, $H);

                arrangeSkyLine($sky, $gap, $W, $H, $sheetStop+1, $sheets[$pI], $skyLeft);
                $gaps[$pI][$sheetStop] = $gap;

                $sheetStop++;
            }

            $skyArray[$pI]["sky"] = $sky;
            $skyArray[$pI]["sheet"] = $sheetStop;
            if ($leastSheets > $sheetStop) {
                $bestPolicy = $pI;
                $leastSheets = $sheetStop;
            }
    }

    $sheets2 = array();
    for ($pI = 0; $pI < 3; $pI++) {
            $sky = $skyArray2[$pI]["sky"];
            $skyLen = count($sky);
            $skyLeft = $skyLen;
            $sheetStop = 0;

            $sheets2[$pI] = array();
            $gaps[$pI] = array();

            // van con rectangle
            while ($skyLeft > 0) {
                $sheets2[$pI][$sheetStop] = array();
                $gap = gapInit($W, $H);

                arrangeRotatedSkyLine($sky, $gap, $W, $H, $sheetStop+1, $sheets2[$pI], $skyLeft);
                $gaps[$pI][$sheetStop] = $gap;

                $sheetStop++;
            }
            if ($leastSheets > $sheetStop) {
                $skyArray[$pI]["sky"] = $sky;
                $skyArray[$pI]["sheet"] = $sheetStop;
                $bestPolicy = $pI;
                $leastSheets = $sheetStop;
            }
    }
}