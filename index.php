<?php
include_once './creds.php';
include_once './CurlFunctions.php';

session_start();


if (!isset($_SESSION['clientSession'])) {
    $_SESSION['clientSession'] = CurlFunctions::login(HOST, USERNAME, PASSWORD);
}


$dhcp = getDhcp($_SESSION['clientSession']);
$static = getStatic($_SESSION['clientSession']);


printStatic($static);
printNonStatic($dhcp);


function printNonStatic($dhcp) {
    ?>
    <h3>Non-Static Mappings</h3>
    <table>
        <?php
        $data = getNonStatic($dhcp);

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


function printStatic($static) {
    ?>
    <h3>Static Mappings</h3>
    <table>
        <?php
        $data = getStaticMapping($static);

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
    $data = json_decode(CurlFunctions::get('https://router/api/edge/data.json?data=dhcp_leases', $session));
    if (!$data->success) {
        return false;
    }
    return $data;
}

function getStatic($session) {
    $data = json_decode(CurlFunctions::get('https://router/api/edge/get.json', $session));
    if (!$data->success) {
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
