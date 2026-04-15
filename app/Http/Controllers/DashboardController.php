<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->input('start_date', now()->startOfYear()->format('Y-m-d'));
        $end = $request->input('end_date', now()->endOfYear()->format('Y-m-d'));

        $ventas = Venta::whereBetween('created_at', [$start, $end])->get();

        $estados = ['pagado', 'pendiente', 'anulada'];
        $data = [];
        $labels = [];

        foreach (range(1, 12) as $month) {
            $labels[] = Carbon::create()->month($month)->format('F');
            foreach ($estados as $estado) {
                $data[$estado][] = $ventas->where('estado', $estado)
                    ->filter(fn($venta) => $venta->created_at->month === $month)
                    ->sum('total');
            }
        }

        return view('dashboard', [
            'labels' => $labels,
            'data' => $data,
            'start' => $start,
            'end' => $end,
            'totales' => $ventas->groupBy('estado')->map->sum('total'),
        ]);
    }


    public function exportExcel(Request $request)
    {
        return Excel::download(new VentasExport($request), 'reporte_ventas.xlsx');
    }


    public function exportPDF(Request $request)
    {
        $ventas = $this->filtrarVentas($request);
        $pdf = Pdf::loadView('exports.ventas_pdf', compact('ventas'));
        return $pdf->download('reporte_ventas.pdf');
    }
    
}

