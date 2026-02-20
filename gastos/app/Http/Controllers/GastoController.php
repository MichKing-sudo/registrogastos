<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class GastoController extends Controller
{
    /**
     * Mostrar el formulario y la lista de gastos
     */
    public function index()
    {
        try {
            // Inicializar la sesión si no existe
            if (!Session::has('gastos')) {
                Session::put('gastos', []);
            }
            
            $gastos = Session::get('gastos', []);
            $total = $this->calcularTotal($gastos);
            
            // Forzar URL base para Codespaces
            $this->forzarUrlCodespaces();
            
            return view('gastos.index', compact('gastos', 'total'));
            
        } catch (\Exception $e) {
            Log::error('Error en index: ' . $e->getMessage());
            return view('gastos.index', ['gastos' => [], 'total' => 0])
                ->with('error', 'Error al cargar los datos');
        }
    }
    
    /**
     * Guardar un nuevo gasto
     */
    public function store(Request $request)
    {
        try {
            // Forzar URL base para Codespaces
            $this->forzarUrlCodespaces();
            
            // Validar los datos
            $request->validate([
                'descripcion' => 'required|min:3|max:255',
                'monto' => 'required|numeric|min:0.01',
            ], [
                'descripcion.required' => 'La descripción es obligatoria',
                'descripcion.min' => 'La descripción debe tener al menos 3 caracteres',
                'monto.required' => 'El monto es obligatorio',
                'monto.numeric' => 'El monto debe ser un número',
                'monto.min' => 'El monto debe ser mayor a 0',
            ]);
            
            // Obtener gastos actuales
            $gastos = Session::get('gastos', []);
            
            // Crear nuevo gasto
            $nuevoGasto = [
                'id' => count($gastos) > 0 ? max(array_column($gastos, 'id')) + 1 : 1,
                'descripcion' => trim($request->descripcion),
                'monto' => floatval($request->monto),
                'created_at' => now()->format('Y-m-d H:i:s')
            ];
            
            // Agregar a la sesión
            array_push($gastos, $nuevoGasto);
            Session::put('gastos', $gastos);
            
            // Mensaje de éxito - Usar redirect()->away() para URL externa
            return redirect()->away($this->getBaseUrl() . route('gastos.index', [], false))
                ->with('success', '✅ Gasto registrado correctamente');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error al guardar gasto: ' . $e->getMessage());
            return redirect()->away($this->getBaseUrl() . route('gastos.index', [], false))
                ->with('error', '❌ Error al registrar el gasto: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Eliminar un gasto específico
     */
    public function destroy($id)
    {
        try {
            $this->forzarUrlCodespaces();
            
            $gastos = Session::get('gastos', []);
            $gastoEliminado = null;
            
            foreach ($gastos as $gasto) {
                if ($gasto['id'] == $id) {
                    $gastoEliminado = $gasto;
                    break;
                }
            }
            
            $gastos = array_filter($gastos, function($gasto) use ($id) {
                return $gasto['id'] != $id;
            });
            
            $gastos = array_values($gastos);
            Session::put('gastos', $gastos);
            
            $mensaje = $gastoEliminado 
                ? "✅ Gasto '{$gastoEliminado['descripcion']}' eliminado"
                : "✅ Gasto eliminado correctamente";
            
            return redirect()->away($this->getBaseUrl() . route('gastos.index', [], false))
                ->with('success', $mensaje);
                
        } catch (\Exception $e) {
            Log::error('Error al eliminar gasto: ' . $e->getMessage());
            return redirect()->away($this->getBaseUrl() . route('gastos.index', [], false))
                ->with('error', '❌ Error al eliminar el gasto');
        }
    }
    
    /**
     * Limpiar todos los gastos
     */
    public function limpiar()
    {
        try {
            $this->forzarUrlCodespaces();
            
            $cantidad = count(Session::get('gastos', []));
            Session::put('gastos', []);
            
            return redirect()->away($this->getBaseUrl() . route('gastos.index', [], false))
                ->with('success', "✅ {$cantidad} gastos eliminados correctamente");
                
        } catch (\Exception $e) {
            Log::error('Error al limpiar gastos: ' . $e->getMessage());
            return redirect()->away($this->getBaseUrl() . route('gastos.index', [], false))
                ->with('error', '❌ Error al limpiar los gastos');
        }
    }
    
    /**
     * Calcular el total de gastos
     */
    private function calcularTotal($gastos)
    {
        $total = 0;
        foreach ($gastos as $gasto) {
            if (isset($gasto['monto']) && is_numeric($gasto['monto'])) {
                $total += floatval($gasto['monto']);
            }
        }
        return $total;
    }
    
    /**
     * Forzar URL base para Codespaces
     */
    private function forzarUrlCodespaces()
    {
        $baseUrl = $this->getBaseUrl();
        URL::forceRootUrl($baseUrl);
        URL::forceScheme('https');
    }
    
    /**
     * Obtener URL base de Codespaces
     */
    private function getBaseUrl()
    {
        // Detectar si estamos en Codespaces
        if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'app.github.dev') !== false) {
            return 'https://' . $_SERVER['HTTP_HOST'];
        }
        
        // URL por defecto
        return 'https://humble-space-invention-97jq56jpvg57fpqrx-8000.app.github.dev';
    }
}