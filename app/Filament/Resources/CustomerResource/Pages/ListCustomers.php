<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Models\Customer;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    // public $defaultAction = 'newYears';

    public function newYears()
    {
        $today = Carbon::today();
        $next30Days = Carbon::today()->addDays(30);

        $newYears = Customer::whereBetween(
            DB::raw("DATE_FORMAT(nacimiento, CONCAT(YEAR(CURDATE()), '-%m-%d'))"),
            [$today->format('Y-m-d'), $next30Days->format('Y-m-d')]
        )->count();
        return Action::make('newYears')
            ->visible($newYears > 0)
            ->modalSubmitActionLabel('Verlos')
            ->action(function () {
                return redirect()->to('https://peluqueria.test/admin/customers?activeTab=cumple');
            })
            ->modalCancelAction(null)
            ->color('success')
            ->modalHeading('Nuevos cumpleaños')
            ->modalDescription('Tienes ' . $newYears . ' clientes que cumplen años en los próximos 30 días.')
            ->modalWidth('lg');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $today = Carbon::today();
        $next30Days = Carbon::today()->addDays(30);

        return [
            'todos' => Tab::make('Todos los clientes')
                ->modifyQueryUsing(fn(Builder $query) => $query->orderBy('id')),
            'cumple' => Tab::make('Cumpleaños en los proximos 30 días')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereBetween(
                    DB::raw("DATE_FORMAT(nacimiento, CONCAT(YEAR(CURDATE()), '-%m-%d'))"),
                    [$today->format('Y-m-d'), $next30Days->format('Y-m-d')]
                )),
            'cumple2' => Tab::make('Cumpleaños 2')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->whereRaw('TIMESTAMPDIFF(YEAR, nacimiento, CURDATE()) <> TIMESTAMPDIFF(YEAR, nacimiento, DATE_SUB(CURDATE(),INTERVAL 30 DAY))')
                )
        ];
    }
}
