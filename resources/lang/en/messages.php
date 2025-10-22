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
    'rds_databases' => [
        'instance_class' => [
            'description' => [
                'T4gMicro' => 'T4g Micro – 2 vCPU, 1 GB RAM (Graviton2, Free‑Tier eligible)',
                'T4gSmall' => 'T4g Small – 2 vCPU, 2 GB RAM (Graviton2, low‑cost)',
                'T4gMedium' => 'T4g Medium – 2 vCPU, 4 GB RAM (Graviton2)',
                'T3Micro' => 'T3 Micro – 2 vCPU, 1 GB RAM (Free‑Tier eligible)',
                'T3Small' => 'T3 Small – 2 vCPU, 2 GB RAM',
                'T3Medium' => 'T3 Medium – 2 vCPU, 4 GB RAM',
                'T3Large' => 'T3 Large – 2 vCPU, 8 GB RAM',
                'T3Xlarge' => 'T3 XLarge – 4 vCPU, 16 GB RAM',
                'T32xlarge' => 'T3 2XLarge – 8 vCPU, 32 GB RAM',
                'T2Micro' => 'T2 Micro – 1 vCPU, 1 GB RAM (legacy Free‑Tier)',
                'T2Small' => 'T2 Small – 1 vCPU, 2 GB RAM',
                'T2Medium' => 'T2 Medium – 2 vCPU, 4 GB RAM',
                'M6gLarge' => 'M6g Large – 2 vCPU, 8 GB RAM (Graviton2)',
                'M6gXlarge' => 'M6g XLarge – 4 vCPU, 16 GB RAM',
                'M6g2xlarge' => 'M6g 2XLarge – 8 vCPU, 32 GB RAM',
                'M6g4xlarge' => 'M6g 4XLarge – 16 vCPU, 64 GB RAM',
                'M6g8xlarge' => 'M6g 8XLarge – 32 vCPU, 128 GB RAM',
                'M6g12xlarge' => 'M6g 12XLarge – 48 vCPU, 192 GB RAM',
                'M6g16xlarge' => 'M6g 16XLarge – 64 vCPU, 256 GB RAM',
                'M5Large' => 'M5 Large – 2 vCPU, 8 GB RAM (Intel Xeon, balanced)',
                'M5Xlarge' => 'M5 XLarge – 4 vCPU, 16 GB RAM',
                'M52xlarge' => 'M5 2XLarge – 8 vCPU, 32 GB RAM',
                'M54xlarge' => 'M5 4XLarge – 16 vCPU, 64 GB RAM',
                'M58xlarge' => 'M5 8XLarge – 32 vCPU, 128 GB RAM',
                'M512xlarge' => 'M5 12XLarge – 48 vCPU, 192 GB RAM',
                'M516xlarge' => 'M5 16XLarge – 64 vCPU, 256 GB RAM',
                'M524xlarge' => 'M5 24XLarge – 96 vCPU, 384 GB RAM',
                'M4Large' => 'M4 Large – 2 vCPU, 8 GB RAM (older generation)',
                'M4Xlarge' => 'M4 XLarge – 4 vCPU, 16 GB RAM',
                'M42xlarge' => 'M4 2XLarge – 8 vCPU, 32 GB RAM',
                'M44xlarge' => 'M4 4XLarge – 16 vCPU, 64 GB RAM',
                'M410xlarge' => 'M4 10XLarge – 40 vCPU, 160 GB RAM',
                'M416xlarge' => 'M4 16XLarge – 64 vCPU, 256 GB RAM',
                'R6gLarge' => 'R6g Large – 2 vCPU, 16 GB RAM (Graviton2)',
                'R6gXlarge' => 'R6g XLarge – 4 vCPU, 32 GB RAM',
                'R6g2xlarge' => 'R6g 2XLarge – 8 vCPU, 64 GB RAM',
                'R6g4xlarge' => 'R6g 4XLarge – 16 vCPU, 128 GB RAM',
                'R6g8xlarge' => 'R6g 8XLarge – 32 vCPU, 256 GB RAM',
                'R6g12xlarge' => 'R6g 12XLarge – 48 vCPU, 384 GB RAM',
                'R6g16xlarge' => 'R6g 16XLarge – 64 vCPU, 512 GB RAM',
                'R5Large' => 'R5 Large – 2 vCPU, 16 GB RAM (Intel Xeon)',
                'R5Xlarge' => 'R5 XLarge – 4 vCPU, 32 GB RAM',
                'R52xlarge' => 'R5 2XLarge – 8 vCPU, 64 GB RAM',
                'R54xlarge' => 'R5 4XLarge – 16 vCPU, 128 GB RAM',
                'R58xlarge' => 'R5 8XLarge – 32 vCPU, 256 GB RAM',
                'R512xlarge' => 'R5 12XLarge – 48 vCPU, 384 GB RAM',
                'R516xlarge' => 'R5 16XLarge – 64 vCPU, 512 GB RAM',
                'R524xlarge' => 'R5 24XLarge – 96 vCPU, 768 GB RAM',
                'X2gLarge' => 'X2g Large – 2 vCPU, 32 GB RAM (Graviton2 high‑mem)',
                'X2gXlarge' => 'X2g XLarge – 4 vCPU, 64 GB RAM',
                'X2g2xlarge' => 'X2g 2XLarge – 8 vCPU, 128 GB RAM',
                'X2g4xlarge' => 'X2g 4XLarge – 16 vCPU, 256 GB RAM',
                'X116xlarge' => 'X1 16XLarge – 64 vCPU, 976 GB RAM (legacy high‑mem)',
                'Z1dLarge' => 'Z1d Large – 2 vCPU, 16 GB RAM (high single‑thread CPU)',
                'Z1dXlarge' => 'Z1d XLarge – 4 vCPU, 32 GB RAM',
                'Z1d2xlarge' => 'Z1d 2XLarge – 8 vCPU, 64 GB RAM',
                'Z1d3xlarge' => 'Z1d 3XLarge – 12 vCPU, 96 GB RAM',
                'Z1d6xlarge' => 'Z1d 6XLarge – 24 vCPU, 192 GB RAM',
                'Z1d12xlarge' => 'Z1d 12XLarge – 48 vCPU, 384 GB RAM',
            ],
        ],
        'storage_type' => [
            'gp2' => 'General Purpose (SSD) – Balanced price and performance; Free‑Tier eligible and default type.',
            'gp3' => 'General Purpose (SSD gp3) – Next‑generation SSD with lower cost and configurable performance; Free‑Tier eligible.',
            'io1' => 'Provisioned IOPS (SSD) – Dedicated high‑throughput storage for mission‑critical, I/O‑intensive workloads; higher cost.',
            'standard' => 'Magnetic (HDD) – Older, slower magnetic storage. Cheap but not recommended for new deployments.',
        ],
        'engines' => [
            'description' => [
                'mysql' => "MySQL – Most popular open-source relational database. Widely supported, easy integration. Free‑Tier eligible.",
                'postgres' => "PostgreSQL – Advanced open-source database with strong JSON and indexing features. Highly reliable and scalable. Free‑Tier eligible.",
                'mariadb' => "MariaDB – MySQL-compatible engine with extra performance features and open governance. Free‑Tier eligible.",
                'aurora_mysql' => "Amazon Aurora (MySQL compatible) – High‑performance clustered MySQL engine with automatic scaling.",
                'aurora_postgresql' => "Amazon Aurora (PostgreSQL compatible) – Aurora's managed PostgreSQL alternative.",
                'aurora' => "Amazon Aurora (legacy generic) – Used only for older API calls; prefer aurora-mysql/aurora-postgresql.",
                'oracle_se2' => "Oracle SE2 (Standard Edition 2) – Commercial Oracle Database with basic features enabled.",
                'oracle_ee' => "Oracle EE (Enterprise Edition) – Full Oracle Database, enterprise capabilities (licensing costs apply).",
                'sqlserver_ex' => "Microsoft SQL Server Express – Free entry‑level SQL Server, limited resources.",
                'sqlserver_web' => "Microsoft SQL Server Web Edition – Optimized for web workloads, lower cost.",
                'sqlserver_se' => "Microsoft SQL Server Standard Edition – Balanced license option for production.",
                'sqlserver_ee' => "Microsoft SQL Server Enterprise Edition – Full premium SQL Server feature set.",
            ],
        ],
        'status' => [
            'started'    => 'Started',
            'stopped'     => 'Stopped',
            'backing_up'  => 'Backing Up',
            'pending'    => 'Pending',
            'failed'      => 'Failed',
        ],
        'associated_servers_msg' => "This RDS Database is associated with servers and cannot be deleted.",
        'delete_failed_msg' => "Failed to delete the RDS database. Please try again later.",
        'db_name_regex' => 'The database name must begin with a letter and contain only letters and numbers (no spaces or special characters).',
        'rds_database_id_unique' => 'This database is already attached to the selected server.',
        'rds_server_id_unique' => 'This server is already attached to the selected database.'
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