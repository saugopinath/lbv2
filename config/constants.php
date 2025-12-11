<?php
return [
    'gender' => [
        'Male' => 'Male',
        'Female' => 'Female',
        'Other' => 'Other',
    ],
    'caste' => [
        'SC' => 'SC',
        'ST' => 'ST',
        'General' => 'General',
    ],
    'category_purohit' => [
        'SC' => 'SC',
        'ST' => 'ST',
        'OTHERS' => 'OTHERS'
    ],
    'caste_lb' => [
        'SC' => 'SC',
        'ST' => 'ST',
        'OTHERS' => 'OTHERS'
    ],
    'family_id_type_lb' => [
        'Swasthya Sathi' => 'Swasthya Sathi',
        'SECC' => 'SECC'
    ],
    'user_level' => [
        'State' => 'State',
        'District' => 'District',
        'Block' => 'Block',
        'Subdiv' => 'Sub Division',
        'Municipality' => 'Municipality',
        'Gram Panchayet' => 'Gram Panchayet',
    ],
    'disablity_type' => [
        'Orthopedically Handicapped' => 'Orthopedically Handicapped',
        'Visually Handicapped' => 'Visually Handicapped',
        'Mental illness' => 'Mental illness',
        'Mental Retardation' => 'Mental Retardation',
        'Mutiple Disablities' => 'Mutiple Disablities',
        'Leprosy Cured' => 'Leprosy Cured',
        'Nervous Disorder' => 'Nervous Disorder',
        'Others' => 'Others'
    ],
    'marital_status' => [
        'Unmarried' => 'Unmarried',
        'Married' => 'Married',
        'Seperated' => 'Seperated',
        'Widow' => 'Widow',
        'Widower' => 'Widower',
    ],
    'ration_cat' => [
        'AAY' => 'AAY',
        'OHH' => 'OHH',
        'RKSY 1' => 'RKSY 1',
        'RKSY 2' => 'RKSY 2',
        'SPHH' => 'SPHH',
        'PHH' => 'PHH',
    ],
    'rural_urban' => [
        '2' => 'Rural',
        '1' => 'Urban',
    ],
    'pension_body' => [
        'Central Govt' => 'Central Govt',
        'State Govt' => 'State Govt',
        'Local Administration' => 'Local Administration',
        'Govt. Aided Organization' => 'Govt. Aided Organization',
    ],
    'social_pension_cat' => [
        'NSAP Old Age' => 'NSAP Old Age',
        'NSAP Widow Pension' => 'NSAP Widow Pension',
        'NSAP Disability Pension' => 'NSAP Disability Pension',
        'Old Age Pension' => 'Old Age Pension',
        'Widow Pension' => 'Widow Pension',
        'Disability Pension' => 'Disability Pension',
        'Lok Prasar Prakalpa' => 'Lok Prasar Prakalpa',
        'Fisherman\'s Old Age Pension' => 'Fisherman\'s Old Age Pension',
        'Farmers Old Age Pension' => 'Farmers Old Age Pension',
        'Artisan/Weaver Old Age Pension' => 'Artisan/Weaver Old Age Pension',
    ],
    'document_group' => [
        '1' => 'Date of Birth Identification',
        '2' => 'Caste Identification'
    ],
    'fin_year' => [
        '2021-2022' => '2021-2022',
	    '2022-2023' => '2022-2023',
        '2023-2024' => '2023-2024',
        '2024-2025' => '2024-2025',
        '2025-2026' => '2025-2026',
    ],
    'monthlist' => [
        'April' => 'APRIL',
        'May' => 'MAY',
        'June' => 'JUNE',
        'July' => 'JULY',
        'August' => 'AUGUST',
        'September' => 'SEPTEMBER',
        // 'October' => 'OCTOBER',
        // 'November' => 'NOVEMBER',
        // 'December' => 'DECEMBER',
        // 'January' => 'JANUARY',
        // 'February' => 'FEBRUARY',
        //'March' => 'MARCH',
    ],
    'month_list' => [
        '01' => 'January',
        '02' => 'February',
        '03' => 'March',
        '04' => 'April',
        '05' => 'May',
        '06' => 'June',
        '07' => 'July',
        '08' => 'August',
        '09' => 'September',
        '10' => 'October',
        '11' => 'November',
        '12' => 'December',
    ],
    'monthval' => [
        '1' => 'January',
        '2' => 'February',
        '3' => 'March',
        '4' => 'April',
        '5' => 'May',
        '6' => 'June',
        '7' => 'July',
        '8' => 'August',
        '9' => 'September',
        '10' => 'October',
        '11' => 'November',
        '12' => 'December',
    ],
    'category' => [
        'ALL' => 'ALL',
        'GENERAL' => 'GENERAL',
        'SC' => 'SC',
        'ST' => 'ST',
    ],
    'lot_size' => [
        '10' => '10',
        '20' => '20',
        '50' => '50',
        '100' => '100',
        '500' => '500',
        '1000' => '1000',
        '5000' => '5000',
        '10000' => '10000',

    ],
    'schemecodeStatic' => [
        'purohitmonthly' => array("scheme_code" => '12', "name" => 'Monthly Scheme', 'slug' => 'monthly', 'maintable' => 'PensionPurohitMonthlyICAD', 'doctable' => 'BenDocsPurohitMonthlyICAD', 'docarctable' => 'BenDocsArcPurohitMonthlyICAD'),
        'purohithousing' => array("scheme_code" => '13', "name" => 'One time Housing Scheme', 'slug' => 'housing', 'maintable' => 'PensionPurohitHousingICAD', 'doctable' => 'BenDocsPurohitHousingICAD', 'docarctable' => 'BenDocsArcPurohitHousingICAD'),
        'purohitboth' => array("scheme_code" => '14', "name" => 'Both', 'slug' => 'both', 'maintable' => 'PensionPurohitHousingICAD')
    ],
    'site_title' => 'Lakshmir Bhandar',
    'site_titleShort' => 'Lakshmir Bhandar',
    'lb_source' => [
        // 'nfsa' => 'NFSA (Khadyasathi)',
        'ss_nfsa' => 'Swasthya Sathi',

    ],
    'religion' => [
        'Hinduism' => 'Hinduism',
        'Islam' => 'Islam',
        'Christianity' => 'Christianity',
        'Sikhism' => 'Sikhism',
        'Buddhism' => 'Buddhism',
        'Jainism' => 'Jainism',
        'Unaffiliated' => 'Unaffiliated',
        'Others' => 'Others'
    ],
    'rejection_cause' => [
        '1' => 'Bank Information Not Valid',
        '2' => 'Duplicate Ration Card',
        '20' => 'Others'
    ],
    'user_audit_trail_code' => [
        'Update' => 1,
        'Delete' => 2
    ],
    'errormsg' => [
        'roolback' => 'Error Occur .. Please try later..',
        'frmjsonnexists' => 'Error Occur .. Please try later..',
        'notValid' => 'is Not Valid',
        'notFound' => 'Not Found',
        'notauthorized' => 'You are not Authorized',
        'applicationidnotfound' => 'Application Id not Found',
        'applicationalreadyverified' => 'Application already verified.. you cannot edit it.',
        'sessiontimeOut' => 'Something wrong..may be session timeout. please logout and then login again',
    ],
    'departmentschememapping' => [
        '1' => array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19),
        '2' => array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19),
        '3' => array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19),
        '4' => array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19),
        '5' => array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19),
    ],
    'not_eligible_cause' => [
        '1' => 'Exclusion as per Scheme Guideline',
        '2' => 'She has died',
        '3' => 'She has been married and stays elsewhere',
        '4' => 'She has not attained the age of 18 years',
        '5' => 'She is a not a citizen of India and/or has not been a resident of West Bengal for the last 10
        years',
        '6' => 'She earns any monthly remuneration from any regular/ contractual Government job or gets 
        any Pension under any Government scheme',
        '7' => 'Others (Please specify)'
    ],
    'bandhan_response_code' => [
        '01'     => "Account Closed or Transferred",
        '02'     => "No Such Account",
        '03'     => "Account Description Does not Tally",
        '04'     => "Miscellaneous - Others",
        '51' =>     "Miscellaneous - KYC Documents Pending",
        '52' =>     "Miscellaneous - Documents Pending for Account Holder turning Major",
        '53' =>     "Miscellaneous - A/c Inactive (No Transactions for last 3 Months)",
        '54' =>     "Miscellaneous - Dormant A/c (No Transactions for last 6 Months)",
        '55'     => "Miscellaneous - A/c in Zero Balance/No Transactions have Happened",
        '56' =>     "Miscellaneous - Simple Account",
        '57' =>     "Miscellaneous - Amount Exceeds limit set on Account by Bank for Credit per Transaction",
        '58' => "Miscellaneous - Account reached maximum Credit limit set on account by Bank",
        '59' =>     "Miscellaneous - Network Failure (CBS)",
        '60' =>     "Account Holder Expired",
        '61'     => "Mandate Cancelled",
        '62' =>     "Account Under Litigation",
        '63' =>     "Invalid Aadhaar Number",
        '64' =>     "Aadhaar Number not Mapped to Account Number",
        '65' =>     "Account Holder Name Invalid",
        '66' =>     "UMRN Does not exist",
        '68' =>     "A/c Blocked or Frozen",
        '99' =>     "Mark Pending",
    ],
    'ds_phase' => [
        'phaselist' => array('2' => 'Phase II', '3' => 'Phase III'),
        'cur_phase' => 3
    ],
    'lb_dob' => [
        'base_dob_chk_date' => '2022-01-01',
        'max_dob' => '1997-01-01',
        'min_dob' => '1962-01-01'
    ],
    'duare_sarkar_phase' => [
        '2' => 'Phase 2',
        '3' => 'Phase 3',
	    '4' => 'Phase 4',
        '5' => 'Special camps: Phase 4',
	    '6' => 'Phase 5',
	    '7' => 'Phase 6',
	'8' => 'Phase 7'
    ],
    'academic_year' => [
        '2020' => '2020',
        '2021' => '2021',
        '2022' => '2022',
        '2023' => '2023',
        '2024' => '2024',
        '2025' => '2025',
    ],
    'EncryptionKey'=>'wltHCqXLI0rTIZtjY2tw9FuglpZFIcHzBhBZAG9ADKw=',
];
