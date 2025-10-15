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
];