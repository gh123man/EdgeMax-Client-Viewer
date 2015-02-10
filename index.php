<html>

<head>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
    <style>
        .hightlight {
            background: rgba(247, 250, 80, 0.64);
        }
        .hover:hover {
            background: #F0F0F0;
        }
        tr, td {
            padding: 5px;
            border: 0px;
        }
        body {
            font-family: 'Open Sans', sans-serif;
        }
        table {border: none;}
    </style>
</head>

<body>
<?php
include_once './creds.php';
include_once './CurlFunctions.php';

session_start();

if (!isset($_SESSION['clientSession'])) {
    $_SESSION['clientSession'] = CurlFunctions::login(HOST, USERNAME, PASSWORD);
}

$dhcp = getDhcp($_SESSION['clientSession']);
$static = getStatic($_SESSION['clientSession']);

if (!$dhcp || !$static) {
    //renew session
    $_SESSION['clientSession'] = CurlFunctions::login(HOST, USERNAME, PASSWORD);
    $dhcp = getDhcp($_SESSION['clientSession']);
    $static = getStatic($_SESSION['clientSession']);
}

$staticData = getStaticMapping($static);
$nonStaticData = getNonStatic($dhcp);


printStatic($staticData);
printNonStatic($nonStaticData);


function printNonStatic($data) {
    ?>
    <h3>DHCP Addresses</h3>
    <table cellspacing="0" cellpadding="0">
        <tr>
            <th></th>
            <th>Computer Name</th>
            <th>IP</th>
        </tr>
        <?php

        foreach ($data as $ip => $data) {
            $name = $data->{'client-hostname'};

            drawYouCheck($ip);

            echo '<td>';
            echo $name;
            echo '</td>';

            echo '<td>';
            echo $ip;
            echo '</td>';

            echo '</tr>';
        }
        ?>
    </table>
    <?php
}


function printStatic($data) {
    ?>

    <h3>Static Mappings</h3>
    <table cellspacing="0" cellpadding="0">
        <tr>
            <th></th>
            <th>Computer Name</th>
            <th>IP</th>
        </tr>
        <?php

        foreach ($data as $name => $data) {
            $ip = $data->{'ip-address'};

            drawYouCheck($ip);

            echo '<td>';
            echo $name;
            echo '</td>';

            echo '<td>';
            echo $ip;
            echo '</td>';

            echo '</tr>';
        }
        ?>
    </table>
    <?php
}

function drawYouCheck($ip) {
    if ($ip == $_SERVER['REMOTE_ADDR']) {
        echo '<tr class="hightlight hover">';
        echo '<td>YOU</td>';
    } else {
        echo '<tr class="hover">';
        echo '<td></td>';
    }
}

function getDhcp($session) {
    $data = json_decode(CurlFunctions::get(HOST . 'api/edge/data.json?data=dhcp_leases', $session));
    if (!is_object($data) || !$data->success) {
        return false;
    }
    return $data;
}

function getStatic($session) {
    $data = json_decode(CurlFunctions::get(HOST . 'api/edge/get.json', $session));
    if (!is_object($data) || !$data->success) {
        return false;
    }
    return $data;
}

function getStaticMapping($static) {
    return $static->GET->service->{'dhcp-server'}->{'shared-network-name'}->LAN1->subnet->{'192.168.1.0/24'}->{'static-mapping'};
}

function getNonStatic($dhcp) {
    return $dhcp->output->{'dhcp-server-leases'}->LAN1;
}




?>
</body>
</html>
