<?php

$shadowsizes = ["Unknown", "Tiny", "Small", "Medium", "Large", "Very Large", "Huge", "Huge (Fin)", "Thin"];

//input: array of hours;
//     hours = true used for time outputs
//     hours = false used for month outputs
//output: string
function timeprint($range, $hours = true){
    if (count($range) == ($hours ? 24 : 12)){
        if ($hours){
            $hourstring = "All day";
        }else{
            $hourstring = "Year-round";
        }
    }else{
        if (!$hours){
            //using months, so deduct 1 to use same logic as hours
            for ($i = 0; $i < count($range); $i++){
                $range[$i]--;
            }
        }
        $hourstring = "";
        if ($range[0] == 0){
            //otherwise at midnight, so wrap around to find true start time
            $temp = ($hours ? 24 : 12);
            for ($i = count($range) - 1; $i >= 0; $i--){
                if ($range[$i] != $temp - 1){
                    if ($i == count($range)){
                        if ($hours){
                            //23 is not at the end of the array, so it starts at midnight
                            $hourstring .= date("g a", mktime(0));
                        }else{
                            //...January
                            $hourstring .= date("M", mktime(null, null, null, 1));
                        }
                    }else{
                        if ($hours){
                            $hourstring .= date("g a", mktime($range[$i + 1]));
                        }else{
                            $hourstring .= date("M", mktime(null, null, null, $range[$i + 1] + 1));
                        }
                        
                        $wraptime = $range[$i + 1];
                    }
                    break; //starting time found, so continue
                }else{
                    $temp = $range[$i];
                }
            }
            $temp = -1; //skip midnight
        }else{
            $temp = -2;
        }

        foreach ($range as $hour){
            if ($hour != $temp + 1){ //don't print sequential hours
                if (isset($wraptime) && $hour == $wraptime){
                    break;
                }
                if ($temp > -1){
                    if ($hours){
                        //Add one extra because the end time should be the NEXT hour
                        $hourstring .= ' &ndash; ' . date("g a", mktime($temp + 1)) . '<br />';
                    }else{
                        //Don't add one extra because the range doesn't include the following month (while the end time should be the NEXT hour)
                        $hourstring .= ' &ndash; ' . date("M", mktime(null, null, null, $temp + 1)) . '<br />';
                    }
                    
                }

                if ($hours){
                    $hourstring .= date("g a", mktime($hour));
                }else{
                    $hourstring .= date("M", mktime(null, null, null, $hour + 1)); //One extra because of the offset
                }
            }
            $temp = $hour;
        }
        if ($hours){
            //Add one extra because the end time should be the NEXT hour
            $hourstring .= ' &ndash; ' . date("g a", mktime($temp + 1));    
        }else{
            //Don't add one extra because the range doesn't include the following month (while the end time should be the NEXT hour)
            $hourstring .= ' &ndash; ' . date("M", mktime(null, null, null, $temp + 1));   
        }
        
        

        
    }
    return $hourstring;
}

//Load databases if specified
if (isset($dbs)){
    $error_message = "";
    $rdb = []; //databases to be returned
    foreach($dbs as $db){ //Iterate DBs
        $lfile = file_get_contents(__DIR__ . "/../db/" . $db . ".json");
        if ($lfile === false){
            $error_message .= "Database '$db' not found";
        }else{
            //File loaded fine, so parse it
            $lobj = json_decode($lfile);
            if ($lobj === null){
                $error_message .= "Could not parse database '$db'";
            }else{
                //Looks like a JSON object
                $rdb[$db] = $lobj; //add new database to array
    
            }
        }
    }
    //Clean up
    if (isset($lobj)) unset($lobj);
    if (count($rdb) == 0) unset($rdb);
    if (strlen($error_message) == 0) unset($error_message);
}


?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=0.5" />
    <title><?= htmlspecialchars($pagetitle) ?> &ndash; Critterpedia</title>
    <meta name="description" content="A site containing lists and details on the critters and more in Animal Crossing: New Horizons" />
    <link rel="stylesheet" href="/style/main.css" />



    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png" />
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png" />
    <link rel="manifest" href="/site.webmanifest" />
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5" />
    <meta name="apple-mobile-web-app-title" content="Critterpedia" />
    <meta name="application-name" content="Critterpedia" />
    <meta name="msapplication-TileColor" content="#da532c" />
    <meta name="theme-color" content="#ffffff" />


</head>
<body>
    <header>
        <h1><a href="/">Critterpedia</a></h1>
        <div>
            <div class="alert">
                This site is under construction and may not work properly.
            </div>
        </div>
        <?php
            if (isset($error_message)){
                echo '<div class="alert">' . htmlspecialchars($error_message) . '</div>';
            }
        ?>
    </header>
    <article>
        <h2><?= htmlspecialchars($pagetitle) ?></h2>