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
    'notifications' => [
        'new_user' => [
            'mail' => [
                'subject' => 'Bienvenido a :appName',
                'greeting' => 'Hola :name,',
                'line1' => '¡Estamos emocionados de tenerte a bordo! Tu cuenta ha sido creada exitosamente. Puedes comenzar iniciando sesión en nuestra aplicación usando tu email y tu contraseña temporal: :password',
                'line2' => 'Por favor asegúrate de cambiar tu contraseña después de tu primer inicio de sesión.',
                'action' => 'Iniciar Sesión Ahora',
                'line3' => '¡Gracias por usar nuestra aplicación!',
            ]
        ],
        'account_activation' => [
            'mail' => [
                'subject' => 'Cuenta Activada',
                'greeting' => 'Hola :name,',
                'line1' => 'Nos complace informarte que tu cuenta ha sido activada por nuestro equipo de Administración. ¡Bienvenido de nuevo! y estamos emocionados de tenerte de vuelta.',
                'action' => 'Visita tu panel',
                'line2' => '¡Gracias por usar nuestra aplicación!',
            ],
            'database' => [
                'title' => 'Cuenta Activada',
                'body' => 'tu cuenta ha sido activada una vez más por nuestro equipo de Administración.',
                'actionLabel' => 'Revisa Tu Panel',
            ]
        ],
        'account_deactivation' => [
            'mail' => [
                'subject' => 'Cuenta Desactivada',
                'greeting' => 'Hola :name,',
                'line1' => 'Lamentamos informarte que tu cuenta ha sido desactivada por nuestro equipo de Administración. Si crees que esto es un error o tienes alguna pregunta, por favor contacta a nuestro equipo de soporte para obtener asistencia.',
                'line2' => '¡Gracias por usar nuestra aplicación!',
            ],
            'database' => [
                'title' => 'Cuenta Desactivada',
                'body' => 'tu cuenta ha sido desactivada por nuestro equipo de Administración.',
                'actionLabel' => 'Contactar Soporte',
            ]
        ],
        'deleted_resource' => [
            'mail' => [
                'subject' => 'Recurso Eliminado',
                'greeting' => 'Hola :name,',
                'line1' => 'Queríamos informarte que tu :resourceType (ID: :resourceId) ha sido eliminado por nuestro equipo de administración.',
                'action' => 'Revisa Tu Panel',
                'line2' => '¡Gracias por usar nuestra aplicación!',
            ],
            'database' => [
                'title' => 'Recurso Eliminado',
                'body' => 'Tu :resourceType (ID: :resourceId) ha sido eliminado por nuestro equipo de administración.',
                'actionLabel' => 'Revisa Tu Panel',
            ]
        ],
        'unused_resources_alert' => [
            'mail' => [
                'subject' => 'Auditoría Semanal – Revisar Recursos No Utilizados',
                'greeting' => 'Hola :name,',
                'line1' => 'Tu reporte de análisis semanal sobre recursos no utilizados está listo.',
                'line2' => 'Esta semana, se identificaron un total de :totalUnusedResources recursos no utilizados:',
                'securityGroupsLine' => '- :unusedSecurityGroupsCount Grupos de Seguridad no utilizados encontrados.',
                'sshKeysLine' => '- :unusedSshKeysCount Claves SSH no utilizadas encontradas.',
                'action' => 'Revisa Tu Panel',
                'line3' => 'Gracias por usar :appName.',
            ],
            'database' => [
                'title' => 'Recursos No Utilizados Detectados',
                'body' => 'Esta semana, se identificaron un total de :totalUnusedResources recursos no utilizados.',
                'actionLabel' => 'Revisa Tu Panel',
            ]
        ],
        'password_reset' => [
            'mail' => [
                'subject' => 'Notificación de Restablecimiento de Contraseña',
                'line1' => 'Haz clic en el botón de abajo para restablecer tu contraseña:',
                'action' => 'Restablecer Contraseña',
                'line2' => 'Si no solicitaste un restablecimiento de contraseña, no se requiere ninguna acción adicional.',
            ]
        ],
    ],
    'middlewares' => [
        'account_inactive' => 'La cuenta está inactiva. Por favor contacta a soporte.',
    ]
];