<?php

return [
    'servers' => [
        'servers_creation_msg' => "Server-Erstellung läuft. Sie können den Status später überprüfen.",
        'failed_to_start_msg' => "Fehler beim Starten der Instanz.",
        'failed_to_stop_msg'  => "Fehler beim Stoppen der Instanz.",
        'failed_to_terminate_msg' => "Fehler beim Beenden der Instanz.",
        'instance_type' => [
            'description' => [
                'T2Micro'    => 'Für kostenlosen Tarif berechtigt, allgemein verwendbar',
                'T3Micro'    => 'Allgemein verwendbar, burst-fähige Leistung',
                'T3Small'    => 'Kleine allgemeine Verwendung',
                'T3Medium'   => 'Mittlere allgemeine Verwendung',
                'C5Large'    => 'Compute-optimiert, gut für leistungsstarke Arbeitslasten',
                'C5Xlarge'   => 'Compute-optimiert, zusätzliche Kapazität',
                'R5Large'    => 'Speicher-optimiert, gut für datenintensive Apps',
                'R5Xlarge'   => 'Speicher-optimiert, mehr RAM',
                'G4dnXlarge' => 'GPU-Instanz, gut für ML oder Grafik-Arbeitslasten',
            ],
        ],
        'status' => [
            'running'    => 'Läuft',
            'stopped'    => 'Gestoppt',
            'terminated' => 'Beendet',
            'pending'    => 'Ausstehend',
        ],
    ],
    'users' => [
        'roles' => [
            'admin' => 'Administrator',
            'user'  => 'Benutzer',
        ],
    ],
    'security_groups' => [
        'associated_servers_msg' => "Diese Sicherheitsgruppe ist mit Servern verknüpft und kann nicht gelöscht werden.",
    ],
    'ssh_keys' => [
        'associated_servers_msg' => "Dieser SSH-Schlüssel ist mit Servern verknüpft und kann nicht gelöscht werden.",
    ],
    'rds_databases' => [
        'instance_class' => [
            'description' => [
                'T4gMicro' => 'T4g Micro – 2 vCPU, 1 GB RAM (Graviton2, kostenloses Kontingent berechtigt)',
                'T4gSmall' => 'T4g Small – 2 vCPU, 2 GB RAM (Graviton2, kostengünstig)',
                'T4gMedium' => 'T4g Medium – 2 vCPU, 4 GB RAM (Graviton2)',
                'T3Micro' => 'T3 Micro – 2 vCPU, 1 GB RAM (kostenloses Kontingent berechtigt)',
                'T3Small' => 'T3 Small – 2 vCPU, 2 GB RAM',
                'T3Medium' => 'T3 Medium – 2 vCPU, 4 GB RAM',
                'T3Large' => 'T3 Large – 2 vCPU, 8 GB RAM',
                'T3Xlarge' => 'T3 XLarge – 4 vCPU, 16 GB RAM',
                'T32xlarge' => 'T3 2XLarge – 8 vCPU, 32 GB RAM',
                'T2Micro' => 'T2 Micro – 1 vCPU, 1 GB RAM (veraltetes kostenloses Kontingent)',
                'T2Small' => 'T2 Small – 1 vCPU, 2 GB RAM',
                'T2Medium' => 'T2 Medium – 2 vCPU, 4 GB RAM',
                'M6gLarge' => 'M6g Large – 2 vCPU, 8 GB RAM (Graviton2)',
                'M6gXlarge' => 'M6g XLarge – 4 vCPU, 16 GB RAM',
                'M6g2xlarge' => 'M6g 2XLarge – 8 vCPU, 32 GB RAM',
                'M6g4xlarge' => 'M6g 4XLarge – 16 vCPU, 64 GB RAM',
                'M6g8xlarge' => 'M6g 8XLarge – 32 vCPU, 128 GB RAM',
                'M6g12xlarge' => 'M6g 12XLarge – 48 vCPU, 192 GB RAM',
                'M6g16xlarge' => 'M6g 16XLarge – 64 vCPU, 256 GB RAM',
                'M5Large' => 'M5 Large – 2 vCPU, 8 GB RAM (Intel Xeon, ausgewogen)',
                'M5Xlarge' => 'M5 XLarge – 4 vCPU, 16 GB RAM',
                'M52xlarge' => 'M5 2XLarge – 8 vCPU, 32 GB RAM',
                'M54xlarge' => 'M5 4XLarge – 16 vCPU, 64 GB RAM',
                'M58xlarge' => 'M5 8XLarge – 32 vCPU, 128 GB RAM',
                'M512xlarge' => 'M5 12XLarge – 48 vCPU, 192 GB RAM',
                'M516xlarge' => 'M5 16XLarge – 64 vCPU, 256 GB RAM',
                'M524xlarge' => 'M5 24XLarge – 96 vCPU, 384 GB RAM',
                'M4Large' => 'M4 Large – 2 vCPU, 8 GB RAM (ältere Generation)',
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
                'X2gLarge' => 'X2g Large – 2 vCPU, 32 GB RAM (Graviton2 hoher Speicher)',
                'X2gXlarge' => 'X2g XLarge – 4 vCPU, 64 GB RAM',
                'X2g2xlarge' => 'X2g 2XLarge – 8 vCPU, 128 GB RAM',
                'X2g4xlarge' => 'X2g 4XLarge – 16 vCPU, 256 GB RAM',
                'X116xlarge' => 'X1 16XLarge – 64 vCPU, 976 GB RAM (veralteter hoher Speicher)',
                'Z1dLarge' => 'Z1d Large – 2 vCPU, 16 GB RAM (hohe Single-Thread-CPU)',
                'Z1dXlarge' => 'Z1d XLarge – 4 vCPU, 32 GB RAM',
                'Z1d2xlarge' => 'Z1d 2XLarge – 8 vCPU, 64 GB RAM',
                'Z1d3xlarge' => 'Z1d 3XLarge – 12 vCPU, 96 GB RAM',
                'Z1d6xlarge' => 'Z1d 6XLarge – 24 vCPU, 192 GB RAM',
                'Z1d12xlarge' => 'Z1d 12XLarge – 48 vCPU, 384 GB RAM',
            ],
        ],
        'storage_type' => [
            'gp2' => 'Allgemeiner Zweck (SSD) – Ausgewogenes Preis-Leistungs-Verhältnis; kostenloses Kontingent berechtigt und Standardtyp.',
            'gp3' => 'Allgemeiner Zweck (SSD gp3) – SSD der nächsten Generation mit geringeren Kosten und konfigurierbarer Leistung; kostenloses Kontingent berechtigt.',
            'io1' => 'Bereitgestellte IOPS (SSD) – Dedizierter Hochdurchsatz-Speicher für unternehmenskritische, I/O-intensive Arbeitslasten; höhere Kosten.',
            'standard' => 'Magnetisch (HDD) – Älterer, langsamerer magnetischer Speicher. Günstig, aber nicht für neue Bereitstellungen empfohlen.',
        ],
        'engines' => [
            'mysql' => "MySQL – Beliebteste Open-Source-relationale Datenbank. Weitreichend unterstützt, einfache Integration. Für kostenloses Kontingent berechtigt.",
            'postgres' => "PostgreSQL – Erweiterte Open-Source-Datenbank mit starken JSON- und Indizierungsfunktionen. Hochzuverlässig und skalierbar. Für kostenloses Kontingent berechtigt.",
            'mariadb' => "MariaDB – MySQL-kompatible Engine mit zusätzlichen Leistungsmerkmalen und offener Governance. Für kostenloses Kontingent berechtigt.",
            'aurora_mysql' => "Amazon Aurora (MySQL-kompatibel) – Hochleistungs-Cluster-MySQL-Engine mit automatischer Skalierung.",
            'aurora_postgresql' => "Amazon Aurora (PostgreSQL-kompatibel) – Auroras verwaltete PostgreSQL-Alternative.",
            'aurora' => "Amazon Aurora (veraltetes generisches) – Nur für ältere API-Aufrufe verwendet; bevorzuge aurora-mysql/aurora-postgresql.",
            'oracle_se2' => "Oracle SE2 (Standard Edition 2) – Kommerzielle Oracle-Datenbank mit aktivierten Grundfunktionen.",
            'oracle_ee' => "Oracle EE (Enterprise Edition) – Vollständige Oracle-Datenbank, Unternehmensfähigkeiten (Lizenzkosten fallen an).",
            'sqlserver_ex' => "Microsoft SQL Server Express – Kostenloser Einstiegs-SQL Server, begrenzte Ressourcen.",
            'sqlserver_web' => "Microsoft SQL Server Web Edition – Optimiert für Web-Arbeitslasten, niedrigere Kosten.",
            'sqlserver_se' => "Microsoft SQL Server Standard Edition – Ausgewogene Lizenzoption für Produktion.",
            'sqlserver_ee' => "Microsoft SQL Server Enterprise Edition – Vollständiger Premium-SQL Server-Funktionsumfang.",
        ],
        'status' => [
            'started'    => 'Gestartet',
            'stopped'     => 'Gestoppt',
            'backing_up'  => 'Wird gesichert',
            'pending'    => 'Ausstehend',
            'failed'      => 'Fehlgeschlagen',
        ],
        'associated_servers_msg' => "Diese RDS Datenbank ist mit Servern verknüpft und kann nicht gelöscht werden.",
        'delete_failed_msg' => "Löschen der RDS-Datenbank fehlgeschlagen. Bitte versuchen Sie es später erneut.",
        'db_name_regex' => 'Der Datenbankname muss mit einem Buchstaben beginnen und darf nur Buchstaben und Zahlen enthalten (keine Leerzeichen oder Sonderzeichen).',
        'rds_database_id_unique' => 'Diese Datenbank ist bereits mit dem ausgewählten Server verknüpft.',
        'rds_server_id_unique' => 'Dieser Server ist bereits mit der ausgewählten Datenbank verknüpft.'
    ],
    'notifications' => [
        'new_user' => [
            'mail' => [
                'subject' => 'Willkommen bei :appName',
                'greeting' => 'Hallo :name,',
                'line1' => 'Wir freuen uns, Sie an Bord zu haben! Ihr Konto wurde erfolgreich erstellt. Sie können loslegen, indem Sie sich mit Ihrer E-Mail und Ihrem temporären Passwort in unsere Anwendung einloggen: :password',
                'line2' => 'Bitte stellen Sie sicher, dass Sie Ihr Passwort nach der ersten Anmeldung ändern.',
                'action' => 'Jetzt anmelden',
                'line3' => 'Vielen Dank, dass Sie unsere Anwendung nutzen!',
            ]
        ],
        'account_activation' => [
            'mail' => [
                'subject' => 'Konto aktiviert',
                'greeting' => 'Hallo :name,',
                'line1' => 'Wir freuen uns, Ihnen mitteilen zu können, dass Ihr Konto von unserem Administrationsteam aktiviert wurde. Willkommen zurück! Wir freuen uns, Sie wieder bei uns zu haben.',
                'action' => 'Besuchen Sie Ihr Dashboard',
                'line2' => 'Vielen Dank, dass Sie unsere Anwendung nutzen!',
            ],
            'database' => [
                'title' => 'Konto aktiviert',
                'body' => 'Ihr Konto wurde erneut von unserem Administrationsteam aktiviert.',
                'actionLabel' => 'Dashboard überprüfen',
            ]
        ],
        'account_deactivation' => [
            'mail' => [
                'subject' => 'Konto deaktiviert',
                'greeting' => 'Hallo :name,',
                'line1' => 'Wir bedauern, Ihnen mitteilen zu müssen, dass Ihr Konto von unserem Administrationsteam deaktiviert wurde. Wenn Sie glauben, dass dies ein Fehler ist oder Fragen haben, wenden Sie sich bitte an unser Support-Team.',
                'line2' => 'Vielen Dank, dass Sie unsere Anwendung nutzen!',
            ],
            'database' => [
                'title' => 'Konto deaktiviert',
                'body' => 'Ihr Konto wurde von unserem Administrationsteam deaktiviert.',
                'actionLabel' => 'Support kontaktieren',
            ]
        ],
        'deleted_resource' => [
            'mail' => [
                'subject' => 'Ressource gelöscht',
                'greeting' => 'Hallo :name,',
                'line1' => 'Wir wollten Sie darüber informieren, dass Ihre :resourceType (ID: :resourceId) von unserem Administrationsteam gelöscht wurde.',
                'action' => 'Dashboard überprüfen',
                'line2' => 'Vielen Dank, dass Sie unsere Anwendung nutzen!',
            ],
            'database' => [
                'title' => 'Ressource gelöscht',
                'body' => 'Ihre :resourceType (ID: :resourceId) wurde von unserem Administrationsteam gelöscht.',
                'actionLabel' => 'Dashboard überprüfen',
            ]
        ],
        'unused_resources_alert' => [
            'mail' => [
                'subject' => 'Wöchentliche Prüfung – Ungenutzte Ressourcen überprüfen',
                'greeting' => 'Hallo :name,',
                'line1' => 'Ihr wöchentlicher Analysebericht über ungenutzte Ressourcen ist bereit.',
                'line2' => 'Diese Woche wurden insgesamt :totalUnusedResources ungenutzte Ressourcen identifiziert:',
                'securityGroupsLine' => '- :unusedSecurityGroupsCount ungenutzte Sicherheitsgruppen gefunden.',
                'sshKeysLine' => '- :unusedSshKeysCount ungenutzte SSH-Schlüssel gefunden.',
                'action' => 'Dashboard überprüfen',
                'line3' => 'Vielen Dank, dass Sie :appName nutzen.',
            ],
            'database' => [
                'title' => 'Ungenutzte Ressourcen erkannt',
                'body' => 'Diese Woche wurden insgesamt :totalUnusedResources ungenutzte Ressourcen identifiziert.',
                'actionLabel' => 'Dashboard überprüfen',
            ]
        ],
        'password_reset' => [
            'mail' => [
                'subject' => 'Passwort zurücksetzen Benachrichtigung',
                'line1' => 'Klicken Sie auf die Schaltfläche unten, um Ihr Passwort zurückzusetzen:',
                'action' => 'Passwort zurücksetzen',
                'line2' => 'Wenn Sie keine Passwort-Zurücksetzung angefordert haben, ist keine weitere Aktion erforderlich.',
            ]
        ],
    ],
    'middlewares' => [
        'account_inactive' => 'Konto ist inaktiv. Bitte kontaktieren Sie den Support.',
    ]
];