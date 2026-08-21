<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .header h2 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .field {
            margin: 15px 0;
            padding: 10px;
            background-color: #f9f9f9;
            border-left: 4px solid #4CAF50;
        }
        .field .label {
            font-weight: bold;
            color: #555;
            display: inline-block;
            width: 140px;
        }
        .field .value {
            color: #333;
            display: inline-block;
        }
        .message-box {
            background-color: #fff;
            padding: 15px;
            margin: 20px 0;
            border: 1px solid #ddd;
            border-left: 4px solid #4CAF50;
            border-radius: 4px;
        }
        .message-box strong {
            color: #4CAF50;
            display: block;
            margin-bottom: 10px;
        }
        .footer {
            margin-top: 30px;
            padding: 20px;
            text-align: center;
            background-color: #f9f9f9;
            border-top: 1px solid #ddd;
        }
        .footer p {
            margin: 5px 0;
            font-size: 14px;
            color: #777;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 20px 0;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .divider {
            margin: 20px 0;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🔔 New IndiaMART Lead Received</h2>
        </div>
        
        <div class="content">
            <p>A new lead has been received from IndiaMART. Please review the details below:</p>
            
            <div class="field">
                <span class="label">Name:</span>
                <span class="value"><?php echo e($leadData['sender_name'] ?? 'N/A'); ?></span>
            </div>
            
            <div class="field">
                <span class="label">Company:</span>
                <span class="value"><?php echo e($leadData['sender_company'] ?? 'N/A'); ?></span>
            </div>
            
            <div class="field">
                <span class="label">Mobile:</span>
                <span class="value"><?php echo e($leadData['sender_mobile'] ?? 'N/A'); ?></span>
            </div>
            
            <div class="field">
                <span class="label">Email:</span>
                <span class="value"><?php echo e($leadData['sender_email'] ?? 'N/A'); ?></span>
            </div>
            
            <div class="field">
                <span class="label">City:</span>
                <span class="value"><?php echo e($leadData['sender_city'] ?? 'N/A'); ?></span>
            </div>
            
            <div class="field">
                <span class="label">State:</span>
                <span class="value"><?php echo e($leadData['sender_state'] ?? 'N/A'); ?></span>
            </div>
            
            <div class="field">
                <span class="label">Product Interest:</span>
                <span class="value"><?php echo e($leadData['query_product_name'] ?? 'N/A'); ?></span>
            </div>
            
            <div class="field">
                <span class="label">Query Type:</span>
                <span class="value"><?php echo e($leadData['query_type'] ?? 'N/A'); ?></span>
            </div>
            
            <?php if(!empty($leadData['query_message'])): ?>
            <div class="message-box">
                <strong>Message:</strong>
                <p><?php echo e($leadData['query_message']); ?></p>
            </div>
            <?php endif; ?>
        </div>
            

        
        <div class="footer">
            <p>This is an automated notification from your Lead Management System.</p>
            <p>Please login to the CRM to assign and follow up on this lead.</p>
        </div>
    </div>
</body>
</html>

<?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/emails/indiamart-lead-notification.blade.php ENDPATH**/ ?>