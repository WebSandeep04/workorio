<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\User;
use App\Models\SalesStatus;
use App\Models\SalesLeadSource;
use App\Models\SalesProduct;
use App\Models\SalesBusinessType;
use App\Models\State;
use App\Models\City;
use App\Models\EntryType;
use App\Models\SubscriptionType;
use App\Models\User as UserModel;
use App\Models\Role as RoleModel;
use Illuminate\Support\Facades\Hash;
use App\Models\Calling;
use App\Models\CallingRemark;
use App\Models\CallingType;

class TenantDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the current tenant from the connection name
        $connectionName = DB::getDefaultConnection();
        $tenantId = $this->extractTenantIdFromConnection($connectionName);
        
        if (!$tenantId) {
            $this->command->error('Could not determine tenant ID from connection name.');
            return;
        }
        
        $this->command->info("Seeding minimal data for tenant ID: {$tenantId}");
        
        // Note: We don't seed tenant records in tenant databases
        // Each tenant database is isolated and doesn't need a tenants table
        
        // Seed only essential data:
        // 1. Roles (admin and user only)
        $this->seedRoles($tenantId);
        
        // 2. Sales status (close-win only)
        $this->seedSalesStatus($tenantId);

        // 3. Default Late Reasons
        $this->seedLateReasons();
        
        // 4. Countries
        $this->call(TenantCountrySeeder::class);

        // 5. Indian states and cities
        $this->seedIndianStatesAndCities($tenantId);
        
        // 6. Admin user
        $this->seedAdminUser($tenantId);
        // 7. Calling types
        $this->seedCallingTypes();
        // 8. Sample callings
        $this->seedCallings();
        // 9. Sample calling remarks
        $this->seedCallingRemarks();
        
        // 9. Quotation settings
        // $this->call(QuotationSettingsSeeder::class);
        
        // 11. Subscription statuses
        $this->seedSubscriptionStatuses();

        // 12. Branches and Departments
        $this->seedBranches();
        $this->seedDepartments();
        
        // 13. Asset Statuses
        $this->seedAssetStatuses();
        
        $this->command->info('Minimal tenant data seeded successfully.');
    }

    /**
     * Seed Asset Statuses
     */
    private function seedAssetStatuses(): void
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('asset_statuses')) {
            $this->command->warn('asset_statuses table not found, skipping asset status seeding.');
            return;
        }

        $statuses = [
            'Available',
            'Assigned',
            'Damaged',
            'Lost',
            'Under Maintenance',
            'Broken'
        ];

        foreach ($statuses as $status) {
            DB::table('asset_statuses')->updateOrInsert(
                ['name' => $status],
                [
                    'name' => $status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('✅ Seeded asset statuses.');
    }

    /**
     * Seed default Late Reasons (for attendance/worklog)
     */
    private function seedLateReasons(): void
    {
        // Guard in case migration hasn't run yet for this tenant
        if (!\Illuminate\Support\Facades\Schema::hasTable('late_reasons')) {
            $this->command->warn('late_reasons table not found, skipping late reasons seeding.');
            return;
        }

        $reasons = [
            'Other',
            'Traffic / transport delay',
            'Health-related',
            'Family emergency',
            'Work-related reason',
            'Personal planning issue',
        ];

        foreach ($reasons as $reason) {
            DB::table('late_reasons')->updateOrInsert(
                ['reason' => $reason],
                [
                    'reason' => $reason,
                    'active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('✅ Seeded default late reasons.');
    }
    
    /**
     * Extract tenant ID from connection name (e.g., "tenant_1" -> 1)
     */
    private function extractTenantIdFromConnection(string $connectionName): ?int
    {
        if (preg_match('/^tenant_(\d+)$/', $connectionName, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }
    
    
    /**
     * Seed roles for the tenant (admin only)
     */
    private function seedRoles(int $tenantId): void
    {
        $roles = [
            [
                'role_name' => 'admin',
                'description' => 'Administrator with full access',
                'is_custom' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];
        
        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['role_name' => $role['role_name']],
                $role
            );
        }
        
        $this->command->info('✅ Seeded roles: admin');
    }
    
    /**
     * Seed sales status (Close Won and Close Lost)
     */
    private function seedSalesStatus(int $tenantId): void
    {
        $statuses = [
            ['status_name' => 'Close Won'],
            ['status_name' => 'Close Lost']
        ];
        
        foreach ($statuses as $status) {
            DB::table('sales_status')->updateOrInsert(
                ['status_name' => $status['status_name']],
                array_merge($status, ['created_at' => now(), 'updated_at' => now()])
            );
        }
        
        $this->command->info('✅ Seeded sales status: Close Won, Close Lost');
    }
    
    /**
     * Seed Indian states and cities
     */
    private function seedIndianStatesAndCities(int $tenantId): void
    {
        // Seed Indian states
        $states = [
            ['state_name' => 'Maharashtra'],
            ['state_name' => 'Karnataka'],
            ['state_name' => 'Tamil Nadu'],
            ['state_name' => 'Gujarat'],
            ['state_name' => 'Rajasthan'],
            ['state_name' => 'Uttar Pradesh'],
            ['state_name' => 'West Bengal'],
            ['state_name' => 'Delhi'],
            ['state_name' => 'Punjab'],
            ['state_name' => 'Haryana']
        ];
        
        foreach ($states as $state) {
            $stateId = DB::table('states')->insertGetId(
                array_merge($state, ['created_at' => now(), 'updated_at' => now()])
            );
            
            // Seed cities for each state
            $this->seedIndianCitiesForState($stateId, $state['state_name']);
        }
        
        $this->command->info('✅ Seeded Indian states and cities');
    }
    
    /**
     * Seed Indian cities for a specific state
     */
    private function seedIndianCitiesForState(int $stateId, string $stateName): void
    {
        $citiesByState = [
            'Maharashtra' => ['Mumbai', 'Pune', 'Nagpur', 'Nashik', 'Aurangabad'],
            'Karnataka' => ['Bangalore', 'Mysore', 'Hubli', 'Mangalore', 'Belgaum'],
            'Tamil Nadu' => ['Chennai', 'Coimbatore', 'Madurai', 'Tiruchirappalli', 'Salem'],
            'Gujarat' => ['Ahmedabad', 'Surat', 'Vadodara', 'Rajkot', 'Bhavnagar'],
            'Rajasthan' => ['Jaipur', 'Jodhpur', 'Udaipur', 'Kota', 'Ajmer'],
            'Uttar Pradesh' => ['Lucknow', 'Kanpur', 'Agra', 'Varanasi', 'Meerut'],
            'West Bengal' => ['Kolkata', 'Howrah', 'Durgapur', 'Asansol', 'Siliguri'],
            'Delhi' => ['New Delhi', 'Central Delhi', 'East Delhi', 'West Delhi', 'North Delhi'],
            'Punjab' => ['Chandigarh', 'Ludhiana', 'Amritsar', 'Jalandhar', 'Patiala'],
            'Haryana' => ['Gurgaon', 'Faridabad', 'Panipat', 'Karnal', 'Hisar']
        ];
        
        $cities = $citiesByState[$stateName] ?? [];
        
        foreach ($cities as $cityName) {
            DB::table('cities')->updateOrInsert(
                ['city_name' => $cityName, 'state_id' => $stateId],
                [
                    'city_name' => $cityName,
                    'state_id' => $stateId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
    }
    
    
    /**
     * Seed admin user for the tenant (admin only)
     */
    private function seedAdminUser(int $tenantId): void
    {
        // Get admin role (role_id = 1)
        $adminRole = RoleModel::where('role_name', 'admin')->first();
        
        if (!$adminRole) {
            $this->command->error('Admin role not found. Please ensure roles are seeded first.');
            return;
        }
        
        // Create default admin user for this tenant
        $adminUser = UserModel::firstOrCreate(
            ['email' => 'admin@tenant' . $tenantId . '.com'],
            [
                'name' => 'Tenant Admin ' . $tenantId,
                'email' => 'admin@tenant' . $tenantId . '.com',
                'password' => Hash::make('admin123'),
                'role_id' => $adminRole->id,
                'is_worklog' => true,
                'is_manager' => null,
                'salary_per_month' => 50000,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        if ($adminUser->wasRecentlyCreated) {
            $this->command->info("✅ Created admin user: {$adminUser->name} ({$adminUser->email})");
        } else {
            $this->command->info("ℹ️ Admin user already exists: {$adminUser->name} ({$adminUser->email})");
        }
    }
    
    /**
     * Seed calling types
     */
    private function seedCallingTypes(): void
    {
        $callingTypes = [
            ['name' => 'Cold'],
            ['name' => 'Follow Up'],
            ['name' => 'Hot Lead'],
            ['name' => 'Customer Support'],
            ['name' => 'Demo Call'],
            ['name' => 'Closing Call'],
            ['name' => 'Junk'],
        ];
        
        foreach ($callingTypes as $type) {
            DB::table('calling_types')->updateOrInsert(
                ['name' => $type['name']],
                array_merge($type, ['created_at' => now(), 'updated_at' => now()])
            );
        }
        
        $this->command->info('✅ Seeded calling types: Cold, Follow Up, Hot Lead, Customer Support, Demo Call, Closing Call, Junk');        
    }
    
    private function seedCallings(): void
    {
        // Get all calling type IDs
        $callingTypeIds = DB::table('calling_types')->pluck('id')->toArray();
        
        if (empty($callingTypeIds)) {
            $this->command->error('Calling types not found. Please ensure calling types are seeded first.');
            return;
        }
        
        // Get status IDs (should be seeded before this)
        $statusIds = DB::table('sales_status')->pluck('id')->toArray();
        
        // pick random 10 from existing states/cities
        $stateIds = DB::table('states')->pluck('id')->all();
        $cityByState = [];
        foreach ($stateIds as $sid) {
            $cityByState[$sid] = DB::table('cities')->where('state_id', $sid)->pluck('id')->all();
        }
        $names = ['Aman','Rohit','Priya','Neha','Karan','Simran','Vivek','Pooja','Arjun','Sneha'];
        $adminUserId = DB::table('users')->value('id');
        for ($i=0; $i<10; $i++) {
            $sid = $stateIds[array_rand($stateIds)];
            $cities = $cityByState[$sid] ?? [];
            $cid = !empty($cities) ? $cities[array_rand($cities)] : null;
            DB::table('callings')->insert([
                'user_id' => $adminUserId,
                'calling_type_id' => $callingTypeIds[array_rand($callingTypeIds)],
                'status_id' => !empty($statusIds) ? $statusIds[array_rand($statusIds)] : null,
                'name' => $names[$i],
                'email' => strtolower($names[$i]).$i.'@example.com',
                'state_id' => $sid,
                'city_id' => $cid,
                'address' => 'Sample address '.$i,
                'phone' => '99999'.str_pad((string)$i, 5, '0', STR_PAD_LEFT),
                'next_follow_up_date' => now()->addDays(rand(1, 30))->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Add some specific Follow Up calls for today's calling functionality
        $followUpTypeId = DB::table('calling_types')->where('name', 'Follow Up')->value('id');
        if ($followUpTypeId) {
            for ($i = 0; $i < 3; $i++) {
                $sid = $stateIds[array_rand($stateIds)];
                $cities = $cityByState[$sid] ?? [];
                $cid = !empty($cities) ? $cities[array_rand($cities)] : null;
                
                DB::table('callings')->insert([
                    'user_id' => $adminUserId,
                    'calling_type_id' => $followUpTypeId,
                    'status_id' => !empty($statusIds) ? $statusIds[array_rand($statusIds)] : null,
                    'name' => 'Follow Up ' . ($i + 1),
                    'email' => 'followup' . ($i + 1) . '@example.com',
                    'state_id' => $sid,
                    'city_id' => $cid,
                    'address' => 'Follow up address ' . ($i + 1),
                    'phone' => '88888' . str_pad((string)($i + 1), 5, '0', STR_PAD_LEFT),
                    'next_follow_up_date' => now()->subDays(rand(0, 2))->toDateString(), // Due today or overdue
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        $this->command->info('✅ Seeded 10 calling records with random calling type IDs + 3 Follow Up calls for today');
    }

    private function seedCallingRemarks(): void
    {
        $callingIds = DB::table('callings')->pluck('id')->all();
        if (empty($callingIds)) {
            return;
        }
        $phrases = [
            'Left voicemail, awaiting response',
            'Spoke to client; requested callback next week',
            'Interested in demo; send details via email',
            'Number unreachable; try again tomorrow',
            'Requested quotation; follow up in 2 days',
            'Not interested currently; follow up in a month',
        ];
        $rows = [];
        foreach ($callingIds as $cid) {
            $n = rand(1, 3);
            for ($i = 0; $i < $n; $i++) {
                $rows[] = [
                    'calling_id' => $cid,
                    'remark' => $phrases[array_rand($phrases)],
                    'created_at' => now()->subDays(rand(0, 10)),
                    'updated_at' => now()->subDays(rand(0, 10)),
                ];
            }
        }
        if (!empty($rows)) {
            DB::table('calling_remarks')->insert($rows);
        }
    }

    /**
     * Seed subscription statuses (pending, payment received)
     */
    private function seedSubscriptionStatuses(): void
    {
        // Guard in case migration hasn't run yet for this tenant
        if (!\Illuminate\Support\Facades\Schema::hasTable('subscription_status')) {
            $this->command->warn('subscription_status table not found, skipping subscription status seeding.');
            return;
        }

        $statuses = [
            ['status_name' => 'Pending'],
            ['status_name' => 'Payment Received']
        ];

        foreach ($statuses as $status) {
            DB::table('subscription_status')->updateOrInsert(
                ['status_name' => $status['status_name']],
                array_merge($status, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('✅ Seeded subscription statuses: pending, payment received.');
    }

    /**
     * Seed Default Branch
     */
    private function seedBranches(): void
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('branches')) {
            $this->command->warn('branches table not found, skipping branch seeding.');
            return;
        }

        DB::table('branches')->updateOrInsert(
            ['code' => 'GN'],
            [
                'code' => 'GN',
                'name' => 'General',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info('✅ Seeded default branch: General');
    }

    /**
     * Seed Default Department
     */
    private function seedDepartments(): void
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('departments')) {
            $this->command->warn('departments table not found, skipping department seeding.');
            return;
        }

        // Get General branch ID
        $branchId = DB::table('branches')->where('code', 'GN')->value('id');

        if (!$branchId) {
            $this->command->error('General branch not found. Skipping department seeding.');
            return;
        }

        DB::table('departments')->updateOrInsert(
            ['code' => 'GEN', 'branch_id' => $branchId],
            [
                'branch_id' => $branchId,
                'code' => 'GEN',
                'name' => 'General',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info('✅ Seeded default department: General');
    }
}
