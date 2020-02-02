<?php

// curl function
//$urls - array of urls to request
function multi_curl($urls, $mailcow_api_key) {
    // for curl handlers
    $curl_handlers = array();
    //for storing contents
    $content = array();
    //setting curl handlers
    foreach ($urls as $url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-API-Key: $mailcow_api_key"));
        $curl_handlers[] = $curl;
    }
    //initiating multi handler
    $multi_curl_handler = curl_multi_init();
    // adding all the single handler to a multi handler
    foreach($curl_handlers as $key => $curl) {
        curl_multi_add_handle($multi_curl_handler,$curl);
    }
    // executing the multi handler
    do {
        $multi_curl = curl_multi_exec($multi_curl_handler, $active);
    } 
    while ($multi_curl == CURLM_CALL_MULTI_PERFORM  || $active);
    foreach($curl_handlers as $curl) {
        //checking for errors
        if(curl_errno($curl) == CURLE_OK) {
            //if no error then getting content
            $content[] = curl_multi_getcontent($curl);
            //parsing content
        }
        else {
            //storing error
            $content[] = curl_error($curl);
        }
    }
    curl_multi_close($multi_curl_handler);
    return $content;
}

?>
