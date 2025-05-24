<?php

namespace App\Enums;

enum OsFamily: string
{
    case AmazonLinux = 'amazon-linux';
    case Ubuntu = 'ubuntu';
    case Debian = 'debian';
    case Windows = 'windows';
    case RedHat = 'redhat';

    public function label(): string
    {
        return match($this) {
            self::AmazonLinux => 'Amazon Linux',
            self::Ubuntu      => 'Ubuntu',
            self::Debian      => 'Debian',
            self::Windows     => 'Windows',
            self::RedHat      => 'Red Hat',
        };
    }

    public function ssmParameter(): string
    {
        return match($this) {
            self::AmazonLinux => '/aws/service/ami-amazon-linux-latest/amzn2-ami-hvm-x86_64-gp2',
            self::Ubuntu      => '/aws/service/canonical/ubuntu/server/22.04/stable/current/amd64/hvm/ebs-gp2/ami-id',
            self::Debian      => '/aws/service/debian/release-12/latest/amd64',
            self::Windows     => '/aws/service/ami-windows-latest/Windows_Server-2022-English-Full-Base',
            self::RedHat      => '/aws/service/redhat/rhel/9/x86_64/latest',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::AmazonLinux => 'fab fa-amazon',
            self::Ubuntu      => 'fab fa-ubuntu',
            self::Debian      => 'fab fa-debian',
            self::Windows     => 'fab fa-windows',
            self::RedHat      => 'fab fa-redhat',
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
