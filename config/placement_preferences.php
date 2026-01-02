<?php

/**
 * Placement Preferences Configuration
 *
 * Dropdown options for student placement preferences
 * Aligned with programmes: BTA (Automotive), BTD (Design & Analysis), BTG (Oil & Gas)
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Skills Options
    |--------------------------------------------------------------------------
    | Technical and soft skills relevant to Mechanical Engineering Technology
    | programmes (BTA, BTD, BTG)
    */
    'skills' => [
        // Technical Skills - Common
        'cad_cam' => 'CAD/CAM Software (AutoCAD, SolidWorks, CATIA)',
        'cnc_machining' => 'CNC Machining & Programming',
        'plc_programming' => 'PLC Programming & Automation',
        'mechanical_design' => 'Mechanical Design & Drafting',
        'quality_control' => 'Quality Control & Inspection (QC/QA)',
        'maintenance_repair' => 'Maintenance & Repair',
        'welding_fabrication' => 'Welding & Metal Fabrication',
        'project_management' => 'Project Management',
        'data_analysis' => 'Data Analysis & Reporting',
        'ms_office' => 'Microsoft Office (Excel, Word, PowerPoint)',

        // BTA - Automotive Specific
        'automotive_systems' => 'Automotive Systems & Diagnostics',
        'engine_technology' => 'Engine Technology & Repair',
        'vehicle_assembly' => 'Vehicle Assembly & Manufacturing',
        'automotive_electronics' => 'Automotive Electronics',
        'ev_hybrid_technology' => 'EV/Hybrid Vehicle Technology',

        // BTD - Design & Analysis Specific
        'fea_simulation' => 'FEA/CFD Simulation (ANSYS, Abaqus)',
        'product_design' => 'Product Design & Development',
        'reverse_engineering' => 'Reverse Engineering',
        '3d_printing' => '3D Printing & Rapid Prototyping',
        'material_testing' => 'Material Testing & Analysis',

        // BTG - Oil & Gas Specific
        'piping_design' => 'Piping Design & Engineering',
        'pressure_vessels' => 'Pressure Vessel & Tank Design',
        'ndt_inspection' => 'NDT (Non-Destructive Testing)',
        'rotating_equipment' => 'Rotating Equipment (Pumps, Compressors)',
        'process_equipment' => 'Process Equipment & Operations',
        'hse_safety' => 'HSE (Health, Safety, Environment)',

        // Soft Skills
        'leadership' => 'Leadership & Team Management',
        'communication' => 'Communication & Presentation',
        'problem_solving' => 'Problem Solving & Critical Thinking',

        'other' => 'Other (Please specify)',
    ],

    /*
    |--------------------------------------------------------------------------
    | Interests Options
    |--------------------------------------------------------------------------
    | Career interests and areas of focus for placement
    */
    'interests' => [
        // Engineering & Technical
        'design_engineering' => 'Design Engineering',
        'manufacturing_production' => 'Manufacturing & Production',
        'quality_assurance' => 'Quality Assurance & Control',
        'maintenance_engineering' => 'Maintenance Engineering',
        'project_engineering' => 'Project Engineering',
        'process_engineering' => 'Process Engineering',
        'research_development' => 'Research & Development (R&D)',
        'technical_sales' => 'Technical Sales & Support',

        // BTA - Automotive Focus
        'automotive_manufacturing' => 'Automotive Manufacturing',
        'vehicle_testing' => 'Vehicle Testing & Validation',
        'automotive_service' => 'Automotive Service & After-Sales',
        'motorsport_engineering' => 'Motorsport Engineering',
        'ev_development' => 'Electric Vehicle Development',

        // BTD - Design Focus
        'product_development' => 'Product Development & Innovation',
        'cae_simulation' => 'CAE & Simulation Engineering',
        'tooling_fixture' => 'Tooling & Fixture Design',
        'industrial_design' => 'Industrial Design',

        // BTG - Oil & Gas Focus
        'upstream_operations' => 'Upstream Operations (Exploration & Production)',
        'downstream_operations' => 'Downstream Operations (Refinery)',
        'pipeline_engineering' => 'Pipeline Engineering',
        'offshore_operations' => 'Offshore Operations',
        'petrochemical' => 'Petrochemical Industry',
        'renewable_energy' => 'Renewable Energy',

        // General
        'consulting' => 'Technical Consulting',
        'training_education' => 'Training & Education',
        'entrepreneurship' => 'Entrepreneurship',

        'other' => 'Other (Please specify)',
    ],

    /*
    |--------------------------------------------------------------------------
    | Preferred Industry Options
    |--------------------------------------------------------------------------
    | Industries aligned with BTA, BTD, and BTG programmes
    */
    'preferred_industry' => [
        // Automotive & Transportation (BTA)
        'automotive_oem' => 'Automotive OEM (Proton, Perodua, Honda, Toyota)',
        'automotive_vendor' => 'Automotive Vendor & Parts Supplier',
        'automotive_aftermarket' => 'Automotive Aftermarket & Service',
        'aerospace' => 'Aerospace & Aviation',
        'railway_transport' => 'Railway & Public Transport',
        'ev_new_energy' => 'EV & New Energy Vehicles',

        // Manufacturing & Design (BTD)
        'heavy_machinery' => 'Heavy Machinery & Equipment',
        'electrical_electronics' => 'Electrical & Electronics Manufacturing',
        'precision_engineering' => 'Precision Engineering',
        'mold_die' => 'Mold & Die Manufacturing',
        'plastic_injection' => 'Plastic Injection & Polymer',
        'metal_fabrication' => 'Metal Fabrication & Steel',
        'consumer_products' => 'Consumer Products Manufacturing',
        'semiconductor' => 'Semiconductor & High-Tech',

        // Oil & Gas / Energy (BTG)
        'oil_gas_upstream' => 'Oil & Gas (Upstream - PETRONAS, Shell, ExxonMobil)',
        'oil_gas_downstream' => 'Oil & Gas (Downstream - Refinery, LNG)',
        'petrochemical' => 'Petrochemical & Chemical',
        'power_generation' => 'Power Generation & Utilities',
        'renewable_energy' => 'Renewable Energy (Solar, Wind, Hydro)',
        'marine_offshore' => 'Marine & Offshore Engineering',

        // General Engineering
        'construction' => 'Construction & Infrastructure',
        'fmcg' => 'FMCG (Fast-Moving Consumer Goods)',
        'food_beverage' => 'Food & Beverage Manufacturing',
        'pharmaceutical' => 'Pharmaceutical & Medical Devices',
        'logistics_supply_chain' => 'Logistics & Supply Chain',
        'consulting_services' => 'Engineering Consulting & Services',

        'other' => 'Other (Please specify)',
    ],

    /*
    |--------------------------------------------------------------------------
    | Preferred Location Options
    |--------------------------------------------------------------------------
    | Malaysian states and regions, plus overseas options
    */
    'preferred_location' => [
        // Industrial Hubs
        'selangor' => 'Selangor (Shah Alam, Klang, Petaling Jaya)',
        'kuala_lumpur' => 'Kuala Lumpur',
        'johor' => 'Johor (Johor Bahru, Pasir Gudang, Senai)',
        'penang' => 'Penang (Bayan Lepas, Butterworth)',
        'perak' => 'Perak (Ipoh, Taiping)',

        // East Coast
        'pahang' => 'Pahang (Kuantan, Gebeng, Gambang)',
        'terengganu' => 'Terengganu (Kemaman, Kerteh)',
        'kelantan' => 'Kelantan (Kota Bharu)',

        // Northern
        'kedah' => 'Kedah (Kulim Hi-Tech Park)',
        'perlis' => 'Perlis',

        // Southern
        'melaka' => 'Melaka',
        'negeri_sembilan' => 'Negeri Sembilan (Nilai, Seremban)',

        // East Malaysia
        'sabah' => 'Sabah (Kota Kinabalu, Sandakan)',
        'sarawak' => 'Sarawak (Kuching, Miri, Bintulu)',
        'labuan' => 'Labuan',

        // Special
        'anywhere_malaysia' => 'Anywhere in Malaysia',
        'overseas' => 'Open to Overseas (Singapore, etc.)',

        'other' => 'Other (Please specify)',
    ],
];
