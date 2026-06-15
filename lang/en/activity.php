<?php

return [
    'title'       => 'Activity Log',
    'subtitle'    => 'System and user activity history',
    'empty'       => 'No activity recorded yet.',
    'event'       => 'Event',
    'description' => 'Description',
    'user'        => 'User',
    'subject'     => 'Subject',
    'when'        => 'When',
    'system'      => 'System',
    'log'         => [
        'user_created'        => 'Created user :name (:email)',
        'user_updated'        => 'Updated user :name (:email)',
        'user_suspended'      => 'Suspended user :name (:email)',
        'user_activated'      => 'Activated user :name (:email)',
        'user_deleted'        => 'Deleted user :name (:email)',
        'user_restored'       => 'Restored user :name (:email)',
        'role_created'        => "Role ':role' created",
        'role_deleted'        => "Role ':role' deleted",
        'permissions_updated' => 'Permissions matrix updated by :name',
        'settings_updated'    => 'System settings updated by :name',
        'profile_updated'     => 'Profile updated for :name (:email)',
        'user_logged_in'      => ':name logged in',
        'user_logged_out'     => ':name logged out',
    ],
];
