<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'apihub' => [
        'apihub_token' => env('APIHUB_TOKEN'),
        'apihub_apiname' => env('APIHUB_APINAME'),
        'apihub_host' => env('APIHUB_HOST'),
    ],
    'oauthapiendpointkiotviet' => [
        'kiotviet_token_url' => env('KIOTVIET_TOKEN_URL'),
        'kiotviet_client_id' => env('KIOTVIET_CLIENT_ID'),
        'kiotviet_client_secret' => env('KIOTVIET_CLIENT_SECRET'),
        'kiotviet_scopes' => env('KIOTVIET_SCOPES'),
        'kiotviet_retailer' => env('KIOTVIET_RETAILER'),
        'kiotviet_apiname' => env('KIOTVIET_APINAME'),
        'kiotviet_branchId' => env('KIOTVIET_BRANCHID'),
        'kiotviet_host' => env('KIOTVIET_HOST'),
    ],
    'oauthapiendpointshipstation' => [
        'shipstation_client_id' => env('SHIPSTATION_CLIENT_ID'),
        'shipstation_client_secret' => env('SHIPSTATION_SECRET'),
        'shipstation_apiname' => env('SHIPSTATION_APINAME'),
        'shipstation_host' => env('SHIPSTATION_HOST'),
        'shipstation_store_id' => env('SHIPSTATION_STOREID'),

    ],

    'oauthapiendpoint1office' => [
        '1office_access_token' => env('1OFFICE_ACCESS_TOKEN'),
        '1office_apiname' => env('1OFFICE_APINAME'),
        '1office_host' => env('1OFFICE_HOST'),
        '1office_cf_country' => env('1OFFICE_CF_COUNTRY'),
        '1office_cf_address_line_1' => env('1OFFICE_CF_ADDRESS_LINE_1'),
        '1office_cf_address_line_2' => env('1OFFICE_CF_ADDRESS_LINE_2'),
        '1office_cf_city' => env('1OFFICE_CF_CITY'),
        '1office_cf_state' => env('1OFFICE_CF_STATE'),
        '1office_cf_zip_Code' => env('1OFFICE_CF_ZIP_CODE'),
        '1office_cf_address_verification_status' => env('1OFFICE_CF_ADDRESS_VERIFICATION_STATUS'),
        '1office_cf_order_status' => env('1OFFICE_CF_ORDERS_STATUS'),
        '1office_cf_order_address_full' => env('1OFFICE_CF_ORDERS_ADDRESS_FULL'),
        '1office_cf_order_internal_notes' => env('1OFFICE_CF_ORDERS_INTERNAL_NOTES'),
        '1office_cf_order_customer_notes' => env('1OFFICE_CF_ORDERS_CUSTOMER_NOTES'),
        '1office_cf_order_rate' => env('1OFFICE_CF_ORDERS_RATE'),
        '1office_cf_order_tracking_number' => env('1OFFICE_CF_ORDERS_TRACKING_NUMBER'),
    ],

];
