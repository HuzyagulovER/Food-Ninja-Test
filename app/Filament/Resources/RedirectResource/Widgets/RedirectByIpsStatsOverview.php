<?php

namespace App\Filament\Resources\RedirectResource\Widgets;

use App\Models\Link;
use App\Models\Redirect;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class RedirectByIpsStatsOverview
	extends BaseWidget
{
	protected ?string $heading = 'Статистика';

	public string $ipAddress;

	public function mount(string $ip_address): void
	{
		$this->ipAddress = $ip_address;
	}

	protected function getStats(): array
	{
		$linksTable     = (new Link())->getTable();
		$redirectsTable = (new Redirect())->getTable();

		$preQuery = Redirect::join($linksTable, "$redirectsTable.link_id", '=', "$linksTable.id")
		                    ->where('user_id', auth()->id())
		                    ->whereIpAddress($this->ipAddress);

		return [
			Stat::make(
				'Количество',
				$preQuery->count()
			)
			    ->chart(
				    collect(
					    DB::select(
						    "
    SELECT
        SUM(hour_total) OVER (ORDER BY hour_bucket) AS cumulative_total
    FROM (
        SELECT
            DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00') AS hour_bucket,
            COUNT(*) AS hour_total
        FROM $redirectsTable
        WHERE ip_address = ?
        GROUP BY hour_bucket
    ) t
    ORDER BY hour_bucket
",
						    [$this->ipAddress]
					    )
				    )->pluck('cumulative_total')
				     ->toArray()
			    )
			    ->descriptionIcon('heroicon-o-arrow-trending-up')
			    ->color('success'),
		];
	}
}
