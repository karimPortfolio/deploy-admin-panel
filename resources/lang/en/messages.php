<?php

return [
    'servers' => [
        'servers_creation_msg' => "Server creation in progress. You can check the status later.",
        'failed_to_start_msg' => "Failed to start the instance.",
        'failed_to_stop_msg'  => "Failed to stop the instance.",
        'failed_to_terminate_msg' => "Failed to terminate the instance.",
        'instance_type' => [
            'description' => [
                'T2Micro'    => 'Free-tier eligible, general purpose',
                'T3Micro'    => 'General purpose, burstable performance',
                'T3Small'    => 'Small general purpose',
                'T3Medium'   => 'Medium general purpose',
                'C5Large'    => 'Compute-optimized, good for high-performance workloads',
                'C5Xlarge'   => 'Compute-optimized, extra capacity',
                'R5Large'    => 'Memory-optimized, good for data-intensive apps',
                'R5Xlarge'   => 'Memory-optimized, more RAM',
                'G4dnXlarge' => 'GPU instance, good for ML or graphics workloads',
            ],
        ],
        'status' => [
            'running'    => 'Running',
            'stopped'    => 'Stopped',
            'terminated' => 'Terminated',
            'pending'    => 'Pending',
        ],
    ],
    'users' => [
        'roles' => [
            'admin' => 'Admin',
            'user'  => 'User',
        ],
    ],
    'security_groups' => [
        'associated_servers_msg' => "This Security group is associated with servers and cannot be deleted.",
    ],
    'ssh_keys' => [
        'associated_servers_msg' => "This SSH key is associated with servers and cannot be deleted.",
    ],
    'notifications' => [
        'new_user' => [
            'mail' => [
                'subject' => 'Welcome to :appName',
                'greeting' => 'Hello :name,',
                'line1' => 'We are excited to have you on board! Your account has been successfully created. You can get started by signing in to our application using your email and your temporary password: :password',
                'line2' => 'Please make sure to change your password after your first login.',
                'action' => 'Sign-in Now',
                'line3' => 'Thank you for using our application!',
            ]
        ],
        'account_activation' => [
            'mail' => [
                'subject' => 'Account Activated',
                'greeting' => 'Hello :name,',
                'line1' => 'We are pleased to inform you that your account has been activated by our Administration team. Welcome aboard again! and we are excited to have you back.',
                'action' => 'Visit your dashboard',
                'line2' => 'Thank you for using our application!',
            ],
            'database' => [
                'title' => 'Account Activated',
                'body' => 'your account has been activated once again by our Administration team.',
                'actionLabel' => 'Check Your Dashboard',
            ]
        ],
        'account_deactivation' => [
            'mail' => [
                'subject' => 'Account Deactivated',
                'greeting' => 'Hello :name,',
                'line1' => 'We are regret to inform you that your account has been deactivated by our Administration team. If you believe this is a mistake or have any questions, please contact our support team for assistance.',
                'line2' => 'Thank you for using our application!',
            ],
            'database' => [
                'title' => 'Account Deactivated',
                'body' => 'your account has been deactivated by our Administration team.',
                'actionLabel' => 'Contact Support',
            ]
        ],
        'deleted_resource' => [
            'mail' => [
                'subject' => 'Resource Deleted',
                'greeting' => 'Hello :name,',
                'line1' => 'We wanted to let you know that your :resourceType (ID: :resourceId) has been deleted by our administration team.',
                'action' => 'Check Your Dashboard',
                'line2' => 'Thank you for using our application!',
            ],
            'database' => [
                'title' => 'Resource Deleted',
                'body' => 'Your :resourceType (ID: :resourceId) has been deleted by our administration team.',
                'actionLabel' => 'Check Your Dashboard',
            ]
        ],
        'unused_resources_alert' => [
            'mail' => [
                'subject' => 'Weekly Audit – Review Unused Resources',
                'greeting' => 'Hello :name,',
                'line1' => 'Your weekly analytics report on unused resources is ready.',
                'line2' => 'This week, a total of :totalUnusedResources unused resources were identified:',
                'securityGroupsLine' => '- :unusedSecurityGroupsCount unused Security Groups found.',
                'sshKeysLine' => '- :unusedSshKeysCount unused SSH Keys found.',
                'action' => 'Check Your Dashboard',
                'line3' => 'Thank you for using :appName.',
            ],
            'database' => [
                'title' => 'Unused Resources Detected',
                'body' => 'This week, a total of :totalUnusedResources unused resources were identified.',
                'actionLabel' => 'Check Your Dashboard',
            ]
        ],
        'password_reset' => [
            'mail' => [
                'subject' => 'Reset Password Notification',
                'line1' => 'Click the button below to reset your password:',
                'action' => 'Reset Password',
                'line2' => 'If you did not request a password reset, no further action is required.',
            ]
        ],
    ],
    'middlewares' => [
        'account_inactive' => 'Account is inactive. Please contact support.',
    ]
];