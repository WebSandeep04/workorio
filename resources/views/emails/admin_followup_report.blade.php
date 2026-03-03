<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Follow-up Report - {{ $today }}</title>
    <style>
        .email-container {
            max-width: 800px;
            margin: 0 auto;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #1a1a1a;
            padding: 5px;
        }
        
        .header {
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .header p {
            margin: 2px 0 0 0;
            color: #666;
            font-size: 9px;
            text-transform: uppercase;
        }
        
        .summary-section {
            margin-bottom: 20px;
        }
        
        .summary-title, .details-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: #000;
            border-left: 3px solid #000;
            padding: 0 5px;
            margin-bottom: 8px;
        }
        
        .table-container {
            width: 100%;
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        th {
            text-align: left;
            background-color: #f4f4f4;
            border: 1px solid #ddd;
            padding: 2px 4px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            color: #444;
        }
        
        td {
            border: 1px solid #eee;
            padding: 1px 4px;
            font-size: 9px;
            color: #333;
        }
        
        .count-badge {
            font-weight: 700;
            font-size: 9px;
        }
        
        .user-section {
            margin-top: 15px;
            border: 1px solid #eee;
            padding: 0;
        }
        
        .user-header {
            background: #f8f9fa;
            padding: 2px 5px;
            margin: 0;
            font-size: 10px;
            font-weight: 700;
            border-bottom: 1px solid #eee;
        }
        
        .date-header {
            background: #fff;
            color: #666;
            padding: 2px 5px;
            margin: 0;
            font-size: 9px;
            font-weight: 600;
            border-bottom: 1px solid #eee;
        }
        
        .status-badge {
            font-weight: 700;
            font-size: 8px;
            text-transform: uppercase;
        }
        
        .status-completed { color: #1e7e34; }
        .status-rejected { color: #bd2130; }
        .status-pending { color: #856404; }
        .status-default { color: #545b62; }
        
        .footer {
            margin-top: 20px;
            border-top: 1px solid #eee;
            padding-top: 10px;
            font-size: 9px;
            color: #888;
        }
        
        .contact-info {
            margin-top: 5px;
            line-height: 1.4;
        }

        @media (max-width: 600px) {
            .email-container { padding: 2px; }
            td, th { padding: 1px 2px; font-size: 8px; }
        }
    </style>
</head>
<body>
    <div class='email-container'>
        <div class='header'>
            <h1>Follow-up Report</h1>
            <p>Daily Sales Follow-up Summary - {{ $today }}</p>
        </div>
        
        <div class='summary-section'>
            <h2 class='summary-title'>Follow-up Summary - {{ $today }}</h2>
            <div class='table-container'>
                <table class='summary-table'>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th style='text-align: center; width: 120px;'>Pending Follow-ups</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($summary as $user => $count)
                        <tr>
                            <td><strong>{{ $user }}</strong></td>
                            <td style='text-align: center;'><span class='count-badge'>{{ $count }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class='details-section'>
            <h2 class='details-title'>New Leads Added Since Yesterday</h2>
            <div class='table-container'>
                <table class='details-table'>
                    <thead>
                        <tr>
                            <th style='width: 20%;'>Lead Name</th>
                            <th style='width: 15%;'>User</th>
                            <th style='width: 15%;'>Phone</th>
                            <th style='width: 15%;'>Location</th>
                            <th style='width: 15%;'>Status</th>
                            <th style='width: 20%;'>Added At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($newLeads as $nl)
                            @php
                                $nl_date = \Carbon\Carbon::parse($nl->createdat)->format('d M, Y');
                                $nl_user = $nl->user ? $nl->user->name : 'Unknown';
                                $nl_city = $nl->city ? $nl->city->city_name : '';
                                $nl_status = $nl->status ? $nl->status->status_name : 'Unknown';
                            @endphp
                            <tr>
                                <td><strong>{{ $nl->leads_name }}</strong></td>
                                <td>{{ $nl_user }}</td>
                                <td>{{ $nl->contact_number }}</td>
                                <td>{{ $nl_city }}</td>
                                <td><span class='status-badge'>{{ $nl_status }}</span></td>
                                <td>{{ $nl_date }}</td>
                            </tr>
                        @empty
                            <tr><td colspan='6' style='text-align:center; padding:10px;'>No new leads added since yesterday.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class='details-section'>
            <h2 class='details-title'>Detailed Follow-up Report</h2>
            
            @php 
                $groupedRecords = $records->groupBy(function($item) {
                    return $item->user ? $item->user->name : 'Unknown';
                });
            @endphp

            @foreach ($groupedRecords as $userName => $userRecords)
                <div class='user-section'>
                    <h3 class='user-header'>USER: {{ $userName }}</h3>
                    
                    @php 
                        $dateRecords = $userRecords->groupBy(function($item) {
                            return $item->next_follow_up_date ? $item->next_follow_up_date->format('Y-m-d') : '';
                        });
                    @endphp
                    
                    @foreach ($dateRecords as $date => $dayRecords)
                        <h4 class='date-header'>DATE: {{ $date }}</h4>
                        <div class='table-container'>
                            <table class='details-table'>
                                <thead>
                                    <tr>
                                        <th style='width: 18%;'>Lead Name</th>
                                        <th style='width: 15%;'>Contact Person</th>
                                        <th style='width: 12%;'>Phone</th>
                                        <th style='width: 15%;'>Location</th>
                                        <th style='width: 15%;'>Email</th>
                                        <th style='width: 10%;'>Status</th>
                                        <th style='width: 15%;'>Product</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dayRecords as $rec)
                                        @php
                                            $status_name = $rec->status ? $rec->status->status_name : 'Unknown';
                                            $status_class = 'status-default';
                                            if (strtolower($status_name) == 'completed') $status_class = 'status-completed';
                                            if (strtolower($status_name) == 'rejected') $status_class = 'status-rejected';
                                            if (strtolower($status_name) == 'pending') $status_class = 'status-pending';

                                            $city = $rec->city ? $rec->city->city_name : '';
                                            $state = $rec->state ? $rec->state->state_name : '';
                                            $location = trim($city . ', ' . $state, ', ');
                                        @endphp
                                        <tr>
                                            <td><strong>{{ $rec->leads_name }}</strong></td>
                                            <td>{{ $rec->contact_person }}</td>
                                            <td>{{ $rec->contact_number }}</td>
                                            <td>{{ $location }}</td>
                                            <td>{{ $rec->email }}</td>
                                            <td><span class='status-badge {{ $status_class }}'>{{ $status_name }}</span></td>
                                            <td>{{ $rec->product ? $rec->product->product_name : '' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
        
        <div class='footer'>
            <p><strong>Best Regards,</strong><br>HR Team - Triserv360</p>
            <div class='contact-info'>
                <strong>Contact:</strong> sandeep@triserv360.com<br>
                <strong>System:</strong> Workorio Lead Management
            </div>
        </div>
    </div>
</body>
</html>
