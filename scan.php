<?php

/*----------------------------------------------------------------------
# Name        : PleskScan
# Version     : 0.1
# Author      : Kreshnik Hasanaj
# Mail        : kreshnik.hasanaj@gmail.com
# WebPage     : http://selftaughtgeek.info
# Purpose     : Scan url for Plesk Panels
# Usage       : At your own risk  (This is for learning purpose only)
# Format      : www.page.com or page.com
# Requirements: uncomment line extension=php_openssl.dll in php.ini before using the scanner.
#----------------------------------------------------------------------*/

error_reporting(0);
$doc = new DOMDocument();
$defaultPorts = array("http://" => 8880, "https://" => 8443);
$headerPatterns = array("/X-Powered-By-Plesk: Plesk/i", "/X-Powered-By: PleskLin/i", "/X-Plesk: PSA-Key/i");
$loginFiles = array('login.php3', 'login_up.php3');
stream_context_set_default(
    array(
        'http' => array(
            'timeout' => 10
        )
    )
);
if(isset($_POST['host']) && !empty($_POST['host'])){

$host = clearHost($_POST['host']);

    foreach($defaultPorts as $key => $value){

        foreach($loginFiles as $file){
            try {
            $headers = get_headers(sprintf("%s%s:%d/%s",$key,$host,$value,$file));
            if(count($headers) > 0){
                    foreach($headerPatterns as $pattern){
                        if(count(preg_grep($pattern, $headers)) > 0){

                            @$doc->loadHTML(file_get_contents(sprintf("%s%s:%d/%s",$key,$host,$value,$file)));
                            $title = $doc->getElementsByTagName("title");
                            if($title->length > 0){
                                $title =  $title->item(0)->nodeValue;
                            } else {
                                $title = null;
                            }

                            print json_encode(array('title' => $title, 'host' => sprintf("%s%s:%d/%s",$key,$host,$value,$file), 'file' => $file, 'match' => implode(' ', preg_grep($pattern, $headers))));
                            return;
                        }
                    }
            }
            } catch(Exception $e){
                return 'error';
            }
        }
    }
    print 'none';
}
function clearHost($host){
    if(!is_null($host) && !empty($host)){
    $host = strtolower($host);
    $host = str_replace(array('http://','https://'),'',$host);
    return $host;
    } else {
        return null;
    }
}