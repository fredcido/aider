<?php
require __DIR__.'/vendor/autoload.php';

use Guzzle\Http\Client;

// create our http client (Guzzle)
$client = new Client('http://localhost:8000', array(
    'request.options' => array(
        'exceptions' => false,
    )
));

//$a = array(
//    "one" => 1,
//    "two" => 2,
//    "three" => 3,
//    "seventeen" => 17
//);

$a['A'] = array(
    array(
        0 => "name",
        1 => "logo_url"
    ),
    array(
        0 => "Herbert Smith Freehills",
        1 => "wwww.something"
    ),
    Array
    (
        0 => "Ashursts",
        1 => ""
    )


);
var_dump($a); die();

foreach ($a as $v){
    print_r( $v['name']); echo'\n';
}

//foreach ($a as list ($v,$w)) {
//
//    echo '$entity->set'.$v.'('.$w.')';
//    //echo "A: $v; B: $$w\n";
//}

echo "\n\n";
