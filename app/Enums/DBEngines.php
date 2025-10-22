<?php

namespace App\Enums;

enum DBEngines: string
{

    case MYSQL = 'mysql';
    case POSTGRES = 'postgres';
    case MARIADB = 'mariadb';

    case AURORA_MYSQL = 'aurora-mysql';
    case AURORA_POSTGRESQL = 'aurora-postgresql';
    case AURORA = 'aurora';

    case ORACLE_SE2 = 'oracle-se2';
    case ORACLE_EE = 'oracle-ee';
    case SQLSERVER_EX = 'sqlserver-ex';
    case SQLSERVER_WEB = 'sqlserver-web';
    case SQLSERVER_SE = 'sqlserver-se';
    case SQLSERVER_EE = 'sqlserver-ee';

    public function label(): string
    {
        return match($this) {
            self::MYSQL => __('messages.rds_databases.engines.description.mysql'),
            self::POSTGRES => __('messages.rds_databases.engines.description.postgres'),
            self::MARIADB => __('messages.rds_databases.engines.description.mariadb'),
            self::AURORA_MYSQL => __('messages.rds_databases.engines.description.aurora_mysql'),
            self::AURORA_POSTGRESQL => __('messages.rds_databases.engines.description.aurora_postgresql'),
            self::AURORA => __('messages.rds_databases.engines.description.aurora'),
            self::ORACLE_SE2 => __('messages.rds_databases.engines.description.oracle_se2'),
            self::ORACLE_EE => __('messages.rds_databases.engines.description.oracle_ee'),
            self::SQLSERVER_EX => __('messages.rds_databases.engines.description.sqlserver_ex'),
            self::SQLSERVER_WEB => __('messages.rds_databases.engines.description.sqlserver_web'),
            self::SQLSERVER_SE => __('messages.rds_databases.engines.description.sqlserver_se'),
            self::SQLSERVER_EE => __('messages.rds_databases.engines.description.sqlserver_ee'),
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::MYSQL => 'fas fa-database',
            self::POSTGRES => 'fas fa-elephant',
            self::MARIADB => 'fas fa-database',
            self::AURORA_MYSQL => 'fab fa-aws',
            self::AURORA_POSTGRESQL => 'fab fa-aws',
            self::AURORA => 'fab fa-aws',
            self::ORACLE_SE2 => 'fas fa-building',
            self::ORACLE_EE => 'fas fa-building',
            self::SQLSERVER_EX => 'fab fa-microsoft',
            self::SQLSERVER_WEB => 'fab fa-microsoft',
            self::SQLSERVER_SE => 'fab fa-microsoft',
            self::SQLSERVER_EE => 'fab fa-microsoft',
        };
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'label' => $this->label(),
            'icon' => $this->icon(),
        ];
    }
    
}
