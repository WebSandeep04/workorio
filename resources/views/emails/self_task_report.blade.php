<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0; padding:2px; background-color:#f4f4f4; font-family: Arial, sans-serif;">
    
<div style="width: 100%; max-width: 1400px; margin: 0 auto; background-color: #ffffff; padding: 0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border-radius: 8px; overflow: hidden;">
    <!-- Header -->
    <div style="background-color: #0d6efd; padding:30px 20px; text-align:center;">
        <h1 style="margin:0; color:white; font-size:28px; font-weight:600;">📋 Task Reminder</h1>
        <p style="margin:10px 0 0 0; color:white; font-size:14px;">Workorio Task Management System</p>
    </div>
    
    <!-- Greeting -->
    <div style="padding:20px 15px 10px 15px;">
        <p style="margin:0; font-size:16px; color:#212529;">Hello <strong style="color:#0d6efd;">{{ $payload['userName'] }}</strong>,</p>
        <p style="margin:15px 0; font-size:14px; color:#495057; line-height:1.6;">
            You have <strong style="color:#dc3545;">{{ $payload['totalTasks'] }} pending {{ $payload['totalTasks'] == 1 ? 'task' : 'tasks' }}</strong> assigned to you. 
            Please review and complete them at your earliest convenience.
        </p>
    </div>
    
    <!-- Tasks Table -->
    <div style="padding:0 2px 30px 2px;">
        <div style="overflow-x: auto; width: 100%; -webkit-overflow-scrolling: touch;">
            <table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse; font-family: Arial, sans-serif; box-shadow: 0 2px 4px rgba(0,0,0,0.1); min-width: 600px; white-space: nowrap;">
                <thead style="background-color:#f1f3f5; color:#495057;">
                    <tr>
                        <th style="padding:6px 4px; text-align:center; border:1px solid #dee2e6; font-size:11px; font-weight:600; width:25px;">#</th>
                        <th style="padding:6px 4px; text-align:left; border:1px solid #dee2e6; font-size:11px; font-weight:600; width:18%;">Customer</th>
                        <th style="padding:6px 4px; text-align:left; border:1px solid #dee2e6; font-size:11px; font-weight:600; width:20%;">Task Name</th>
                        <th style="padding:6px 4px; text-align:left; border:1px solid #dee2e6; font-size:11px; font-weight:600; white-space: normal;">Task Description</th>
                        <th style="padding:6px 4px; text-align:center; border:1px solid #dee2e6; font-size:11px; font-weight:600; width:70px;">Priority</th>
                        <th style="padding:6px 4px; text-align:center; border:1px solid #dee2e6; font-size:11px; font-weight:600; width:70px;">Status</th>
                        <th style="padding:6px 4px; text-align:center; border:1px solid #dee2e6; font-size:11px; font-weight:600; width:75px;">Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @php $counter = 1; @endphp
                    @foreach ($payload['tasks'] as $task)
                        @php
                            $rowColor = ($counter % 2 == 0) ? '#f8f9fa' : '#ffffff';
                            
                            $customerDisplay = htmlspecialchars($task['customer_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
                            if (!empty($task['customer_company'])) {
                                $customerDisplay .= "<br><small style='color:#6c757d; font-size:10px;'>" . htmlspecialchars($task['customer_company'], ENT_QUOTES, 'UTF-8') . "</small>";
                            }
                            
                            $taskNameDisplay = !empty($task['task_name']) ? htmlspecialchars($task['task_name'], ENT_QUOTES, 'UTF-8') : '<span style="color:#adb5bd; font-style:italic;">No Name</span>';
                            
                            if (!empty($task['priority_name'])) {
                                $priorityColor = $task['priority_color'] ?? '#6c757d';
                                $priorityName = htmlspecialchars($task['priority_name'], ENT_QUOTES, 'UTF-8');
                                $priorityBadge = "<span style='background-color:{$priorityColor}; color:white; padding:2px 8px; border-radius:8px; font-size:9px; font-weight:600; display:inline-block;'>{$priorityName}</span>";
                            } else {
                                $priorityBadge = "<span style='background-color:#e9ecef; color:#6c757d; padding:2px 8px; border-radius:8px; font-size:9px; font-weight:600; display:inline-block;'>None</span>";
                            }
                            
                            $statusColor = $task['status_color'] ?? '#6c757d';
                            $statusName = htmlspecialchars($task['status_name'] ?? 'Pending', ENT_QUOTES, 'UTF-8');
                            $statusBadge = "<span style='background-color:{$statusColor}; color:white; padding:2px 8px; border-radius:8px; font-size:9px; font-weight:600; display:inline-block;'>{$statusName}</span>";
                            
                            $createdDate = date('d M Y', strtotime($task['created_at']));

                            $taskDescription = nl2br(htmlspecialchars($task['task'] ?? '', ENT_QUOTES, 'UTF-8'));
                        @endphp
                        
                        <tr style="background-color:{{ $rowColor }};">
                            <td style="padding:6px 4px; text-align:center; border:1px solid #dee2e6; font-weight:600; color:#495057; font-size:11px;">{{ $counter }}</td>
                            <td style="padding:6px 4px; text-align:left; border:1px solid #dee2e6; color:#212529; font-size:12px;">{!! $customerDisplay !!}</td>
                            <td style="padding:6px 4px; text-align:left; border:1px solid #dee2e6; color:#212529; font-size:12px;">{!! $taskNameDisplay !!}</td>
                            <td style="padding:6px 4px; text-align:left; border:1px solid #dee2e6; color:#212529; font-size:12px; white-space: normal;">{!! $taskDescription !!}</td>
                            <td style="padding:6px 4px; text-align:center; border:1px solid #dee2e6;">{!! $priorityBadge !!}</td>
                            <td style="padding:6px 4px; text-align:center; border:1px solid #dee2e6;">{!! $statusBadge !!}</td>
                            <td style="padding:6px 4px; text-align:center; border:1px solid #dee2e6; font-size:10px; color:#6c757d;">{{ $createdDate }}</td>
                        </tr>
                        @php $counter++; @endphp
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Footer -->
    <div style="background-color:#f8f9fa; padding:20px 15px; border-top:1px solid #dee2e6;">
        <p style="margin:0; font-size:12px; color:#6c757d; text-align:center;">
            This is an automated reminder from <strong>Workorio</strong>.<br>
            Please do not reply to this email.
        </p>
        <p style="margin:10px 0 0 0; font-size:11px; color:#adb5bd; text-align:center;">
            © {{ $payload['year'] }} Workorio. All rights reserved.
        </p>
    </div>

</div>

</body>
</html>
