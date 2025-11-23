<?php

namespace App\Services\AWS;

use Carbon\Carbon;

class Ec2InstanceService
{
    public function __construct(
        private readonly \Aws\Ec2\Ec2Client $ec2Client,
        private readonly AmazonMachineImageService $amazonMachineImageService,
        private readonly VpcService $vpcService,
        private readonly \Aws\CloudWatch\CloudWatchClient $cloudWatchClient
    ) {}

    /**
     * Create an EC2 instance.
     *
     * @param array $params
     * @return array
     */
    public function createInstance($params)
    {
        $imageId = $this->amazonMachineImageService->getAmiByOsFamily($params['os_family']);
        if (!$imageId) {
            throw new \RuntimeException('No AMI found for the specified OS family: ' . $params['os_family']);
        }

        $subnet = $this->vpcService->getOrCreateSubnetByVpcId($params['vpc_id']);
        if (!$subnet || !isset($subnet['SubnetId'])) {
            throw new \RuntimeException('No subnet found for the specified VPC ID: ' . $params['vpc_id']);
        }

        try {
            $result = $this->ec2Client->runInstances([
                'ImageId' => $imageId,
                'InstanceType' => $params['instance_type']->value,
                'MinCount' => 1,
                'MaxCount' => 1,
                'SecurityGroupIds' => array($params['group_id']),
                'SubnetId' => $subnet['SubnetId'],
                'KeyName' => $params['key_name'],
                'AssociatePublicIpAddress' => true,
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

            $this->ec2Client->waitUntil('InstanceRunning', [
                'InstanceIds' => [$result['Instances'][0]['InstanceId']],
                'WaiterConfig' => [
                    'Delay' => 10,
                    'MaxAttempts' => 20,
                ],
            ]);

            if (!isset($result['Instances']) || !is_array($result['Instances']))
            {
                return [];
            }

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
     * Create an EC2 instance.
     *
     * @param string $name
     * @return \Aws\Result|array
     * @throws \Aws\Exception\AwsException
     */
    public function createKeyPair(string $name): \Aws\Result|array
    {
        try
        {
            $keyPair = $this->ec2Client->createKeyPair([
                'KeyName' => $name . '-key-' . \Str::random(6)
            ]);

            return $keyPair ?? [];
        }
        catch(\Aws\Exception\AwsException $e)
        {
            throw new \RuntimeException('Failed to create key pair: ' . $e->getMessage());
        }
    }

    /**
     * Start an EC2 instance.
     *
     * @param string $instanceId
     * @return \Aws\Result
     */
    public function startInstance($instanceId)
    {
        try {
            return $this->ec2Client->startInstances([
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
    public function stopInstance($instanceId)
    {
        try {
            return $this->ec2Client->stopInstances([
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
    public function terminateInstance($instanceId)
    {
        try {
            return $this->ec2Client->terminateInstances([
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
    public function describeInstanceByInstanceId(string $instanceId)
    {
        try {
            $ec2Instances = $this->ec2Client->describeInstances([
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
    public function getInstanceUtilization(string $instanceId)
    {
        $cpuUtilization = $this->getMetricStatisticsByMetricName('CPUUtilization', $instanceId);
        $diskReadOps = $this->getMetricStatisticsByMetricName('DiskReadOps', $instanceId);
        $diskWriteOps = $this->getMetricStatisticsByMetricName('DiskWriteOps', $instanceId);

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
    private function getMetricStatisticsByMetricName(string $metricName, string $instanceId)
    {
        try {
            $metrics = $this->cloudWatchClient->getMetricStatistics([
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