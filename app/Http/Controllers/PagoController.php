<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Pago;
use App\Models\Recibo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PagoController extends Controller
{
    // Mostrar la pantalla de cobros con buscador
    public function index(Request $request)
    {
        $search = $request->input('search');
        $usuario = null;

        if ($search) {
            // Buscamos al usuario con su tarifa, su red y ordenamos sus pagos
            $usuario = Usuario::with([
                'tarifa',
                'red',
                'pagos' => function ($query) {
                    $query->orderBy('periodo_anio', 'desc')->orderBy('periodo_mes', 'desc');
                }
            ])
                ->where('folio', 'LIKE', "%{$search}%")
                ->orWhere('nombre_completo', 'LIKE', "%{$search}%")
                ->first();

            // Si se encontró al usuario
            if ($usuario) {
                // 1. Verificamos y generamos el pago pendiente del mes en curso si no existe
                $usuario->verificarYGenerarPagosPendientes();

                // 2. RECARGAMOS la relación de pagos para que incluya el nuevo registro recién creado
                $usuario->load([
                    'pagos' => function ($query) {
                        $query->orderBy('periodo_anio', 'desc')->orderBy('periodo_mes', 'desc');
                    }
                ]);
            }
        }

        return view('pagos.index', compact('usuario', 'search'));
    }


    // Registrar o liquidar el pago de un periodo (Soporta pendientes y pagos adelantados)
    public function store(Request $request)
    {
        $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'periodo_mes' => 'required|string',
            'periodo_anio' => 'required|integer',
            'monto' => 'required|numeric|min:0',
        ]);

        $periodoCompleto = $request->periodo_mes . ' ' . $request->periodo_anio;

        // Usamos una transacción para que el pago y su recibo queden guardados de forma segura
        return DB::transaction(function () use ($request, $periodoCompleto) {

            // 1. Buscar si ya existe un registro para este mes y año en este usuario
            $pagoExistente = Pago::where('usuario_id', $request->usuario_id)
                ->where('periodo_mes', $request->periodo_mes)
                ->where('periodo_anio', $request->periodo_anio)
                ->first();

            if ($pagoExistente) {
                // Si ya fue pagado, bloqueamos para evitar duplicados
                if ($pagoExistente->estatus === 'pagado') {
                    return back()->withErrors(['error' => 'El periodo ' . $periodoCompleto . ' ya se encuentra pagado para este usuario.']);
                }

                // Si estaba como 'pendiente', lo actualizamos a 'pagado'
                $pagoExistente->update([
                    'monto' => $request->monto,
                    'fecha_pago' => Carbon::now(),
                    'estatus' => 'pagado',
                ]);

                // Generar o actualizar su recibo correspondiente como pagado
                Recibo::create([
                    'usuario_id' => $request->usuario_id,
                    'monto' => $request->monto,
                    'mes_anio' => $periodoCompleto,
                    'estatus' => 'pagado',
                    'fecha_pago' => Carbon::now(),
                ]);

                return back()->with('success', '¡El periodo pendiente ' . $periodoCompleto . ' ha sido cobrado, liquidado y su recibo generado con éxito!');
            }

            // 2. Si no existe (es un pago adelantado manual), lo creamos directamente como 'pagado'
            $pago = Pago::create([
                'usuario_id' => $request->usuario_id,
                'periodo_mes' => $request->periodo_mes,
                'periodo_anio' => $request->periodo_anio,
                'monto' => $request->monto,
                'fecha_pago' => Carbon::now(),
                'estatus' => 'pagado',
            ]);

            // Generar el recibo correspondiente para este pago adelantado
            Recibo::create([
                'usuario_id' => $request->usuario_id,
                'monto' => $request->monto,
                'mes_anio' => $periodoCompleto,
                'estatus' => 'pagado',
                'fecha_pago' => Carbon::now(),
            ]);

            return back()->with('success', '¡Pago adelantado registrado, cobrado y recibo con QR generado con éxito!');
        });
    }

    public function matriz(Request $request)
    {
        $search = $request->input('search');
        $anioActual = $request->input('anio', date('Y'));

        // Cargamos TODOS los pagos del usuario para que la matriz evalué correctamente cualquier año
        $query = Usuario::with(['tarifa', 'red', 'pagos']);

        if ($search) {
            $query->where('folio', 'LIKE', "%{$search}%")
                ->orWhere('nombre_completo', 'LIKE', "%{$search}%");
        }

        $usuarios = $query->paginate(10);
        $mesesAnio = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

        return view('pagos.matriz', compact('usuarios', 'search', 'anioActual', 'mesesAnio'));
    }

    public function anular($id)
    {
        $pago = Pago::findOrFail($id);

        // Buscar el recibo vinculado a este usuario y periodo exacto
        $periodoCompleto = $pago->periodo_mes . ' ' . $pago->periodo_anio;
        $recibo = Recibo::where('usuario_id', $pago->usuario_id)
            ->where('mes_anio', $periodoCompleto)
            ->first();

        // Mapeo de meses a números para comparar fechas
        $mesesNumero = [
            'Enero' => 1,
            'Febrero' => 2,
            'Marzo' => 3,
            'Abril' => 4,
            'Mayo' => 5,
            'Junio' => 6,
            'Julio' => 7,
            'Agosto' => 8,
            'Septiembre' => 9,
            'Octubre' => 10,
            'Noviembre' => 11,
            'Diciembre' => 12
        ];

        $mesPagoNum = $mesesNumero[ucfirst(strtolower(trim($pago->periodo_mes)))] ?? 0;
        $anioPago = (int) $pago->periodo_anio;

        $mesActualNum = (int) Carbon::now()->month;
        $anioActual = (int) Carbon::now()->year;

        // Determinamos si el recibo es un mes adelantado (futuro)
        $esAdelantado = ($anioPago > $anioActual) || ($anioPago == $anioActual && $mesPagoNum > $mesActualNum);

        if ($esAdelantado) {
            // Si es adelantado, eliminamos el registro del pago y su recibo por completo
            if ($recibo) {
                $recibo->delete();
            }
            $pago->delete();
            return back()->with('success', 'El recibo y el pago adelantado han sido eliminados correctamente.');
        } else {
            // Si es mes actual o pasado, ambos se anulan y regresan a estatus pendiente
            if ($recibo) {
                $recibo->estatus = 'pendiente';
                $recibo->fecha_pago = null;
                $recibo->save();
            }
            $pago->estatus = 'pendiente';
            $pago->fecha_pago = null;
            $pago->save();
            return back()->with('success', 'El pago y su recibo han sido anulados; el periodo ahora está pendiente.');
        }
    }

    public function ticket($id)
    {
        $pago = Pago::with(['usuario.tarifa', 'usuario.red'])->findOrFail($id);

        return view('pagos.ticket', compact('pago'));
    }

    public function comprobante($id)
    {
        $pago = Pago::with(['usuario.tarifa', 'usuario.red'])->findOrFail($id);

        return view('pagos.comprobante', compact('pago'));
    }
}