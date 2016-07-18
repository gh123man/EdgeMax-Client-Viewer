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
            padding: 8px;
            border: 0px;
        }
        body {
            font-family: 'Open Sans', sans-serif;
            background: #F5F5F5;
        }
        table {border: none;}
        .panel {
            float: left;
            margin: 40px;
            padding: 40px;
        }
        .mac {
            color: #444;
            font-size: 9pt;
        }
        h3 {
            font-size: 13pt;
        }

        input {
            border: none;
            border-color: transparent;
            font-size: 14pt;
            width: 150px;
            background: rgba(0, 0, 0, 0);
        }
        .card {
            box-shadow: 0px 1px 5px rgba(0, 0, 0, 0.30);
            border-radius: 2px;
            background: #FFF;
        }
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


$dhcpClients = getDHCPLans($dhcp);
$staticClients = getStaticLans($static);

foreach ($staticClients as $lan => $clients) {
?>

    <div class="panel card">
        <div class="title"><?php echo $lan; ?></div>
<?php
        printStatic($clients[0]);
        printNonStatic($dhcpClients->{$lan});
?>

    </div>
    
<?php
}

//printStatic($staticData);
//printNonStatic($nonStaticData);


function printNonStatic($data) {
    ?>
    <h3>DHCP Addresses</h3>
    <table cellspacing="0" cellpadding="0">
        <tr>
            <th>Computer Name</th>
            <th>IP</th>
        </tr>
        <?php

        foreach ($data as $ip => $data) {
            $name = $data->{'client-hostname'};
            $mac = $data->{'mac'};

            tr($ip, function() use ($name, $ip, $mac) {

?>
                <td>
                    <div>
                    <div><?php echo $name?><div>
                    <div class="mac"><?php echo $mac?></div>
                    <div>

                </td>
                <td><input onClick="this.select();" value="<?php echo $ip?>" readonly></td> 
<?php
            });
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
            <th>Computer Name</th>
            <th>IP</th>
        </tr>
        <?php

        foreach ($data as $name => $data) {
            $ip = $data->{'ip-address'};
            $mac = $data->{'mac-address'};

            tr($ip, function() use ($name, $ip, $mac) {

?>
                <td>
                    <div>
                    <div><?php echo $name?><div>
                    <div class="mac"><?php echo $mac?></div>
                    <div>

                </td>
                <td><input onClick="this.select();" value="<?php echo $ip?>" readonly></td> 
<?php
            });
        }
        ?>
    </table>
    <?php
}

function tr($ip, $body) {
    if ($ip == $_SERVER['REMOTE_ADDR']) {
        echo '<tr class="hightlight hover">';
    } else {
        echo '<tr class="hover">';
    }
    $body();
    echo "</tr>";
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

function getDHCPLans($obj) {
    return $obj->output->{'dhcp-server-leases'};
}

function getStaticLans($obj) {
    $out = array();
    foreach ($obj->GET->service->{'dhcp-server'}->{'shared-network-name'} as $lan => $network) {
        foreach ($network->subnet as $range => $subnet) {
            $out[$lan][] = $subnet->{'static-mapping'};
        }
    }
    return $out;
    
}

function getStaticMapping($static) {
    /*
    $subnet = array();
    $lans = $static->GET->service->{'dhcp-server'}->{'shared-network-name'};
    foreach ($lans as $lan) {
        foreach ($lan as $subnet) {
            
        }
    }
     */
    return $static->GET->service->{'dhcp-server'}->{'shared-network-name'}->LAN1->subnet->{'192.168.1.0/24'}->{'static-mapping'};
}

function getNonStatic($dhcp) {
    return $dhcp->output->{'dhcp-server-leases'}->LAN1;
}




?>
</body>
</html>
