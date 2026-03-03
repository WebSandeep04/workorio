<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Quotation - <?php echo e($quote->quotation_number); ?></title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Helvetica', sans-serif;
            margin: 0;
            padding: 0;
            color: #1f2937;
            background-color: #f9fafb;
            line-height: 1.5;
        }
        .page {
            width: 100%;
            height: 100%;
            position: relative;
            background-color: #f9fafb;
        }
        .header-banner {
            background-color: <?php echo e($settings->primary_color ?? '#434AFA'); ?>;
            color: white;
            padding: 40px;
            height: 120px;
        }
        .content {
            padding: 40px;
        }
        .section-header {
            border-bottom: 2px solid <?php echo e($settings->secondary_color ?? '#FF8C00'); ?>;
            color: <?php echo e($settings->secondary_color ?? '#FF8C00'); ?>;
            padding-bottom: 5px;
            margin-bottom: 20px;
            font-size: 20px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th {
            background-color: <?php echo e($settings->primary_color ?? '#434AFA'); ?>;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 12px;
        }
        table td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }
        .totals {
            float: right;
            width: 300px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
        .text-right {
            text-align: right;
        }
        .font-bold {
            font-weight: bold;
        }
        .footer {
            position: absolute;
            bottom: 40px;
            left: 40px;
            font-size: 10px;
            color: #6b7280;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
    <?php echo $__env->yieldContent('styles'); ?>
</head>
<body>
    <?php echo $__env->yieldContent('content'); ?>
</body>
</html>
<?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/quotation/templates/base.blade.php ENDPATH**/ ?>