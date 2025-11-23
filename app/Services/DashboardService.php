<?php

namespace App\Services;

use App\Models\RdsDatabase;
use App\Models\SecurityGroup;
use App\Models\Server;
use App\Models\SshKey;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService
{
	public function totalServersCount(): array
	{
		return [
			'total' => Server::query()->count(),
		];
	}

	public function totalUsersCount(): array
	{
		return [
			'total' => User::query()->count(),
		];
	}

	public function totalSecurityGroupsCount(): array
	{
		return [
			'total' => SecurityGroup::query()->count(),
		];
	}

	public function totalSshKeysCount(): array
	{
		return [
			'total' => SshKey::query()->count(),
		];
	}

	public function totalRdsDatabasesCount(): array
	{
		return [
			'total' => RdsDatabase::query()->count(),
		];
	}

	public function monthlyServersTotal(Request $request): array
	{
		$servers = $this->monthlyAggregateQuery(
			Server::class,
			$this->resolveYear($request)
		);

		return $this->formatMonthlyTotals($servers);
	}

	public function monthlySecurityGroupsTotal(Request $request): array
	{
		$securityGroups = $this->monthlyAggregateQuery(
			SecurityGroup::class,
			$this->resolveYear($request)
		);

		return $this->formatMonthlyTotals($securityGroups);
	}

	public function monthlyRdsDatabasesTotal(Request $request): array
	{
		$rdsDatabases = $this->monthlyAggregateQuery(
			RdsDatabase::class,
			$this->resolveYear($request)
		);

		return $this->formatMonthlyTotals($rdsDatabases);
	}

	public function totalServersBySecurityGroups(): array
	{
		return Server::query()
			->select('security_group_id', DB::raw('COUNT(*) as total'))
			->with('securityGroup')
			->groupBy('security_group_id')
			->get()
			->filter(fn ($server) => $server->securityGroup)
			->map(fn ($server) => [
				'securityGroup' => $server->securityGroup->group_id,
				'total' => $server->total,
			])
			->values()
			->toArray();
	}

	public function totalServersByStatus(): array
	{
		return Server::query()
			->select('status', DB::raw('COUNT(*) as total'))
			->groupBy('status')
			->get()
			->map(fn ($server) => [
				'status' => $server->status->label(),
				'color' => $server->status->hexColor(),
				'total' => $server->total,
			])
			->toArray();
	}

	private function monthlyAggregateQuery(string $modelClass, int $year): Collection
	{
		return $modelClass::query()
			->select(DB::raw($this->monthQuery()), DB::raw('COUNT(*) as count'))
			->whereYear('created_at', $year)
			->groupBy('month')
			->get()
			->keyBy('month');
	}

	private function formatMonthlyTotals(Collection $collection): array
	{
		$data = [];

		for ($month = 1; $month <= 12; $month++) {
			$data[] = [
				'month' => Carbon::createFromDate(null, $month, 1)->format('M'),
				'total' => $collection->get($month)?->count ?? 0,
			];
		}

		return $data;
	}

	private function monthQuery(): string
	{
		return match (config('database.default')) {
			'mysql', 'pgsql' => 'MONTH(created_at) as month',
			'sqlite' => "CAST(strftime('%m', created_at) AS INTEGER) as month",
			'sqlsrv' => 'MONTH(created_at) as month',
			default => throw new \Exception('Unsupported database driver: ' . config('database.default')),
		};
	}

	private function resolveYear(Request $request): int
	{
		return (int) ($request->input('filter.year') ?? date('Y'));
	}
}


