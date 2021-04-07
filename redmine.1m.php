#!/usr/bin/php
<?php
#  <xbar.title>Redmine Tickets</xbar.title>
#  <xbar.version>v1.3</xbar.version>
#  <xbar.author>Tino</xbar.author>
#  <xbar.author.github>tino-codes</xbar.author.github>
#  <xbar.desc>Show your Redmine tickets, grouped by project, sort by status</xbar.desc>
#  <xbar.image>https://upload.wikimedia.org/wikipedia/commons/thumb/3/3f/Redmine_logo.svg/500px-Redmine_logo.svg.png</xbar.image>
#  <xbar.dependencies>php</xbar.dependencies>
#  <xbar.abouturl>https://github.com/tino-codes/xbar-plugins</xbar.abouturl>
#  <xbar.var>string(VAR_URL="https://www.your-redmine.com/"): Redmine url (e.g. https://www.your-redmine.com/)</xbar.var>
#  <xbar.var>string(VAR_TOKEN="abcdef1234567890"): Your API token (see https://www.your-redmine.com/my/api_key)</xbar.var>

$redmineUrl = getenv('VAR_URL');

if (substr($redmineUrl, -1) !== '/') {
    $redmineUrl .= '/';
}

$startUrl     = $redmineUrl . 'issues';
$redmineToken = getenv('VAR_TOKEN');
$ticketsUrl   = $redmineUrl . 'issues.json?key=' . $redmineToken . '&limit=100&status_id=open&assigned_to_id=me';
$json         = file_get_contents($ticketsUrl);

if (!$json) {
    echo '🆘';
    exit;
}

$data = json_decode($json, true);

if (!$data || !is_array($data)) {
    echo '🆘';
    exit;
}

$projects = [];
$issues   = [];
$statusIds = [];
$statusColor = [
  'Neu' => '#ff7f7f',
  'In Bearbeitung' => '#FFFF99'
];
$ticketCount = [];
$logo = 'iVBORw0KGgoAAAANSUhEUgAAABkAAAAZCAQAAABu4E3oAAAACXBIWXMAAAsTAAALEwEAmpwYAAAGw2lUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNi4wLWMwMDUgNzkuMTY0NTkwLCAyMDIwLzEyLzA5LTExOjU3OjQ0ICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOmRjPSJodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLyIgeG1sbnM6cGhvdG9zaG9wPSJodHRwOi8vbnMuYWRvYmUuY29tL3Bob3Rvc2hvcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgMjIuMSAoTWFjaW50b3NoKSIgeG1wOkNyZWF0ZURhdGU9IjIwMjEtMDMtMjdUMjI6MjI6MjcrMDE6MDAiIHhtcDpNb2RpZnlEYXRlPSIyMDIxLTAzLTI3VDIyOjM0OjI2KzAxOjAwIiB4bXA6TWV0YWRhdGFEYXRlPSIyMDIxLTAzLTI3VDIyOjM0OjI2KzAxOjAwIiBkYzpmb3JtYXQ9ImltYWdlL3BuZyIgcGhvdG9zaG9wOkNvbG9yTW9kZT0iMSIgcGhvdG9zaG9wOklDQ1Byb2ZpbGU9IkdyYXkgR2FtbWEgMi4yIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOmEwNzg5YTdkLTQwNTgtNDEzOC04ZGQ1LTdiNzA0Yzk0YTIxZCIgeG1wTU06RG9jdW1lbnRJRD0iYWRvYmU6ZG9jaWQ6cGhvdG9zaG9wOjM2MmQ2YmRiLTcyZWMtYzY0Ny1iNDNhLWYzY2YzMjI5NDViYyIgeG1wTU06T3JpZ2luYWxEb2N1bWVudElEPSJ4bXAuZGlkOmI3NDRhZjE4LTQ1YTQtNGQ0Ny1iYWFhLWZjYWU5MmE5ODc0YSI+IDx4bXBNTTpIaXN0b3J5PiA8cmRmOlNlcT4gPHJkZjpsaSBzdEV2dDphY3Rpb249ImNyZWF0ZWQiIHN0RXZ0Omluc3RhbmNlSUQ9InhtcC5paWQ6Yjc0NGFmMTgtNDVhNC00ZDQ3LWJhYWEtZmNhZTkyYTk4NzRhIiBzdEV2dDp3aGVuPSIyMDIxLTAzLTI3VDIyOjIyOjI3KzAxOjAwIiBzdEV2dDpzb2Z0d2FyZUFnZW50PSJBZG9iZSBQaG90b3Nob3AgMjIuMSAoTWFjaW50b3NoKSIvPiA8cmRmOmxpIHN0RXZ0OmFjdGlvbj0ic2F2ZWQiIHN0RXZ0Omluc3RhbmNlSUQ9InhtcC5paWQ6MGE4NTE5YTYtMDU4Ni00OWIzLThlNzAtZjcwMzc4NjM4MGQ1IiBzdEV2dDp3aGVuPSIyMDIxLTAzLTI3VDIyOjM0OjIwKzAxOjAwIiBzdEV2dDpzb2Z0d2FyZUFnZW50PSJBZG9iZSBQaG90b3Nob3AgMjIuMSAoTWFjaW50b3NoKSIgc3RFdnQ6Y2hhbmdlZD0iLyIvPiA8cmRmOmxpIHN0RXZ0OmFjdGlvbj0ic2F2ZWQiIHN0RXZ0Omluc3RhbmNlSUQ9InhtcC5paWQ6YTA3ODlhN2QtNDA1OC00MTM4LThkZDUtN2I3MDRjOTRhMjFkIiBzdEV2dDp3aGVuPSIyMDIxLTAzLTI3VDIyOjM0OjI2KzAxOjAwIiBzdEV2dDpzb2Z0d2FyZUFnZW50PSJBZG9iZSBQaG90b3Nob3AgMjIuMSAoTWFjaW50b3NoKSIgc3RFdnQ6Y2hhbmdlZD0iLyIvPiA8L3JkZjpTZXE+IDwveG1wTU06SGlzdG9yeT4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz6CO37WAAAB7ElEQVQ4y82Tv2pVQRCHv/1zdnbP7jn3RrvY5CoKKaJc8gIGxZdImjRpAumTNmARK7nR2Aoi+gbBoDaaKAghr6AogqIWSggox+Js7j2tTZCvWWbmN/tjZ5aGf4WzkfQyfXoIDovuq0W1rR6qe3pR9w0WS+jQkdQ4PB67OmmpVh0Oi+9Kpsb0cUPzrI7V7ETiZ2Mye3ZYMmF8iMSh+kljXkzhHrQCt1OhX9KoXzIUigz2lDn1uy2UJ72LZkdtmvvpkn2a7/uj5zQtpIycV89zeisRMAgBvdXG9J6dKomtsYqKmvKmmxfMMo1d0QQ8GodQICuqMcsOO+9v1SRSlijznqa4K9ENJGhKAhpB0MTgZ0zUIxrzodYVFUQSfik/6UmcjpREHIaCgCfgpzlp82EpEUEoKXbbkH1VkaiICAZH6z5iX+f8bkAgEikum211TOM3WrcVCUOkIpGokXUadWy27ZWSEs6RZuwFR4FZ8wuTGXfn7a+bNYfDTseZPlTIiEYf6HV7NeAzQqSmytTYa3pd7dO4UQQZmOxUfQlh0rns7EXp1ee2xuzLgHRD/8jjeltMdgGLGWPRB7nt93IB2Rhv7RuDHmM64gL97rRKNgm39Vd1pA75ZB8Jbox08NjHfFSH6kh/K+7w3/79M5H8BXAMUH4s6uYJAAAAAElFTkSuQmCC';
echo  $data['total_count'] .' | templateImage='.$logo . PHP_EOL;
echo '---' . PHP_EOL;
echo 'Redmine | href=' . $startUrl . PHP_EOL;
echo '---' . PHP_EOL;


foreach ($data['issues'] as $issue) {
    $projects[$issue['project']['id']] = $issue['project']['name'];

    if (!isset($ticketCount[$issue['project']['id']])) {
      $ticketCount[$issue['project']['id']]= 0;
    }

    $ticketCount[$issue['project']['id']]++;


    $issues[$issue['project']['id']][$issue['status']['name']][$issue['id']] =
        '#' . $issue['id'] . ' ' . $issue['subject'] .
        ' (' . $issue['tracker']['name'] . ' / '.$issue['status']['name'].')';

    $statusIds[$issue['status']['name']] = $issue['status']['id'];
}

foreach ($projects as $projectId => $projectName) {
    echo $projectName . ' ('.$ticketCount[$projectId].') | href=' . $redmineUrl . 'projects/' . $projectId . PHP_EOL;

    uksort(
      $issues[$projectId],
      function($a, $b) use ($statusIds) {
        if ($statusIds[$a] == $statusIds[$b]) {
            return 0;
        }
        return ($statusIds[$a] < $statusIds[$b]) ? -1 : 1;
      }
    );

    foreach ($issues[$projectId] as $status => $issueList) {
        foreach ($issueList as $issueId => $issue) {
            echo  $issue . ' | href=' . $redmineUrl . 'issues/' . $issueId;
            echo ' color=' . ($statusColor[$status] ?? '#90ee90');
            echo PHP_EOL;
        }
    }
    echo '---' . PHP_EOL;
}
