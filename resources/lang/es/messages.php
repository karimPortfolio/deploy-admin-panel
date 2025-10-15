<?php

return [
    'servers' => [
        'servers_creation_msg' => "Creación de servidor en progreso. Puedes verificar el estado más tarde.",
        'failed_to_start_msg' => "Error al iniciar la instancia.",
        'failed_to_stop_msg'  => "Error al detener la instancia.",
        'failed_to_terminate_msg' => "Error al terminar la instancia.",
        'instance_type' => [
            'description' => [
                'T2Micro'    => 'Elegible para nivel gratuito, propósito general',
                'T3Micro'    => 'Propósito general, rendimiento escalable',
                'T3Small'    => 'Propósito general pequeño',
                'T3Medium'   => 'Propósito general mediano',
                'C5Large'    => 'Optimizado para cómputo, bueno para cargas de trabajo de alto rendimiento',
                'C5Xlarge'   => 'Optimizado para cómputo, capacidad extra',
                'R5Large'    => 'Optimizado para memoria, bueno para aplicaciones intensivas en datos',
                'R5Xlarge'   => 'Optimizado para memoria, más RAM',
                'G4dnXlarge' => 'Instancia GPU, buena para cargas de trabajo de ML o gráficos',
            ],
        ],
        'status' => [
            'running'    => 'Ejecutándose',
            'stopped'    => 'Detenido',
            'terminated' => 'Terminado',
            'pending'    => 'Pendiente',
        ],
    ],
    'users' => [
        'roles' => [
            'admin' => 'Administrador',
            'user'  => 'Usuario',
        ],
    ],
    'security_groups' => [
        'associated_servers_msg' => "Este grupo de seguridad está asociado con servidores y no puede ser eliminado.",
    ],
    'ssh_keys' => [
        'associated_servers_msg' => "Esta clave SSH está asociada con servidores y no puede ser eliminada.",
    ],
];