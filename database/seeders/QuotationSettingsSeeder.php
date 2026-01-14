<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuotationSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample services array
        $services = [
            'ERP Development & Implementation',
            'Custom Software Development',
            'Mobile App Development',
            'SEO / SMO (Search & Social Optimization)',
            'Website Development',
            'IT Infrastructure Services',
            'Fractional CIO Services',
            'Staff Augmentation',
            'Testing & QA',
            'Cyber Security'
        ];

        $settingsData = [
            'company_name' => 'Triserv Solutions',
            'company_description' => "Triserv Solutions is a leading technology company specializing in innovative software solutions and IT services. We are committed to delivering excellence through cutting-edge technology and industry best practices.\n\nWith years of experience in the industry, we have helped numerous businesses transform their operations and achieve their digital goals. Our team of experts is dedicated to providing tailored solutions that meet your unique business needs.",
            'mission' => 'To empower businesses with innovative technology solutions that drive growth, efficiency, and competitive advantage in the digital era.',
            'vision' => 'To be the most trusted technology partner for businesses worldwide, recognized for excellence, innovation, and customer success.',
            'core_values' => 'Innovation, Integrity, Transparency, Excellence, Customer Focus, Quality, Teamwork, Continuous Learning',
            'services' => json_encode($services),
            'office_name' => 'Triserv Solutions - Head Office',
            'office_address' => "Krishna Tower\nGreen Park Extension",
            'office_city' => 'New Delhi',
            'office_state' => 'Delhi',
            'office_pincode' => '110016',
            'office_country' => 'India',
            'phone' => '+91-9839353494',
            'email' => 'info@triserv360.com',
            'website' => 'www.triserv360.com',
            'gstin' => '07AAJCT3301R1Z6',
            'pan' => 'AAJCT3301R',
            'bank_details' => "Bank Name: ICICI Bank\nAccount Name: Triserv Solutions Pvt. Ltd.\nAccount Number: 628805026732\nIFSC Code: ICIC0001234\nBranch: Green Park, New Delhi",
            'logo_path' => null,
            'updated_at' => now(),
        ];

        // Use updateOrInsert to allow re-seeding (updates if exists, inserts if not)
        $existing = DB::table('quotation_settings')->first();
        
        if ($existing) {
            // Update existing record
            DB::table('quotation_settings')
                ->where('id', $existing->id)
                ->update($settingsData);
            $this->command->info('Quotation settings updated successfully!');
        } else {
            // Insert new record
            $settingsData['created_at'] = now();
            DB::table('quotation_settings')->insert($settingsData);
            $this->command->info('Quotation settings seeded successfully!');
        }
    }
}
