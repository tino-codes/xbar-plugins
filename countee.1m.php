#!/usr/bin/php
<?php
#  <xbar.title>Impftermine Sachsen</xbar.title>
#  <xbar.version>v1.0</xbar.version>
#  <xbar.author>Tino</xbar.author>
#  <xbar.author.github>tino-codes</xbar.author.github>
#  <xbar.desc>Shows free COVID19 vaccination appointments in Saxony's vaccination centers.</xbar.desc>
#  <xbar.image>https://raw.githubusercontent.com/tino-codes/xbar-plugins/main/countee.png</xbar.image>
#  <xbar.dependencies>php</xbar.dependencies>
#  <xbar.abouturl>https://github.com/tino-codes/xbar-plugins</xbar.abouturl>
#  <xbar.var>select(VAR_IMPFZENTRUM="Dresden"): The vaccination center to be displayed directly in the status bar. [Annaberg, Belgern, Borna, Chemnitz, Dresden, Eich, Grimma, Kamenz, Dippoldiswalde, Leipzig, Lรถbau, Mittweida, Pirna, Plauen, Riesa, Zwickau]</xbar.var>


setlocale(LC_TIME, 'de_DE.UTF-8');
$dataUrl  = 'https://www.startupuniverse.ch/api/1.1/de/counters/getAll/_iz_sachsen?cached=impfee';
$json     = file_get_contents($dataUrl);
$statusIZ = getenv('VAR_IMPFZENTRUM');
$darkmode = getenv('XBARDarkMode') === 'true';

$green = '#006400';

if ($darkmode) {
    $green = '#90ee90';
}

if (!$json) {
    echo '๐';
    exit;
}

$data = json_decode($json, true);

if (!$data || !is_array($data)) {
    echo '๐';
    exit;
}

$details = '';

foreach ($data['response']['data'] as $iz) {
    $details .= $iz['name'] . ': ' . $iz['counteritems'][0]['val'];
    if ($iz['counteritems'][0]['val'] > 0) {
        $details .= ' | color=' . $green;
    }
    $details .= PHP_EOL;

    $timeslots = json_decode($iz['counteritems'][0]['val_s'], true);

    if (is_array($timeslots)) {
        foreach ($timeslots as $timeslot) {
            $details .= '-- ' . date('d.m.', $timeslot['d']) . ': ' . $timeslot['c'];
            if ($timeslot['c'] > 0) {
                $details .= ' | color=' . $green;
            }
            $details .= PHP_EOL;
        }
    }

    if (strpos($iz['name'], $statusIZ) !== false) {
        if ($iz['counteritems'][0]['val'] === 0) {
            echo '๐ด ';
        } else {
            echo '๐ข ' . $iz['counteritems'][0]['val'];
        }

        echo PHP_EOL . '---' . PHP_EOL;
    }
}

echo $details;

echo '---' . PHP_EOL;
echo 'Terminvergabe | href=https://sachsen.impfterminvergabe.de/' . PHP_EOL;
echo 'Countee รbersicht | href=https://www.countee.ch/app/de/counter/impfee/_iz_sachsen' . PHP_EOL;
