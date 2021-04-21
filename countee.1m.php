#!/usr/bin/php
<?php
#  <xbar.title>Impftermine Sachsen</xbar.title>
#  <xbar.version>v1.0</xbar.version>
#  <xbar.author>Tino</xbar.author>
#  <xbar.author.github>tino-codes</xbar.author.github>
#  <xbar.desc>Zeigt freie Impftermine in Sachsen</xbar.desc>
#  <xbar.image>https://raw.githubusercontent.com/tino-codes/xbar-plugins/main/countee.png</xbar.image>
#  <xbar.dependencies>php</xbar.dependencies>
#  <xbar.abouturl>https://github.com/tino-codes/xbar-plugins</xbar.abouturl>
#  <xbar.var>string(VAR_IZ="Dresden"): Das Impfzentrum, welches direkt in der Statusbar angezeigt werden soll.</xbar.var>

setlocale(LC_TIME, 'de_DE.UTF-8');
$dataUrl  = 'https://www.startupuniverse.ch/api/1.1/de/counters/getAll/_iz_sachsen?cached=impfee';
$json     = file_get_contents($dataUrl);
$statusIZ = getenv('VAR_IZ');

if (!$json) {
    echo '🆘';
    exit;
}

$data = json_decode($json, true);

if (!$data || !is_array($data)) {
    echo '🆘';
    exit;
}

$details = '';

foreach ($data['response']['data'] as $iz) {
    $details .= $iz['name'] . ': ' . $iz['counteritems'][0]['val'];
    if ($iz['counteritems'][0]['val'] > 0) {
        $details .= ' | color=#90ee90';
    }
    $details .= PHP_EOL;

    $timeslots = json_decode($iz['counteritems'][0]['val_s'], true);

    if (is_array($timeslots)) {
        foreach ($timeslots as $timeslot) {
            $details .= '-- ' . date('d.m.', $timeslot['d']) . ': ' . $timeslot['c'];
            if ($timeslot['c'] > 0) {
                $details .= ' | color=green';
            }
            $details .= PHP_EOL;
        }
    }

    if (strpos($iz['name'], $statusIZ) !== false) {
        if ($iz['counteritems'][0]['val'] === 0) {
            echo '🔴 ';
        } else {
            echo '🟢 ' . $iz['counteritems'][0]['val'];
        }

        echo PHP_EOL . '---' . PHP_EOL;
    }
}

echo $details;

echo '---' . PHP_EOL;
echo 'Terminvergabe | href=https://sachsen.impfterminvergabe.de/' . PHP_EOL;
