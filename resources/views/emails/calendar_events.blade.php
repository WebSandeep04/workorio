<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pending Events + Monthly Summary</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f6f9fc;">

@php
    function getStatusColor($statusName) {
        if (empty($statusName)) {
            return '#ffc107'; // Default: yellow for pending
        }
        $statusLower = strtolower(trim($statusName));
        $colorMap = [
            'pending' => '#ffc107',
            'created' => '#28a745',
            'completed' => '#28a745',
            'done' => '#28a745',
            'missed' => '#dc3545',
            'cancelled' => '#dc3545',
            'canceled' => '#dc3545',
            'in progress' => '#17a2b8',
            'in-progress' => '#17a2b8',
            'on hold' => '#6c757d',
            'on-hold' => '#6c757d',
        ];
        return $colorMap[$statusLower] ?? '#6c757d'; 
    }

    $rangeEvents = $payload['rangeEvents'];
    $monthlyEvents = $payload['monthlyEvents'];
    $totalTomorrow = count($rangeEvents);

    $pendingRows = [];
    foreach ($rangeEvents as $i => $r) {
        $rowBg = ($i % 2 == 0) ? "#ffffff" : "#fafafa";
        
        if (is_null($r['status_id']) || empty($r['status_name'])) {
            $evtDate = $r['event_date'];
            if ($evtDate == $payload['targetDate']) {
                $statusText = "Due Today";
                $statusColor = getStatusColor('pending');
            } elseif ($evtDate > $payload['targetDate']) {
                $statusText = "Upcoming" . ($r['alert_before_days'] > 0 ? " (Alert)" : "");
                $statusColor = '#cff4fc';
            } else {
                $statusText = "Pending";
                $statusColor = getStatusColor('pending');
            }
            $statusBadge = "<span style='background:{$statusColor};color:#212529;padding:3px 8px;border-radius:12px;font-size:11px;font-weight:500;'>$statusText</span>";
        } else {
            $statusName = $r['status_name'];
            $statusColor = getStatusColor($statusName);
            $statusText = htmlspecialchars($statusName, ENT_QUOTES, 'UTF-8');
            $isMissed = (strtolower(trim($statusName)) === 'missed');
            $missedReason = !empty($r['missed_reason_name']) ? htmlspecialchars($r['missed_reason_name'], ENT_QUOTES, 'UTF-8') : '';
            if ($isMissed && $missedReason) {
                $statusText .= "<br><span style='font-size:10px;opacity:0.85;font-weight:400;'>({$missedReason})</span>";
            }
            $textColor = in_array($statusColor, ['#ffc107', '#17a2b8']) ? '#212529' : '#fff';
            $statusBadge = "<span style='background:{$statusColor};color:{$textColor};padding:3px 8px;border-radius:12px;font-size:11px;font-weight:500;display:inline-block;'>$statusText</span>";
        }

        $pendingRows[] = "
            <tr style='background:{$rowBg};'>
                <td style='padding:10px 12px;border-bottom:1px solid #e5e5e5;font-weight:600;color:#0f172a;font-size:13px;'>" . htmlspecialchars($r['item_name'], ENT_QUOTES, 'UTF-8') . "</td>
                <td style='padding:10px 12px;border-bottom:1px solid #e5e5e5;color:#334155;font-size:13px;'>" . htmlspecialchars($r['client_name'], ENT_QUOTES, 'UTF-8') . "</td>
                <td style='padding:10px 12px;text-align:center;border-bottom:1px solid #e5e5e5;color:#64748b;font-size:12px;'>" . htmlspecialchars($r['event_date'], ENT_QUOTES, 'UTF-8') . "</td>
                <td style='padding:10px 12px;text-align:center;border-bottom:1px solid #e5e5e5;'>" . $statusBadge . "</td>
            </tr>";
    }

    $half = (int)ceil(count($pendingRows) / 2);
    $firstHalf = array_slice($pendingRows, 0, $half);
    $secondHalf = array_slice($pendingRows, $half);

    $renderTable = function($rows) {
        if (empty($rows)) {
            return "<div style='flex:1;margin:0 12px;min-width:280px;'></div>";
        }
        $tableHead = "<thead>
                <tr style='background:#f8fafc;border-bottom:2px solid #e2e8f0;'>
                  <th style='padding:12px;text-align:left;font-weight:700;color:#334155;font-size:12px;'>Event Name</th>
                  <th style='padding:12px;text-align:left;font-weight:700;color:#334155;font-size:12px;'>Client</th>
                  <th style='padding:12px;text-align:center;font-weight:700;color:#334155;font-size:12px;width:120px;'>Date</th>
                  <th style='padding:12px;text-align:center;font-weight:700;color:#334155;font-size:12px;width:140px;'>Status</th>
                </tr>
              </thead>";
        return "<table width='100%' cellpadding='0' cellspacing='0' style='border-collapse:collapse;font-size:13px;border:1px solid #eef2f7;border-radius:10px;overflow:hidden;'>
              " . $tableHead . "
              <tbody>" . implode('', $rows) . "</tbody>
            </table>";
    };

    if ($totalTomorrow > 0) {
        $rowsHtml = "<table width='100%' cellpadding='0' cellspacing='12' style='table-layout:fixed; width: 100%; min-width: 600px;'>
            <tr>
                <td valign='top' style='width:50%;padding-right:6px;'>" . $renderTable($firstHalf) . "</td>
                <td valign='top' style='width:50%;padding-left:6px;'>" . $renderTable($secondHalf) . "</td>
            </tr>
        </table>";
    } else {
        $rowsHtml = "<div style='padding:20px;text-align:center;color:#6c757d;font-size:13px;background:#f8fafc;border-radius:8px;'>No pending items from " . htmlspecialchars($payload['startDate'], ENT_QUOTES, 'UTF-8') . " up to " . htmlspecialchars($payload['endDate'], ENT_QUOTES, 'UTF-8') . ".</div>";
    }

    // Monthly Logic
    $monthlyByClient = [];
    foreach ($monthlyEvents as $r) {
        $monthlyByClient[$r['client_name']][] = $r;
    }

    $monthlySummaryHtml = "";
    if (!empty($monthlyByClient)) {
        $dates = [];
        $periodStart = new DateTime($payload['monthStart']);
        $periodEnd = (new DateTime($payload['monthEnd']))->modify('+1 day');
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($periodStart, $interval, $periodEnd);
        foreach ($period as $dt) {
            $dates[] = $dt->format('Y-m-d');
        }

        $monthlySummaryHtml .= "<div style='overflow-x:auto; width: 100%; -webkit-overflow-scrolling: touch;'>
            <table style='width:100%; min-width: 600px; border-collapse:collapse;font-size:11px;border:1px solid #dde6f6;'>
                <thead>
                    <tr style='background:#f1f5ff;border-bottom:1px solid #d6e4ff;'>
                        <th style='padding:10px 12px;text-align:left;font-weight:700;color:#334155;min-width:140px;'>Client</th>";
        foreach ($dates as $date) {
            $dayNum = date('d', strtotime($date));
            $monthlySummaryHtml .= "<th style='padding:8px 6px;text-align:center;font-weight:600;color:#64748b;border-left:1px solid #d6e4ff;min-width:40px;'>$dayNum</th>";
        }
        $monthlySummaryHtml .= "</tr>
                </thead>
                <tbody>";

        foreach ($monthlyByClient as $clientName => $events) {
            $eventsByDate = [];
            foreach ($events as $event) {
                $eventsByDate[$event['event_date']][] = $event;
            }

            $monthlySummaryHtml .= "<tr style='background:#ffffff;border-bottom:1px solid #e6efff;'>
                <td style='padding:8px 10px;font-weight:600;color:#1f2a44;border-right:1px solid #d6e4ff;'>" . htmlspecialchars($clientName, ENT_QUOTES, 'UTF-8') . "</td>";

            foreach ($dates as $date) {
                if (isset($eventsByDate[$date])) {
                    $cellContent = "";
                    foreach ($eventsByDate[$date] as $event) {
                        $isPending = is_null($event['status_id']) || empty($event['status_name']);
                        
                        if ($isPending) {
                            $badgeColor = getStatusColor('pending');
                            $evtDate = $event['event_date'];
                            if ($evtDate == $payload['targetDate']) {
                                $statusText = "Due Today";
                                $badgeColor = getStatusColor('pending');
                            } elseif ($evtDate > $payload['targetDate']) {
                                $statusText = "Upcoming" . ($event['alert_before_days'] > 0 ? " (Alert)" : "");
                                $badgeColor = '#0dcaf0'; 
                            } else {
                                $statusText = "Pending";
                                $badgeColor = getStatusColor('pending');
                            }
                        } else {
                            $statusName = $event['status_name'];
                            $badgeColor = getStatusColor($statusName);
                            $statusText = htmlspecialchars($statusName, ENT_QUOTES, 'UTF-8');
                            $isMissed = (strtolower(trim($statusName)) === 'missed');
                            $missedReason = !empty($event['missed_reason_name']) ? htmlspecialchars($event['missed_reason_name'], ENT_QUOTES, 'UTF-8') : '';
                            if ($isMissed && $missedReason) {
                                $statusText .= "<br><span style='font-size:9px;opacity:0.85;'>({$missedReason})</span>";
                            }
                        }
                        $cellContent .= "<div style='margin:2px 0;padding:4px 6px;border-radius:8px;font-size:10px;background:" . $badgeColor . "1a;color:" . $badgeColor . ";font-weight:500;'>" . $statusText . "</div>";
                    }
                    $monthlySummaryHtml .= "<td style='padding:6px;border-left:1px solid #dbe4f7;vertical-align:top;background:#fbfdff;'>$cellContent</td>";
                } else {
                    $monthlySummaryHtml .= "<td style='padding:6px;text-align:center;color:#c1ccde;font-size:10px;border-left:1px solid #dbe4f7;'>—</td>";
                }
            }
            $monthlySummaryHtml .= "</tr>";
        }
        $monthlySummaryHtml .= "</tbody></table></div>";
    } else {
        $monthlySummaryHtml = "<div style='padding:20px;text-align:center;color:#6c757d;font-size:13px;background:#f8fafc;border-radius:8px;'>No events found for " . htmlspecialchars($payload['currentMonth'], ENT_QUOTES, 'UTF-8') . ".</div>";
    }

@endphp

    <table width="100%" cellspacing="0" cellpadding="0" style="background:#f6f9fc;padding:24px 0;font-family:Arial,Helvetica,sans-serif;">
        <tr>
            <td align="center">
                <table width="94%" cellpadding="0" cellspacing="0" style="max-width:1400px;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 8px 24px rgba(15,23,42,.08);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #0d6efd; color:white; padding:22px 24px;">
                            <div style="font-size:16px;font-weight:bold;">Pending Events — {{ $payload['startDate'] }} to {{ $payload['endDate'] }}</div>
                        </td>
                    </tr>

                    <!-- Summary Badge -->
                    <tr>
                        <td style="padding:16px 24px 8px 24px;background:#ffffff;">
                            <div style="display:inline-block;background:#e7f1ff;color:#0d6efd;border:1px solid #cfe2ff;border-radius:10px;padding:6px 12px;font-size:12px;font-weight:600;">
                                Total: {{ $totalTomorrow }} pending items up to {{ $payload['endDate'] }}
                            </div>
                        </td>
                    </tr>

                    <!-- Tomorrow's Table -->
                    <tr>
                        <td style="padding:8px 24px 20px 24px;background:#ffffff;">
                            <div style="overflow-x: auto; width: 100%; -webkit-overflow-scrolling: touch;">
                                {!! $rowsHtml !!}
                            </div>
                        </td>
                    </tr>

                    <!-- Monthly Summary Section -->
                    <tr>
                        <td style="padding:20px 24px;background:#f8fafc;border-top:2px solid #e2e8f0;">
                            <div style="font-size:18px;font-weight:700;color:#334155;margin-bottom:16px;">
                                📅 Monthly Summary - {{ $payload['currentMonth'] }}
                            </div>
                            {!! $monthlySummaryHtml !!}
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding:14px 24px;background:#ffffff;color:#64748b;font-size:12px;border-top:1px solid #eef2f7;text-align:center;">
                            © {{ date('Y') }} Workorio • Automated notification
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>
