<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use function PHPSTORM_META\type;

class dashboardController extends Controller
{
    public function servicios()
    {
        try {
            $fechaInicio = Carbon::now()->startOfYear()->toDateString();
            $fechaFin = Carbon::now()->endOfYear()->toDateString();

            $servicios = DB::table('servicios as s')
                ->leftJoin('servicios_reservas as sr', 's.id', '=', 'sr.servicioID')
                ->leftJoin('habitaciones_reservas as hr', 'sr.habitacionReservaID', '=', 'hr.id')
                ->leftJoin('reservas as r', 'hr.reservaID', '=', 'r.id')
                ->select(
                    's.nombre',
                    DB::raw("SUM(CASE WHEN r.status = 1 AND r.fecha_entrada >= '$fechaInicio' AND r.fecha_entrada <= '$fechaFin' THEN COALESCE(s.precio * sr.cantidad, 0) ELSE 0 END) AS precio"),
                    DB::raw("SUM(CASE WHEN r.status = 1 AND r.fecha_entrada >= '$fechaInicio' AND r.fecha_entrada <='$fechaFin' THEN COALESCE(sr.cantidad, 0) ELSE 0 END) AS cantidad")
                )
                ->groupBy('s.nombre')
                ->get();

            return response()->json($servicios);
        } catch (\Exception $e) {
            Log::error('Error al obtener los servicios: ' . $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Error al obtener los servicios: ' . $e->getMessage());
        } catch (\PDOException $e) {
            Log::error('Error al obtener los servicios: ' . $e->getMessage());
        }
    }



    public function ventaPorMes()
    {
        try {
            Carbon::setLocale('es');
            $fechaInicio = Carbon::now()->startOfYear()->toDateString();
            $fechaFin = Carbon::now()->endOfYear()->toDateString();
            $totales = DB::select('CALL VentasPorMes(?, ?)', [$fechaInicio, $fechaFin]);

            // Crear un array con todos los meses del año
            $meses = [
                'Enero' => 0, 'Febrero' => 0, 'Marzo' => 0, 'Abril' => 0, 'Mayo' => 0, 'Junio' => 0,
                'Julio' => 0, 'Agosto' => 0, 'Septiembre' => 0, 'Octubre' => 0, 'Noviembre' => 0, 'Diciembre' => 0
            ];

            // Mapear los resultados de la base de datos a los meses
            foreach ($totales as $total) {
                $mes = Carbon::parse($total->Mes)->translatedFormat('F'); // Formatear el mes en texto completo
                $mes = ucfirst($mes); // Asegúrate de que el primer carácter sea mayúscula
                if (isset($meses[$mes])) {
                    $meses[$mes] += $total->Total_Mes; // Sumar en lugar de asignar directamente
                }
            }

            // Convertir el array de meses a un formato adecuado para JSON
            $resultados = [];
            foreach ($meses as $mes => $total) {
                $resultados[] = ['mes' => $mes, 'total' => $total];
            }

            return response()->json($resultados);
        } catch (\Exception $e) {
            Log::error('Error al obtener los servicios: ' . $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Error al obtener los servicios: ' . $e->getMessage());
        } catch (\PDOException $e) {
            Log::error('Error al obtener los servicios: ' . $e->getMessage());
        }
    }

    public function usuariosPorMes()
    {
        try {
            Carbon::setLocale('es');
            $fechaInicio = Carbon::now()->startOfYear()->toDateString();
            $fechaFin = Carbon::now()->endOfYear()->toDateString();
            $usuarios = User::whereDate('created_at', '>=', $fechaInicio)
                ->whereDate('created_at', '<=', $fechaFin)
                ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as mes'), DB::raw('COUNT(*) as total'))
                ->groupBy('mes')
                ->get();

            $meses = [
                'Enero' => 0, 'Febrero' => 0, 'Marzo' => 0, 'Abril' => 0, 'Mayo' => 0, 'Junio' => 0,
                'Julio' => 0, 'Agosto' => 0, 'Septiembre' => 0, 'Octubre' => 0, 'Noviembre' => 0, 'Diciembre' => 0
            ];

            foreach ($usuarios as $usuario) {
                $mes = Carbon::parse($usuario->mes)->translatedFormat('F');
                $mes = ucfirst($mes); // Asegúrate de que el primer carácter sea mayúscula
                if (isset($meses[$mes])) {
                    $meses[$mes] += $usuario->total; // Sumar en lugar de asignar directamente
                }
            }

            $usuarios = [];
            foreach ($meses as $mes => $total) {
                $usuarios[] = ['mes' => $mes, 'total' => $total];
            }

            return response()->json($usuarios);
        } catch (\Exception $e) {
            Log::error('Error al obtener los servicios: ' . $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Error al obtener los servicios: ' . $e->getMessage());
        } catch (\PDOException $e) {
            Log::error('Error al obtener los servicios: ' . $e->getMessage());
        }
    }

    public function ingresosPorTipoHabitacion()
    {
        try{
        Carbon::setLocale('es');
        $fechaInicio = Carbon::now()->startOfYear()->toDateString();
        $fechaFin = Carbon::now()->endOfYear()->toDateString();
        $ingresos = DB::table('tipo_habitacion as th')
            ->leftJoin('habitaciones as h', 'th.id', '=', 'h.tipoID')
            ->leftJoin('habitaciones_reservas as hr', 'h.id', '=', 'hr.habitacionID')
            ->leftJoin('reservas as r', function ($join) use ($fechaInicio, $fechaFin) {
                $join->on('hr.reservaID', '=', 'r.id')
                    ->whereDate('r.fecha_entrada', '>=', $fechaInicio)->whereDate('r.fecha_entrada', '<=', $fechaFin)->where('r.status', '=', '1');
            })
            ->select('th.tipo as nombre', DB::raw('COALESCE(SUM(DATEDIFF(r.fecha_salida, r.fecha_entrada) * th.precio_noche), 0) as total'))
            ->groupBy('th.id', 'th.tipo')
            ->orderBy('th.tipo')
            ->get();

        return response()->json($ingresos);
    }
    catch (\Exception $e) {
        Log::error('Error al obtener los servicios: ' . $e->getMessage());
    } catch (\Throwable $e) {
        Log::error('Error al obtener los servicios: ' . $e->getMessage());
    } catch (\PDOException $e) {
        Log::error('Error al obtener los servicios: ' . $e->getMessage());
    }
    }
}
