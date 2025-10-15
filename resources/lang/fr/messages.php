<?php

return [
    'servers' => [
        'servers_creation_msg' => "Création du serveur en cours. Vous pouvez vérifier le statut plus tard.",
        'failed_to_start_msg' => "Échec du démarrage de l'instance.",
        'failed_to_stop_msg'  => "Échec de l'arrêt de l'instance.",
        'failed_to_terminate_msg' => "Échec de la terminaison de l'instance.",
        'instance_type' => [
            'description' => [
                'T2Micro'    => 'Éligible au niveau gratuit, usage général',
                'T3Micro'    => 'Usage général, performance modulable',
                'T3Small'    => 'Petit usage général',
                'T3Medium'   => 'Usage général moyen',
                'C5Large'    => 'Optimisé pour le calcul, bon pour les charges de travail haute performance',
                'C5Xlarge'   => 'Optimisé pour le calcul, capacité supplémentaire',
                'R5Large'    => 'Optimisé pour la mémoire, bon pour les applications intensives en données',
                'R5Xlarge'   => 'Optimisé pour la mémoire, plus de RAM',
                'G4dnXlarge' => 'Instance GPU, bon pour ML ou charges de travail graphiques',
            ],
        ],
        'status' => [
            'running'    => 'En cours d\'exécution',
            'stopped'    => 'Arrêté',
            'terminated' => 'Terminé',
            'pending'    => 'En attente',
        ],
    ],
    'users' => [
        'roles' => [
            'admin' => 'Administrateur',
            'user'  => 'Utilisateur',
        ],
    ],
    'security_groups' => [
        'associated_servers_msg' => "Ce groupe de sécurité est associé à des serveurs et ne peut pas être supprimé.",
    ],
    'ssh_keys' => [
        'associated_servers_msg' => "Cette clé SSH est associée à des serveurs et ne peut pas être supprimée.",
    ],
];