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
];