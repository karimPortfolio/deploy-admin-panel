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
    'notifications' => [
        'new_user' => [
            'mail' => [
                'subject' => 'Bienvenue sur :appName',
                'greeting' => 'Bonjour :name,',
                'line1' => 'Nous sommes ravis de vous accueillir ! Votre compte a été créé avec succès. Vous pouvez commencer en vous connectant à notre application en utilisant votre email et votre mot de passe temporaire : :password',
                'line2' => 'Veuillez vous assurer de changer votre mot de passe après votre première connexion.',
                'action' => 'Se connecter maintenant',
                'line3' => 'Merci d\'utiliser notre application !',
            ]
        ],
        'account_activation' => [
            'mail' => [
                'subject' => 'Compte activé',
                'greeting' => 'Bonjour :name,',
                'line1' => 'Nous avons le plaisir de vous informer que votre compte a été activé par notre équipe d\'administration. Bienvenue à nouveau ! et nous sommes ravis de vous revoir.',
                'action' => 'Visitez votre tableau de bord',
                'line2' => 'Merci d\'utiliser notre application !',
            ],
            'database' => [
                'title' => 'Compte activé',
                'body' => 'votre compte a été activé à nouveau par notre équipe d\'administration.',
                'actionLabel' => 'Vérifiez votre tableau de bord',
            ]
        ],
        'account_deactivation' => [
            'mail' => [
                'subject' => 'Compte désactivé',
                'greeting' => 'Bonjour :name,',
                'line1' => 'Nous regrettons de vous informer que votre compte a été désactivé par notre équipe d\'administration. Si vous pensez qu\'il s\'agit d\'une erreur ou si vous avez des questions, veuillez contacter notre équipe de support pour obtenir de l\'aide.',
                'line2' => 'Merci d\'utiliser notre application !',
            ],
            'database' => [
                'title' => 'Compte désactivé',
                'body' => 'votre compte a été désactivé par notre équipe d\'administration.',
                'actionLabel' => 'Contacter le support',
            ]
        ],
        'deleted_resource' => [
            'mail' => [
                'subject' => 'Ressource supprimée',
                'greeting' => 'Bonjour :name,',
                'line1' => 'Nous voulions vous faire savoir que votre :resourceType (ID : :resourceId) a été supprimé(e) par notre équipe d\'administration.',
                'action' => 'Vérifiez votre tableau de bord',
                'line2' => 'Merci d\'utiliser notre application !',
            ],
            'database' => [
                'title' => 'Ressource supprimée',
                'body' => 'Votre :resourceType (ID : :resourceId) a été supprimé(e) par notre équipe d\'administration.',
                'actionLabel' => 'Vérifiez votre tableau de bord',
            ]
        ],
        'unused_resources_alert' => [
            'mail' => [
                'subject' => 'Audit hebdomadaire – Examiner les ressources inutilisées',
                'greeting' => 'Bonjour :name,',
                'line1' => 'Votre rapport d\'analyse hebdomadaire sur les ressources inutilisées est prêt.',
                'line2' => 'Cette semaine, un total de :totalUnusedResources ressources inutilisées ont été identifiées :',
                'securityGroupsLine' => '- :unusedSecurityGroupsCount groupes de sécurité inutilisés trouvés.',
                'sshKeysLine' => '- :unusedSshKeysCount clés SSH inutilisées trouvées.',
                'action' => 'Vérifiez votre tableau de bord',
                'line3' => 'Merci d\'utiliser :appName.',
            ],
            'database' => [
                'title' => 'Ressources inutilisées détectées',
                'body' => 'Cette semaine, un total de :totalUnusedResources ressources inutilisées ont été identifiées.',
                'actionLabel' => 'Vérifiez votre tableau de bord',
            ]
        ],
        'password_reset' => [
            'mail' => [
                'subject' => 'Notification de réinitialisation du mot de passe',
                'line1' => 'Cliquez sur le bouton ci-dessous pour réinitialiser votre mot de passe :',
                'action' => 'Réinitialiser le mot de passe',
                'line2' => 'Si vous n\'avez pas demandé de réinitialisation de mot de passe, aucune action supplémentaire n\'est requise.',
            ]
        ],
    ],
    'middlewares' => [
        'account_inactive' => 'Le compte est inactif. Veuillez contacter le support.',
    ]
];