<?php

return [
    'version' => '0.5.0',

    'license_server' => env('DRAVION_LICENSE_SERVER', 'https://apsbg.com/dravion-server'),
    'updates_server' => env('DRAVION_UPDATES_SERVER', 'https://apsbg.com/dravion-server'),

    'license_key'    => env('DRAVION_LICENSE_KEY', ''),
    'licensed_domain'=> env('DRAVION_LICENSED_DOMAIN', ''),
];
