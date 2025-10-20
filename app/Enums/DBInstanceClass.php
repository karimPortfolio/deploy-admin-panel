<?php

namespace App\Enums;

enum DBInstanceClass: string
{

    //T‑Class – burstable, general‑purpose (low‑cost)
    case T4G_MICRO = 'db.t4g.micro';
    case T4G_SMALL = 'db.t4g.small';
    case T4G_MEDIUM = 'db.t4g.medium';
    case T3_MICRO = 'db.t3.micro';
    case T3_SMALL = 'db.t3.small';
    case T3_MEDIUM = 'db.t3.medium';
    case T3_LARGE = 'db.t3.large';
    case T3_XLARGE = 'db.t3.xlarge';
    case T3_2XLARGE = 'db.t3.2xlarge';
    case T2_MICRO = 'db.t2.micro';
    case T2_SMALL = 'db.t2.small';
    case T2_MEDIUM = 'db.t2.medium';

    //M‑Class – balanced compute & memory (general production)
    case M6G_LARGE = 'db.m6g.large';
    case M6G_XLARGE = 'db.m6g.xlarge';
    case M6G_2XLARGE = 'db.m6g.2xlarge';
    case M6G_4XLARGE = 'db.m6g.4xlarge';
    case M6G_8XLARGE = 'db.m6g.8xlarge';
    case M6G_12XLARGE = 'db.m6g.12xlarge';
    case M6G_16XLARGE = 'db.m6g.16xlarge';

    case M5_LARGE = 'db.m5.large';
    case M5_XLARGE = 'db.m5.xlarge';
    case M5_2XLARGE = 'db.m5.2xlarge';
    case M5_4XLARGE = 'db.m5.4xlarge';
    case M5_8XLARGE = 'db.m5.8xlarge';
    case M5_12XLARGE = 'db.m5.12xlarge';
    case M5_16XLARGE = 'db.m5.16xlarge';
    case M5_24XLARGE = 'db.m5.24xlarge';

    case M4_LARGE = 'db.m4.large';
    case M4_XLARGE = 'db.m4.xlarge';
    case M4_2XLARGE = 'db.m4.2xlarge';
    case M4_4XLARGE = 'db.m4.4xlarge';
    case M4_10XLARGE = 'db.m4.10xlarge';
    case M4_16XLARGE = 'db.m4.16xlarge';

    //R‑Class – memory‑optimized
    case R6G_LARGE = 'db.r6g.large';
    case R6G_XLARGE = 'db.r6g.xlarge';
    case R6G_2XLARGE = 'db.r6g.2xlarge';
    case R6G_4XLARGE = 'db.r6g.4xlarge';
    case R6G_8XLARGE = 'db.r6g.8xlarge';
    case R6G_12XLARGE = 'db.r6g.12xlarge';
    case R6G_16XLARGE = 'db.r6g.16xlarge';

    case R5_LARGE = 'db.r5.large';
    case R5_XLARGE = 'db.r5.xlarge';
    case R5_2XLARGE = 'db.r5.2xlarge';
    case R5_4XLARGE = 'db.r5.4xlarge';
    case R5_8XLARGE = 'db.r5.8xlarge';
    case R5_12XLARGE = 'db.r5.12xlarge';
    case R5_16XLARGE = 'db.r5.16xlarge';
    case R5_24XLARGE = 'db.r5.24xlarge';

    // X‑Class – high‑memory, enterprise
    case X2G_LARGE = 'db.x2g.large';
    case X2G_XLARGE = 'db.x2g.xlarge';
    case X2G_2XLARGE = 'db.x2g.2xlarge';
    case X2G_4XLARGE = 'db.x2g.4xlarge';
    case X1_16XLARGE = 'db.x1.16xlarge';

    //Z‑Class – high compute + memory balance
    case Z1D_LARGE = 'db.z1d.large';
    case Z1D_XLARGE = 'db.z1d.xlarge';
    case Z1D_2XLARGE = 'db.z1d.2xlarge';
    case Z1D_3XLARGE = 'db.z1d.3xlarge';
    case Z1D_6XLARGE = 'db.z1d.6xlarge';
    case Z1D_12XLARGE = 'db.z1d.12xlarge';
    
    public function description(): string
    {
        return match($this) {
            self::T4G_MICRO => __('messages.rds_databases.instance_class.description.T4gMicro'),
            self::T4G_SMALL => __('messages.rds_databases.instance_class.description.T4gSmall'),
            self::T4G_MEDIUM => __('messages.rds_databases.instance_class.description.T4gMedium'),
            self::T3_MICRO => __('messages.rds_databases.instance_class.description.T3Micro'),
            self::T3_SMALL => __('messages.rds_databases.instance_class.description.T3Small'),
            self::T3_MEDIUM => __('messages.rds_databases.instance_class.description.T3Medium'),
            self::T3_LARGE => __('messages.rds_databases.instance_class.description.T3Large'),
            self::T3_XLARGE => __('messages.rds_databases.instance_class.description.T3Xlarge'),
            self::T3_2XLARGE => __('messages.rds_databases.instance_class.description.T32xlarge'),
            self::T2_MICRO => __('messages.rds_databases.instance_class.description.T2Micro'),
            self::T2_SMALL => __('messages.rds_databases.instance_class.description.T2Small'),
            self::T2_MEDIUM => __('messages.rds_databases.instance_class.description.T2Medium'),

            self::M6G_LARGE => __('messages.rds_databases.instance_class.description.M6gLarge'),
            self::M6G_XLARGE => __('messages.rds_databases.instance_class.description.M6gXlarge'),
            self::M6G_2XLARGE => __('messages.rds_databases.instance_class.description.M6g2xlarge'),
            self::M6G_4XLARGE => __('messages.rds_databases.instance_class.description.M6g4xlarge'),
            self::M6G_8XLARGE => __('messages.rds_databases.instance_class.description.M6g8xlarge'),
            self::M6G_12XLARGE => __('messages.rds_databases.instance_class.description.M6g12xlarge'),
            self::M6G_16XLARGE => __('messages.rds_databases.instance_class.description.M6g16xlarge'),

            self::M5_LARGE => __('messages.rds_databases.instance_class.description.M5Large'),
            self::M5_XLARGE => __('messages.rds_databases.instance_class.description.M5Xlarge'),
            self::M5_2XLARGE => __('messages.rds_databases.instance_class.description.M52xlarge'),
            self::M5_4XLARGE => __('messages.rds_databases.instance_class.description.M54xlarge'),
            self::M5_8XLARGE => __('messages.rds_databases.instance_class.description.M58xlarge'),
            self::M5_12XLARGE => __('messages.rds_databases.instance_class.description.M512xlarge'),
            self::M5_16XLARGE => __('messages.rds_databases.instance_class.description.M516xlarge'),
            self::M5_24XLARGE => __('messages.rds_databases.instance_class.description.M524xlarge'),

            self::M4_LARGE => __('messages.rds_databases.instance_class.description.M4Large'),
            self::M4_XLARGE => __('messages.rds_databases.instance_class.description.M4Xlarge'),
            self::M4_2XLARGE => __('messages.rds_databases.instance_class.description.M42xlarge'),
            self::M4_4XLARGE => __('messages.rds_databases.instance_class.description.M44xlarge'),
            self::M4_10XLARGE => __('messages.rds_databases.instance_class.description.M410xlarge'),
            self::M4_16XLARGE => __('messages.rds_databases.instance_class.description.M416xlarge'),

            self::R6G_LARGE => __('messages.rds_databases.instance_class.description.R6gLarge'),
            self::R6G_XLARGE => __('messages.rds_databases.instance_class.description.R6gXlarge'),
            self::R6G_2XLARGE => __('messages.rds_databases.instance_class.description.R6g2xlarge'),
            self::R6G_4XLARGE => __('messages.rds_databases.instance_class.description.R6g4xlarge'),
            self::R6G_8XLARGE => __('messages.rds_databases.instance_class.description.R6g8xlarge'),
            self::R6G_12XLARGE => __('messages.rds_databases.instance_class.description.R6g12xlarge'),
            self::R6G_16XLARGE => __('messages.rds_databases.instance_class.description.R6g16xlarge'),

            self::R5_LARGE => __('messages.rds_databases.instance_class.description.R5Large'),
            self::R5_XLARGE => __('messages.rds_databases.instance_class.description.R5Xlarge'),
            self::R5_2XLARGE => __('messages.rds_databases.instance_class.description.R52xlarge'),
            self::R5_4XLARGE => __('messages.rds_databases.instance_class.description.R54xlarge'),
            self::R5_8XLARGE => __('messages.rds_databases.instance_class.description.R58xlarge'),
            self::R5_12XLARGE => __('messages.rds_databases.instance_class.description.R512xlarge'),
            self::R5_16XLARGE => __('messages.rds_databases.instance_class.description.R516xlarge'),
            self::R5_24XLARGE => __('messages.rds_databases.instance_class.description.R524xlarge'),

            self::X2G_LARGE => __('messages.rds_databases.instance_class.description.X2gLarge'),
            self::X2G_XLARGE => __('messages.rds_databases.instance_class.description.X2gXlarge'),
            self::X2G_2XLARGE => __('messages.rds_databases.instance_class.description.X2g2xlarge'),
            self::X2G_4XLARGE => __('messages.rds_databases.instance_class.description.X2g4xlarge'),
            self::X1_16XLARGE => __('messages.rds_databases.instance_class.description.X116xlarge'),

            self::Z1D_LARGE => __('messages.rds_databases.instance_class.description.Z1dLarge'),
            self::Z1D_XLARGE => __('messages.rds_databases.instance_class.description.Z1dXlarge'),
            self::Z1D_2XLARGE => __('messages.rds_databases.instance_class.description.Z1d2xlarge'),
            self::Z1D_3XLARGE => __('messages.rds_databases.instance_class.description.Z1d3xlarge'),
            self::Z1D_6XLARGE => __('messages.rds_databases.instance_class.description.Z1d6xlarge'),
            self::Z1D_12XLARGE => __('messages.rds_databases.instance_class.description.Z1d12xlarge'),
        };
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'description' => $this->description(),
        ];
    }
}
