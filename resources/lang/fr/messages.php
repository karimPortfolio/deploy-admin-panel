<?php

return [
    'days' => 'Jours',
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
    'rds_databases' => [
        'instance_class' => [
            'description' => [
                'T4gMicro' => 'T4g Micro – 2 vCPU, 1 GB RAM (Graviton2, éligible au niveau gratuit)',
                'T4gSmall' => 'T4g Small – 2 vCPU, 2 GB RAM (Graviton2, faible coût)',
                'T4gMedium' => 'T4g Medium – 2 vCPU, 4 GB RAM (Graviton2)',
                'T3Micro' => 'T3 Micro – 2 vCPU, 1 GB RAM (éligible au niveau gratuit)',
                'T3Small' => 'T3 Small – 2 vCPU, 2 GB RAM',
                'T3Medium' => 'T3 Medium – 2 vCPU, 4 GB RAM',
                'T3Large' => 'T3 Large – 2 vCPU, 8 GB RAM',
                'T3Xlarge' => 'T3 XLarge – 4 vCPU, 16 GB RAM',
                'T32xlarge' => 'T3 2XLarge – 8 vCPU, 32 GB RAM',
                'T2Micro' => 'T2 Micro – 1 vCPU, 1 GB RAM (ancien niveau gratuit)',
                'T2Small' => 'T2 Small – 1 vCPU, 2 GB RAM',
                'T2Medium' => 'T2 Medium – 2 vCPU, 4 GB RAM',
                'M6gLarge' => 'M6g Large – 2 vCPU, 8 GB RAM (Graviton2)',
                'M6gXlarge' => 'M6g XLarge – 4 vCPU, 16 GB RAM',
                'M6g2xlarge' => 'M6g 2XLarge – 8 vCPU, 32 GB RAM',
                'M6g4xlarge' => 'M6g 4XLarge – 16 vCPU, 64 GB RAM',
                'M6g8xlarge' => 'M6g 8XLarge – 32 vCPU, 128 GB RAM',
                'M6g12xlarge' => 'M6g 12XLarge – 48 vCPU, 192 GB RAM',
                'M6g16xlarge' => 'M6g 16XLarge – 64 vCPU, 256 GB RAM',
                'M5Large' => 'M5 Large – 2 vCPU, 8 GB RAM (Intel Xeon, équilibré)',
                'M5Xlarge' => 'M5 XLarge – 4 vCPU, 16 GB RAM',
                'M52xlarge' => 'M5 2XLarge – 8 vCPU, 32 GB RAM',
                'M54xlarge' => 'M5 4XLarge – 16 vCPU, 64 GB RAM',
                'M58xlarge' => 'M5 8XLarge – 32 vCPU, 128 GB RAM',
                'M512xlarge' => 'M5 12XLarge – 48 vCPU, 192 GB RAM',
                'M516xlarge' => 'M5 16XLarge – 64 vCPU, 256 GB RAM',
                'M524xlarge' => 'M5 24XLarge – 96 vCPU, 384 GB RAM',
                'M4Large' => 'M4 Large – 2 vCPU, 8 GB RAM (ancienne génération)',
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
                'X2gLarge' => 'X2g Large – 2 vCPU, 32 GB RAM (Graviton2 haute mémoire)',
                'X2gXlarge' => 'X2g XLarge – 4 vCPU, 64 GB RAM',
                'X2g2xlarge' => 'X2g 2XLarge – 8 vCPU, 128 GB RAM',
                'X2g4xlarge' => 'X2g 4XLarge – 16 vCPU, 256 GB RAM',
                'X116xlarge' => 'X1 16XLarge – 64 vCPU, 976 GB RAM (ancienne haute mémoire)',
                'Z1dLarge' => 'Z1d Large – 2 vCPU, 16 GB RAM (CPU haute performance mono-thread)',
                'Z1dXlarge' => 'Z1d XLarge – 4 vCPU, 32 GB RAM',
                'Z1d2xlarge' => 'Z1d 2XLarge – 8 vCPU, 64 GB RAM',
                'Z1d3xlarge' => 'Z1d 3XLarge – 12 vCPU, 96 GB RAM',
                'Z1d6xlarge' => 'Z1d 6XLarge – 24 vCPU, 192 GB RAM',
                'Z1d12xlarge' => 'Z1d 12XLarge – 48 vCPU, 384 GB RAM',
            ],
        ],
        'storage_type' => [
            'gp2' => 'Usage général (SSD) – Équilibre entre prix et performance ; éligible au niveau gratuit et type par défaut.',
            'gp3' => 'Usage général (SSD gp3) – SSD de nouvelle génération avec coût réduit et performance configurable ; éligible au niveau gratuit.',
            'io1' => 'IOPS provisionnées (SSD) – Stockage haute performance dédié pour les charges de travail critiques et intensives en E/S ; coût plus élevé.',
            'standard' => 'Magnétique (HDD) – Stockage magnétique plus ancien et plus lent. Bon marché mais non recommandé pour les nouveaux déploiements.',
        ],
        'engines' => [
            'description' => [
                'mysql' => "MySQL – Base de données relationnelle open source la plus populaire. Largement supportée, intégration facile. Éligible au niveau gratuit.",
                'postgres' => "PostgreSQL – Base de données avancée open source avec de solides fonctionnalités JSON et d'indexation. Très fiable et évolutive. Éligible au niveau gratuit.",
                'mariadb' => "MariaDB – Moteur compatible MySQL avec des fonctionnalités supplémentaires de performance et de gouvernance ouverte. Éligible au niveau gratuit.",
                'aurora_mysql' => "Amazon Aurora (compatible MySQL) – Moteur MySQL clusterisé haute performance avec mise à l'échelle automatique.",
                'aurora_postgresql' => "Amazon Aurora (compatible PostgreSQL) – Alternative PostgreSQL gérée d'Aurora.",
                'aurora' => "Amazon Aurora (générique hérité) – Utilisé uniquement pour les anciens appels API ; préférez aurora-mysql/aurora-postgresql.",
                'oracle_se2' => "Oracle SE2 (Standard Edition 2) – Base de données Oracle commerciale avec fonctionnalités de base activées.",
                'oracle_ee' => "Oracle EE (Enterprise Edition) – Base de données Oracle complète, capacités d'entreprise (coûts de licence applicables).",
                'sqlserver_ex' => "Microsoft SQL Server Express – SQL Server gratuit de niveau basique, ressources limitées.",
                'sqlserver_web' => "Microsoft SQL Server Web Edition – Optimisé pour les charges de travail web, coût réduit.",
                'sqlserver_se' => "Microsoft SQL Server Standard Edition – Option de licence équilibrée pour la production.",
                'sqlserver_ee' => "Microsoft SQL Server Enterprise Edition – Ensemble complet de fonctionnalités premium de SQL Server.",
            ],
        ],
        'status' => [
            'started'    => 'Démarré',
            'stopped'     => 'Arrêté',
            'backing_up'  => 'Sauvegarde',
            'pending'    => 'En attente',
            'failed'      => 'Échec',
        ],
        'associated_servers_msg' => "Cette base de données RDS est associée à des serveurs et ne peut pas être supprimée.",
        'delete_failed_msg' => "Échec de la suppression de la base de données RDS. Veuillez réessayer plus tard.",
        'db_name_regex' => 'Le nom de la base de données doit commencer par une lettre et ne contenir que des lettres et des chiffres (pas d\'espaces ni de caractères spéciaux).',
        'rds_database_id_unique' => 'Cette base de données est déjà attachée au serveur sélectionné.',
        'rds_server_id_unique' => 'Ce serveur est déjà attaché à la base de données sélectionnée.'
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