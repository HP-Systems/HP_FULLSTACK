<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PDOException;

class ReportesController extends Controller
{
    public function ventasPorMes(Request $request)
    {
        try {
            // Configura Carbon para usar el idioma español
            $fechaInicio = Carbon::now()->startOfMonth()->toDateString();
            $fechaFin = Carbon::now()->endOfMonth()->toDateString();

            // Llama al procedimiento almacenado
            $ventas = DB::select('CALL GenerarReporteVentasPorMes(?, ?)', [$fechaInicio, $fechaFin]);
            $data = [
                'ventas' => $ventas,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin
            ];

            // Retorna la vista con los datos filtrados
            return view('reportes.reporte1', $data);
        } catch (\Exception $e) {
            Log::error('Error al generar el reporte de ventas por mes: ' . $e->getMessage());
        } catch (PDOException $e) {
            Log::error('Error al generar el reporte de ventas por mes: ' . $e->getMessage());
        }
    }
    public function ventasPorMesFiltrar(Request $request)
    {
        try {
            $message = [
                'fecha_inicio.required' => 'La fecha de inicio es requerida',
                'fecha_fin.required' => 'La fecha de fin es requerida',
                'fecha_fin.after_or_equal' => 'La fecha de fin debe ser mayor o igual a la fecha de inicio'
            ];
            $validator = Validator::make($request->all(), [
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio'
            ], $message);
            if ($validator->fails()) {
                $fechaInicio = Carbon::now()->startOfMonth()->toDateString();
                $fechaFin = Carbon::now()->endOfMonth()->toDateString();
                return redirect()->route('reporte1', ['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin])->withErrors($validator);
            }
            $fechaInicio = $request->input('fecha_inicio');
            $fechaFin = $request->input('fecha_fin');

            // Llama al procedimiento almacenado
            $ventas = DB::select('CALL GenerarReporteVentasPorMes(?, ?)', [$fechaInicio, $fechaFin]);

            // Retorna la vista con los datos filtrados
            return view('reportes.reporte1', ['ventas' => $ventas, 'fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin]);
        } catch (\Exception $e) {
            Log::error('Error al generar el reporte de ventas por mes: ' . $e->getMessage());
        } catch (PDOException $e) {
            Log::error('Error al generar el reporte de ventas por mes: ' . $e->getMessage());
        }
    }

    public function pdfVentasPorMes(Request $request)
    {
        try {
            $message = [
                'fecha_inicio.required' => 'La fecha de inicio es requerida',
                'fecha_fin.required' => 'La fecha de fin es requerida',
                'fecha_fin.after_or_equal' => 'La fecha de fin debe ser mayor o igual a la fecha de inicio'
            ];
            $validator = Validator::make($request->all(), [
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio'
            ], $message);
            if ($validator->fails()) {
                $fechaInicio = Carbon::now()->startOfMonth()->toDateString();
                $fechaFin = Carbon::now()->endOfMonth()->toDateString();
                return redirect()->route('reporte1', ['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin])->withErrors($validator);
            }
            $fechaInicio = $request->input('fecha_inicio');
            $fechaFin = $request->input('fecha_fin');
            Carbon::setLocale('es');
            // a carbon
            $fechaInicio = Carbon::parse($fechaInicio);
            $fechaFin = Carbon::parse($fechaFin);

            $fechaInicioFormateada = ucfirst($fechaInicio->translatedFormat('F d Y'));
            $fechaFinFormateada = ucfirst($fechaFin->translatedFormat('F d Y'));

            // Llama al procedimiento almacenado
            $ventas = DB::select('CALL GenerarReporteVentasPorMes(?, ?)', [$fechaInicio->toDateString(), $fechaFin]);

            $total = collect($ventas)->sum('Total_General');
            $alojamiento = collect($ventas)->sum('Total_Alojamiento');
            $servicios = collect($ventas)->sum('Total_Servicios');
            $logoPath = public_path('images/logo.jpg');

            $data = [
                'ventas' => $ventas,
                'fechaInicio' => $fechaInicioFormateada,
                'fechaFin' => $fechaFinFormateada,
                'total' => $total,
                'alojamiento' => $alojamiento,
                'servicios' => $servicios,
                'logo' => $logoPath,
            ];

            // Genera el PDF


            $pdf = PDF::loadView('reportes.pdf.reporte1', $data);

            // Agregar pie de página
            $pdf->setOption('footer-center', 'Derechos Reservados &copy; ' . date('Y'));
            return $pdf->download('reporte_ventas_por_mes.pdf');
        } catch (\Exception $e) {
            Log::error('Error al generar el reporte de ventas por mes: ' . $e->getMessage());
        } catch (PDOException $e) {
            Log::error('Error al generar el reporte de ventas por mes: ' . $e->getMessage());
        }
    }
    public function tipoHabitacion()
    {
        try {

            $fechaInicio = Carbon::now()->startOfMonth()->toDateString();
            $fechaFin = Carbon::now()->endOfMonth()->toDateString();
            $tiposHabitaciones = DB::select('CALL GenerarReporteTipoHabitacionMes(?, ?)', [$fechaInicio, $fechaFin]);

            $tiposHabitacionesCollection = collect($tiposHabitaciones);
            $agrupadoPorTipo = $tiposHabitacionesCollection->groupBy('Tipo_Habitacion');

            // Convertir la colección agrupada a un arreglo
            $agrupadoPorTipoArray = $agrupadoPorTipo->toArray();
            //por cada tipo de habitacion agregalos a un arreglo con el nombre y la informacion 

            return view('reportes.reporte2', ['tiposHabitaciones' => $agrupadoPorTipoArray, 'fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin]);
        } catch (\Exception $e) {
            Log::error('Error al generar el reporte de tipo de habitaciones: ' . $e->getMessage());
        } catch (PDOException $e) {
            Log::error('Error al generar el reporte de tipo de habitaciones: ' . $e->getMessage());
        }
    }

    public function tipoHabitacionFiltrar(Request $request)
    {
        try {
            $message = [
                'fecha_inicio.required' => 'La fecha de inicio es requerida',
                'fecha_fin.required' => 'La fecha de fin es requerida',
                'fecha_fin.after_or_equal' => 'La fecha de fin debe ser mayor o igual a la fecha de inicio'
            ];
            $validator = Validator::make($request->all(), [
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio'
            ], $message);
            if ($validator->fails()) {
                $fechaInicio = Carbon::now()->startOfMonth()->toDateString();
                $fechaFin = Carbon::now()->endOfMonth()->toDateString();
                return redirect()->route('reporte2', ['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin])->withErrors($validator);
            }
            $fechaInicio = $request->input('fecha_inicio');
            $fechaFin = $request->input('fecha_fin');

            $tiposHabitaciones = DB::select('CALL GenerarReporteTipoHabitacionMes(?, ?)', [$fechaInicio, $fechaFin]);

            $tiposHabitacionesCollection = collect($tiposHabitaciones);
            $agrupadoPorTipo = $tiposHabitacionesCollection->groupBy('Tipo_Habitacion');

            // Convertir la colección agrupada a un arreglo
            $agrupadoPorTipoArray = $agrupadoPorTipo->toArray();

            //por cada tipo de habitacion agregalos a un arreglo con el nombre y la informacion 

            return view('reportes.reporte2', ['tiposHabitaciones' => $agrupadoPorTipoArray, 'fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin]);
        } catch (\Exception $e) {
            Log::error('Error al generar el reporte de tipo de habitaciones: ' . $e->getMessage());
        } catch (PDOException $e) {
            Log::error('Error al generar el reporte de tipo de habitaciones: ' . $e->getMessage());
        }
    }
    public function pdfTipoHabitacion(Request $request)
    {
        try {
            $message = [
                'fecha_inicio.required' => 'La fecha de inicio es requerida',
                'fecha_fin.required' => 'La fecha de fin es requerida',
                'fecha_fin.after_or_equal' => 'La fecha de fin debe ser mayor o igual a la fecha de inicio'
            ];
            $validator = Validator::make($request->all(), [
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio'
            ], $message);
            if ($validator->fails()) {
                $fechaInicio = Carbon::now()->startOfMonth()->toDateString();
                $fechaFin = Carbon::now()->endOfMonth()->toDateString();
                return redirect()->route('reporte2', ['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin])->withErrors($validator);
            }
            $fechaInicio = $request->input('fecha_inicio');
            $fechaFin = $request->input('fecha_fin');
            Carbon::setLocale('es');
            // a carbon
            $fechaInicio = Carbon::parse($fechaInicio);
            $fechaFin = Carbon::parse($fechaFin);

            $fechaInicioFormateada = ucfirst($fechaInicio->translatedFormat('F d Y'));
            $fechaFinFormateada = ucfirst($fechaFin->translatedFormat('F d Y'));

            $tiposHabitaciones = DB::select('CALL GenerarReporteTipoHabitacionMes(?, ?)', [$fechaInicio->toDateString(), $fechaFin]);


            $tiposHabitacionesCollection = collect
            ($tiposHabitaciones);

            $agrupadoPorTipo = $tiposHabitacionesCollection->groupBy('Tipo_Habitacion');

            foreach ($agrupadoPorTipo as $key => $value) {
                $items = [];
                $totalGeneral = $value->sum('Total_General');
                $totalAlojamiento = $value->sum('Total_Alojamiento');
                $totalServicios = $value->sum('Total_Servicios');
                foreach ($value as $item) {
                    $items[] = $item;
                }
                $agrupadoPorTipoConTotales[$key] = [
                    'items'=>$items,
                    'total' => $totalGeneral,
                    'alojamiento' => $totalAlojamiento,
                    'servicios' => $totalServicios
                ];
            }
           

            $logoPath = public_path('images/logo.jpg');

            $data = [
                'tiposHabitaciones' => $agrupadoPorTipoConTotales,
                'fechaInicio' => $fechaInicioFormateada,
                'fechaFin' => $fechaFinFormateada,
                'logo' => $logoPath,
            ];
           
            $pdf = PDF::loadView('reportes.pdf.reporte2',$data );
            // Agregar pie de página
            $pdf->setOption('footer-center', 'Derechos Reservados &copy; ' . date('Y'));
            return $pdf->download('reporte_tipo_habitacion.pdf');
        } catch (\Exception $e) {
            Log::error('Error al generar el reporte de tipo de habitaciones: ' . $e->getMessage());
        } catch (PDOException $e) {
            Log::error('Error al generar el reporte de tipo de habitaciones: ' . $e->getMessage());
        }
    }
}
