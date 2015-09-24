<?php

    if (isset($_REQUEST['A']))
    {
        $array = json_decode($_REQUEST['A']);
        $lim = (int) $array[0];
        $dS = $array[1];
        $dE = $array[2];

        $date = explode('-', $dS);
        $dS = $date[2].'-'.$date[1].'-'.$date[0]; 
        $dS = $dS." 00:00:00+00";

        $date = explode('-', $dE);
        $dE = $date[2].'-'.$date[1].'-'.$date[0];
        $dE = $dE." 23:59:59+00";
    } else {

        die ("Error");
    }

    include ("connectionConfig.php");

    if (!$db_connection)
    {
        die('Error: Could not connect: ' . pg_last_error());
    }

    $query = "SELECT a.*, m.moebius_status, m.moebius_id FROM alerts as a LEFT JOIN moebiusos as m ON a.id = m.idalert WHERE a.timeraised BETWEEN '$dS' AND '$dE' ORDER BY a.id DESC LIMIT $lim";
    $result = pg_query($query);

    $object = new stdClass();
    $array = array();

    while ($alert = pg_fetch_object($result))
    {
        $data = array();

        if ($alert->state == "CLOSED" and $alert->moebius_status != 4)
        {
            ignoreClosedAlerts($alert);
        }

        if (!$alert->moebius_status or $alert->moebius_status == 4){$b1 = "<a class='btn btn-block btn-success generar' id=".$alert->id.">Generar</a>";}
            else{$b1 = "";}

        if (!$alert->moebius_status){$b2 = "<a class='btn btn-block btn-warning ignorar' id=".$alert->id.">Ignorar</a>";}
            else{$b2 = "";}

        array_push(
            $data, 
            $alert->id,
            getNodeName($alert),
            $alert->message,
            "<a class='btn btn-link' href=history.php?a=".$alert->id." id=".$alert->id.">Historia</a>",
            $alert->object,
            $alert->timeraised,
            ucfirst(strtolower($alert->severity)),
            ucfirst(strtolower($alert->state)),
            moebiusStatus($alert->moebius_status),
            $alert->moebius_id,
            $b1,
            $b2
        );

        array_push($array, $data);
    }

    $object->data = $array;

    echo json_encode($object);

    function ignoreClosedAlerts($alert)
    {
        $id = $alert->id;
        $query = "";

        if (!$alert->moebius_status)
        {
            $query = "INSERT INTO moebiusos(idalert, moebius_status, moebius_timeraised) VALUES ($id, 4, NOW())";
        } elseif ($alert->moebius_status != 2) {
            $query = "UPDATE moebiusos SET moebius_status=4, moebius_timeraised=NOW() WHERE idalert=$id";
        }

        if ($query != ""){
            pg_query($query);
        }
    }

    function moebiusStatus($status)
    {
        if (!$status)
        {
            return "Orden no generada";
        } else
        {
            switch ($status)
            {
                case 2: return "Orden generada"; break;
                case 3: return "Orden completada"; break;
                case 4: return "Alerta ignorada"; break;
            }
        }
    }

    function getNodeName($alert)
    {
        $id = $alert->id;
        $query = "SELECT value FROM alertdata WHERE name = 'objectName' AND alert_id = $id";
        $result = pg_query($query);
        while ($alert = pg_fetch_object($result))
        {
            if ($alert->value)
            {
                return $alert->value;
            }
        }
    }

    function getHistory($alert)
    {

    }
?>