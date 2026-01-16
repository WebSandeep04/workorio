<?php
use App\Models\Tenant;
use App\Models\User;
use App\Models\PasswordResetOtp;
use App\Services\TenantDatabaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$email = 'sandeep@triserv360.com';
echo "Searching for user: $email\n";

// 1. Check Master
DB::setDefaultConnection('mysql');
$user = User::where('email', $email)->first();
if ($user) {
    echo "Found in MASTER DB. User ID: {$user->id}\n";
    checkOtp('mysql', $email);
} else {
    echo "Not found in MASTER DB.\n";
}

// 2. Check Tenants
$tenants = Tenant::all();
foreach ($tenants as $tenant) {
    try {
        if (!TenantDatabaseService::connectionExists($tenant->id)) {
            TenantDatabaseService::createConnection($tenant);
        }
        TenantDatabaseService::setDefaultConnection($tenant->id);
        $connName = TenantDatabaseService::getConnectionName($tenant->id);
        
        $user = User::where('email', $email)->first();
        if ($user) {
            echo "Found in TENANT ID: {$tenant->id} (Connection: $connName). User ID: {$user->id}\n";
            checkOtp($connName, $email);
        }
    } catch (\Exception $e) {
        echo "Error checking tenant {$tenant->id}: " . $e->getMessage() . "\n";
    }
}

function checkOtp($connection, $email) {
    $otp = DB::connection($connection)->table('password_reset_otps')
        ->where('email', $email)
        ->orderBy('created_at', 'desc')
        ->first();
        
    if ($otp) {
        echo "  -> Found OTP Record:\n";
        echo "     Code: {$otp->otp}\n";
        echo "     Expires: {$otp->expires_at}\n";
        echo "     Used: {$otp->used}\n";
        echo "     Created: {$otp->created_at}\n";
        
        $expiry = \Carbon\Carbon::parse($otp->expires_at);
        $now = \Carbon\Carbon::now();
        echo "     Current App Time: " . $now->toDateTimeString() . "\n";
        echo "     Is Past? " . ($expiry->isPast() ? 'YES (Expired)' : 'NO (Valid)') . "\n";
    } else {
        echo "  -> No OTP records found for this email in this DB.\n";
    }
}
