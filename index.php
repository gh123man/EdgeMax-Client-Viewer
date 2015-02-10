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
    <h3>Non-Static Mappings</h3>
    <table>
        <?php

        foreach ($data as $ip => $data) {
            $name = $data->{'client-hostname'};
            echo '<tr>';

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
    <table>
        <?php

        foreach ($data as $name => $data) {
            $ip = $data->{'ip-address'};
            echo '<tr>';

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
