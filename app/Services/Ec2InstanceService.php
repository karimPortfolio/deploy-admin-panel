<?php

namespace App\Services;

use Aws\Ec2\Ec2Client;
use Aws\CloudWatch\CloudWatchClient;
use Carbon\Carbon;

class Ec2InstanceService
{

    /**
     * Create an EC2 instance.
     *
     * @param array $params
     * @return array
     */
    public static function createInstance($params)
    {
        $ec2Client = app(Ec2Client::class);

        $imageId = AmazonMachineImageService::getAmiByOsFamily($params['os_family']);
        if (!$imageId) {
            throw new \RuntimeException('No AMI found for the specified OS family: ' . $params['os_family']);
        }

        $subnet = VpcService::getOrCreateSubnetByVpcId($params['vpc_id']);
        if (!$subnet || !isset($subnet['SubnetId'])) {
            throw new \RuntimeException('No subnet found for the specified VPC ID: ' . $params['vpc_id']);
        }

        try {
            $result = $ec2Client->runInstances([
                'ImageId' => $imageId,
                'InstanceType' => $params['instance_type']->value,
                'MinCount' => 1,
                'MaxCount' => 1,
                'SecurityGroupIds' => array($params['group_id']),
                'SubnetId' => $subnet['SubnetId'],
                'TagSpecifications' => [
                    [
                        'ResourceType' => 'instance',
                        'Tags' => [
                            [
                                'Key' => 'Name',
                                'Value' => $params['name'],
                            ],
                        ],
                    ],
                ],
            ]);

            $ec2Client->waitUntil('InstanceRunning', [
                'InstanceIds' => [$result['Instances'][0]['InstanceId']],
                'WaiterConfig' => [
                    'Delay' => 10,
                    'MaxAttempts' => 20,
                ],
            ]);

            return [
                'InstanceId' => $result['Instances'][0]['InstanceId'],
                'PublicIpAddress' => $result['Instances'][0]['PublicIpAddress'] ?? null,
                'PrivateIpAddress' => $result['Instances'][0]['PrivateIpAddress'] ?? null,
                'State' => $result['Instances'][0]['State']['Name'],
                'LaunchTime' => $result['Instances'][0]['LaunchTime'],
                'ImageId' => $result['Instances'][0]['ImageId'],
                'InstanceType' => $result['Instances'][0]['InstanceType'],
                'SecurityGroupId' => $result['Instances'][0]['SecurityGroups'][0]['GroupId'] ?? null,
                'VpcId' => $result['Instances'][0]['VpcId'] ?? null,
                'SubnetId' => $result['Instances'][0]['SubnetId'] ?? null,
            ];
        } catch (\Aws\Exception\AwsException $e) {
            throw new \RuntimeException('Failed to create EC2 instance: ' . $e->getMessage());
        }
    }

    /**
     * Start an EC2 instance.
     *
     * @param string $instanceId
     * @return \Aws\Result
     */
    public static function startInstance($instanceId)
    {
        $ec2Client = app(Ec2Client::class);

        try {
            return $ec2Client->startInstances([
                'InstanceIds' => [$instanceId],
            ]);
        } catch (\Aws\Exception\AwsException $e) {
            throw new \RuntimeException('Failed to start EC2 instance: ' . $e->getMessage());
        }
    }


    /**
     * Stop an EC2 instance.
     *
     * @param string $instanceId
     * @return \Aws\Result
     */
    public static function stopInstance($instanceId)
    {
        $ec2Client = app(Ec2Client::class);

        try {
            return $ec2Client->stopInstances([
                'InstanceIds' => [$instanceId],
            ]);
        } catch (\Aws\Exception\AwsException $e) {
            throw new \RuntimeException('Failed to stop EC2 instance: ' . $e->getMessage());
        }
    }


    /**
     * Terminate an EC2 instance.
     *
     * @param string $instanceId
     * @return \Aws\Result
     */
    public static function terminateInstance($instanceId)
    {
        $ec2Client = app(Ec2Client::class);

        try {
            return $ec2Client->terminateInstances([
                'InstanceIds' => [$instanceId],
            ]);
        } catch (\Aws\Exception\AwsException $e) {
            throw new \RuntimeException('Failed to terminate EC2 instance: ' . $e->getMessage());
        }
    }

    /**
     * Describe EC2 instances.
     *
     * @param string $instanceId
     * @return \Aws\Result
     */
    public static function describeInstanceByInstanceId(string $instanceId)
    {
        $ec2Client = app(Ec2Client::class);

        try {
            $ec2Instances = $ec2Client->describeInstances([
                'InstanceIds' => [$instanceId],
            ]);

            return $ec2Instances->get('Reservations')
                ? $ec2Instances->get('Reservations')[0]['Instances'][0]
                : [];
        } catch (\Aws\Exception\AwsException $e) {
            throw new \RuntimeException('Failed to describe EC2 instances: ' . $e->getMessage());
        }
    }


    /**
     * Get the utilization metrics for an EC2 instance.
     *
     * @param string $instanceId
     * @return array
     */
    public static function getInstanceUtilization(string $instanceId)
    {
        $cpuUtilization = static::getMetricStatisticsByMetricName('CPUUtilization', $instanceId);
        $diskReadOps = static::getMetricStatisticsByMetricName('DiskReadOps', $instanceId);
        $diskWriteOps = static::getMetricStatisticsByMetricName('DiskWriteOps', $instanceId);

        return [
            'cpu_utilization' => $cpuUtilization,
            'disk_read_ops' => $diskReadOps,
            'disk_write_ops' => $diskWriteOps,
        ];
    }

    /**
     * Get metric statistics for a specific metric name.
     *
     * @param string $metricName
     * @param string $instanceId
     * @return array
     */
    private static function getMetricStatisticsByMetricName(string $metricName, string $instanceId)
    {
        $cloudWatchClient = app(CloudWatchClient::class);

        try {
            $metrics = $cloudWatchClient->getMetricStatistics([
                'Namespace' => 'AWS/EC2',
                'MetricName' => $metricName,
                'Dimensions' => [
                    [
                        'Name' => 'InstanceId',
                        'Value' => $instanceId,
                    ],
                ],
                'StartTime' => Carbon::now()->subHours(2)->toIso8601String(),
                'EndTime' => Carbon::now()->toIso8601String(),
                'Period' => 600,
                'Statistics' => ['Average'],
                'Unit' => 'Percent',
            ]);

            if (!$metrics->hasKey('Datapoints')) {
                return [];
            }
            
            return $metrics->get('Datapoints') ?? [];
        } catch (\Aws\Exception\AwsException $e) {
            throw new \RuntimeException('Failed to get metric statistics: ' . $e->getMessage());
        }
    }
}