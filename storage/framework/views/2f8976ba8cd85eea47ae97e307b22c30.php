

<?php $__env->startSection('content'); ?>
    <!-- Page 1: About -->
    <div class="page">
        <div class="header-banner">
            <h1 style="margin: 0; font-size: 28px;">About <?php echo e($settings->company_name ?? 'Our Company'); ?></h1>
        </div>
        <div class="content" style="text-align: center; padding-top: 60px;">
            <div style="max-width: 600px; margin: 0 auto;">
                <p style="font-size: 14px; color: #374151;"><?php echo e($settings->company_description ?? ''); ?></p>
                
            
                
                <?php if($settings->core_values): ?>
                    <div style="margin-top: 40px;">
                        <h2 style="color: <?php echo e($settings->primary_color ?? '#434AFA'); ?>; font-size: 20px;">Core Values</h2>
                        <p><?php echo e($settings->core_values); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="footer"><?php echo e($settings->company_name); ?> | <?php echo e($settings->website); ?></div>
    </div>

    <div class="page-break"></div>

    <!-- Page 2: Services -->
    <div class="page">
        <div class="header-banner">
            <h1 style="margin: 0; font-size: 28px;">Our Services</h1>
        </div>
        <div class="content">
            <div style="display: block; width: 100%;">
                <?php $services = is_string($settings->services) ? json_decode($settings->services, true) : ($settings->services ?? []); ?>
                <?php $__currentLoopData = array_chunk($services, 2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="width: 100%; margin-bottom: 20px; overflow: hidden;">
                        <?php $__currentLoopData = $row; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div style="width: 45%; float: left; margin-right: 4%; background: white; padding: 15px; border-left: 5px solid <?php echo e($settings->primary_color ?? '#434AFA'); ?>; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                <strong style="font-size: 14px;"><?php echo e($service); ?></strong>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <div style="clear: both;"></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <div class="footer"><?php echo e($settings->company_name); ?> | <?php echo e($settings->website); ?></div>
    </div>

    <div class="page-break"></div>

    <!-- Page 3: Quotation -->
    <div class="page">
        <div class="content" style="padding-top: 20px;">
            <div style="margin-bottom: 30px;">
                <div style="float: left; width: 50%;">
                    <h1 style="color: <?php echo e($settings->primary_color ?? '#434AFA'); ?>; margin: 0;">QUOTATION</h1>
                    <p style="margin: 5px 0; font-size: 12px;">#<?php echo e($quote->quotation_number); ?></p>
                    <p style="margin: 0; font-size: 12px;">Date: <?php echo e($quote->created_at->format('d M, Y')); ?></p>
                </div>
                <div style="float: right; width: 50%; text-align: right;">
                    <strong style="font-size: 16px;"><?php echo e($settings->company_name); ?></strong><br>
                    <span style="font-size: 11px; color: #6b7280;">
                        <?php echo e($settings->office_address); ?><br>
                        <?php echo e($settings->office_city); ?>, <?php echo e($settings->office_state); ?> - <?php echo e($settings->office_pincode); ?><br>
                        Phone: <?php echo e($settings->phone); ?> | Email: <?php echo e($settings->email); ?>

                    </span>
                </div>
                <div style="clear: both;"></div>
            </div>

            <div style="margin-bottom: 30px; background: #f3f4f6; padding: 15px; border-radius: 5px;">
                <div style="float: left; width: 50%;">
                    <strong style="color: #6b7280; font-size: 11px; text-transform: uppercase;">Quote For:</strong><br>
                    <?php if($quote->customer_type == 'customer'): ?>
                        <strong><?php echo e($quote->customer->name ?? 'N/A'); ?></strong><br>
                        <?php echo e($quote->customer->company_name ?? ''); ?>

                    <?php else: ?>
                        <?php $prospect = \App\Models\Prospectus::find($quote->customer_id); ?>
                        <strong><?php echo e($prospect->prospectus_name ?? 'N/A'); ?></strong><br>
                        <?php echo e($prospect->contact_person ?? ''); ?>

                    <?php endif; ?>
                </div>
                <div style="float: right; width: 50%; text-align: right;">
                    <strong style="color: #6b7280; font-size: 11px; text-transform: uppercase;">Project Details:</strong><br>
                    Timeline: <?php echo e($quote->data['project_timeline'] ?? 'N/A'); ?>

                </div>
                <div style="clear: both;"></div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 40%;">Product/Service</th>
                        <th style="width: 25%;">Remark</th>
                        <th style="width: 15%; text-align: right;">Price</th>
                        <th style="width: 15%; text-align: right;">Tax (18%)</th>
                        <th style="width: 15%; text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $products = $quote->data['products'] ?? []; 
                        $subtotal = 0;
                    ?>
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php 
                            $price = $item['price'] ?? 0;
                            $tax = round($price * 0.18, 2);
                            $rowTotal = $price + $tax;
                            $subtotal += $rowTotal;
                        ?>
                        <tr>
                            <td><?php echo e($index + 1); ?></td>
                            <td><?php echo e(\App\Models\SalesProduct::find($item['product_id'])->product_name ?? $item['product_id']); ?></td>
                            <td><?php echo e($item['remark'] ?? ''); ?></td>
                            <td style="text-align: right;">₹<?php echo e(number_format($price, 2)); ?></td>
                            <td style="text-align: right;">₹<?php echo e(number_format($tax, 2)); ?></td>
                            <td style="text-align: right;">₹<?php echo e(number_format($rowTotal, 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>

            <div style="float: right; width: 250px;">
                <div style="border-bottom: 1px solid #e5e7eb; padding: 5px 0;">
                    <span style="color: #6b7280;">Subtotal:</span>
                    <span style="float: right;">₹<?php echo e(number_format($subtotal, 2)); ?></span>
                </div>
                <?php if(($quote->data['discount'] ?? 0) > 0): ?>
                    <div style="border-bottom: 1px solid #e5e7eb; padding: 5px 0; color: #ef4444;">
                        <span>Discount:</span>
                        <span style="float: right;">-₹<?php echo e(number_format($quote->data['discount'], 2)); ?></span>
                    </div>
                <?php endif; ?>
                <div style="padding: 10px 0; font-size: 16px; font-weight: bold; color: <?php echo e($settings->primary_color ?? '#434AFA'); ?>;">
                    <span>Grand Total:</span>
                    <span style="float: right;">₹<?php echo e(number_format($quote->total_amount, 2)); ?></span>
                </div>
            </div>
            <div style="clear: both;"></div>

            <div style="margin-top: 40px;">
                <div class="section-header">Bank Details</div>
                <div style="white-space: pre-line; font-size: 11px;"><?php echo e($settings->bank_details); ?></div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('quotation.templates.base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/quotation/templates/modern.blade.php ENDPATH**/ ?>