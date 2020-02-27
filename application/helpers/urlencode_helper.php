<?php

/*
 * This function creates encoded urls
 * 
 */
if (!function_exists('encode_url')) {
    function encode_url($userId) {
        $new_Id = $userId + 11;
        $newId = $new_Id * 9;
        $random_string = generateRandomString();
        $random_nums = genRandNums();
        $randNums = genRandNums();
        $randString = genRandString();
        $new_url = $random_string . $randNums . $newId . $random_nums . $randString;
        return $new_url;
    }
}

if (!function_exists('generateRandomString')) {
    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
}

if (!function_exists('genRandNums')) {
    function genRandNums($length = 5) {
        $characters = '0123456789';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
}

if (!function_exists('genRandString')) {
    function genRandString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
}

if (!function_exists('decode_uri')) {
    function decode_uri($encoded_uri) {
        if (strlen($encoded_uri)<33)
        {
            return FALSE;
        }
        else
        {
            $substrig = substr($encoded_uri, 15, -15);
            $substrig1 = ($substrig / 9) - 11;
            return $substrig1;
        }
    }
}

if (!function_exists('encode_serial')) {
    function encode_serial($userId) {
       $hash =  $userId * 369852147;
       $serialNum = substr($hash,-9); //get a 9 digit random hash
       return $serialNum;
    }
}

?>
