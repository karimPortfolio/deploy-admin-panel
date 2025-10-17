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