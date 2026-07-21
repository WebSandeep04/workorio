<?php
$req = Request::create('/test', 'GET', ['month'=>6, 'year'=>2026]);
$req->headers->set('X-Requested-With', 'XMLHttpRequest');
$c = app(\App\Http\Controllers\Payroll\MonthlyAttendanceReviewController::class);
echo $c->index($req)->getContent();
