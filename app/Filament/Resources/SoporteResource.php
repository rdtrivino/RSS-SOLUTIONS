<?php

namespace App\Filament\Resources;

use App\Models\Soporte;
use App\Models\RadicadoRespuesta;
use App\Models\ProductoServicio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use App\Services\FacturacionService; // <<< IMPORTANTE
use Illuminate\Support\Facades\Log;    // <<< NUEVO
use Illuminate\Support\Str;            // <<< NUEVO

class SoporteResource extends Resource
{
    protected static ?string $model = Soporte::class;

    protected static ?string $navigationIcon  = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Operación';
    protected static ?string $navigationLabel = 'Soportes';
    protected static ?int    $navigationSort  = 20;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['radicado.user']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Datos del reporte')->schema([
                Forms\Components\TextInput::make('titulo')->disabled(),
                Forms\Components\Textarea::make('descripcion')->rows(4)->disabled(),
                Forms\Components\Select::make('estado')
                    ->options(['abierto'=>'Abierto','en_progreso'=>'En progreso','cerrado'=>'Cerrado'])
                    ->disabled(),
                Forms\Components\Select::make('prioridad')
                    ->options(['baja'=>'Baja','media'=>'Media','alta'=>'Alta'])
                    ->disabled(),
            ])->columns(2),

            Forms\Components\Section::make('Contacto')->schema([
                Forms\Components\TextInput::make('tipo_documento')->disabled(),
                Forms\Components\TextInput::make('numero_documento')->disabled(),
                Forms\Components\TextInput::make('telefono')->disabled(),
                Forms\Components\TextInput::make('ciudad')->disabled(),
                Forms\Components\TextInput::make('direccion')->disabled(),
            ])->columns(3),

            Forms\Components\Section::make('Equipo')->schema([
                Forms\Components\TextInput::make('tipo_equipo')->disabled(),
                Forms\Components\TextInput::make('marca')->disabled(),
                Forms\Components\TextInput::make('modelo')->disabled(),
                Forms\Components\TextInput::make('serial')->disabled(),
                Forms\Components\TextInput::make('so')->label('S.O.')->disabled(),
                Forms\Components\TextInput::make('accesorios')->disabled(),
            ])->columns(3),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('radicado.numero')
                    ->label('Radicado')->searchable()->sortable()->copyable(),

                Tables\Columns\TextColumn::make('titulo')
                    ->label('Título')->searchable()->limit(40),

                Tables\Columns\TextColumn::make('prioridad')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'alta' => 'danger', 'media' => 'warning', 'baja' => 'success', default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'abierto' => 'warning', 'en_progreso' => 'info', 'cerrado' => 'success', default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('radicado.user.name')
                    ->label('Radicado por')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')->options([
                    'abierto'=>'Abierto','en_progreso'=>'En progreso','cerrado'=>'Cerrado'
                ]),
                Tables\Filters\SelectFilter::make('prioridad')->options([
                    'alta'=>'Alta','media'=>'Media','baja'=>'Baja'
                ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Action::make('cambiar_estado')
                    ->label('Cambiar estado')
                    ->icon('heroicon-m-arrow-path')
                    ->visible(fn (Soporte $record) => $record->estado !== 'cerrado')
                    ->form([
                        Forms\Components\Select::make('nuevo_estado')
                            ->label('Nuevo estado')
                            ->options(function (?Soporte $record) {
                                return match ($record?->estado) {
                                    'abierto'     => ['en_progreso' => 'En progreso', 'cerrado' => 'Cerrado'],
                                    'en_progreso' => ['cerrado' => 'Cerrado'],
                                    default       => ['cerrado' => 'Cerrado'],
                                };
                            })
                            ->required(),

                        Forms\Components\CheckboxList::make('tareas')
                            ->label('Servicios realizados')
                            ->options([
                                'diag'             => 'Diagnóstico',
                                'formateo'         => 'Formateo e instalación de S.O.',
                                'backup'           => 'Backup de información',
                                'cambio_bateria'   => 'Cambio de batería',
                                'cambio_disco'     => 'Cambio de disco / SSD',
                                'limpieza'         => 'Limpieza y mantenimiento',
                                'inst_software'    => 'Instalación de programas',
                                'drivers'          => 'Actualización de drivers',
                                'red'              => 'Reparación de red / internet',
                                'pantalla'         => 'Cambio de pantalla',

                                // 🔹 Servicios adicionales
                                'placa'            => 'Reparación de tarjeta madre / placa base',
                                'teclado'          => 'Cambio de teclado',
                                'carcasa'          => 'Cambio de bisagras / carcasa',
                                'ventilador'       => 'Reemplazo de ventilador / sistema de refrigeración',
                                'ram'              => 'Ampliación de memoria RAM',
                                'recuperacion'     => 'Recuperación de datos',
                                'perifericos'      => 'Instalación / configuración de periféricos',
                                'migracion'        => 'Migración de sistema a otro equipo',
                                'soporte_remoto'   => 'Soporte remoto',
                                'contrato'         => 'Contratos de mantenimiento preventivo',
                                'servidores'       => 'Gestión de servidores y backups',
                                'correo'           => 'Configuración de correo corporativo',
                                'seguridad'        => 'Seguridad informática (antivirus, firewall)',
                                'camaras'          => 'Instalación de cámaras de seguridad',
                                'capacitacion'     => 'Capacitación básica en software',
                                'asesoria'         => 'Asesoría en compra y actualización de equipos',

                                // Extra
                                'otros'            => 'Otros (especificar abajo)',
                            ])
                            ->columns(2)
                            ->live(),

                        Forms\Components\Textarea::make('tareas_otras')
                            ->label('Otros trabajos (detalle)')
                            ->rows(2)
                            ->maxLength(500)
                            ->placeholder('Ej. Reemplazo de teclado, recuperación de partición, etc.')
                            ->visible(fn (Forms\Get $get) => in_array('otros', $get('tareas') ?? [])),

                        Forms\Components\Textarea::make('notas')
                            ->label('Trabajo realizado / notas')
                            ->rows(3),

                        Forms\Components\FileUpload::make('fotos')
                            ->label('Fotos del trabajo (opcional)')
                            ->multiple()
                            ->image()
                            ->directory('radicados/soporte/fotos'),
                    ])
                    ->action(function (Soporte $record, array $data) {

                        if ($record->estado === 'cerrado') {
                            Notification::make()
                                ->title('Caso cerrado')
                                ->body('Este soporte ya está cerrado y no puede reabrirse.')
                                ->danger()->send();
                            return;
                        }

                        $nuevo   = $data['nuevo_estado'];
                        $tareas  = $data['tareas'] ?? [];
                        $otras   = trim((string)($data['tareas_otras'] ?? ''));

                        $transicionesValidas = match ($record->estado) {
                            'abierto'     => ['en_progreso', 'cerrado'],
                            'en_progreso' => ['cerrado'],
                            default       => [],
                        };
                        if (! in_array($nuevo, $transicionesValidas, true)) {
                            Notification::make()
                                ->title('Transición inválida')
                                ->body('No es posible cambiar el estado seleccionado desde el estado actual.')
                                ->warning()->send();
                            return;
                        }

                        $record->update(['estado' => $nuevo]);

                        // Map de claves -> nombre catálogo
                        $tareasMap = [
                            'diag'             => 'Diagnóstico',
                            'formateo'         => 'Formateo e instalación de S.O.',
                            'backup'           => 'Backup de información',
                            'cambio_bateria'   => 'Cambio de batería',
                            'cambio_disco'     => 'Cambio de disco / SSD',
                            'limpieza'         => 'Limpieza y mantenimiento',
                            'inst_software'    => 'Instalación de programas',
                            'drivers'          => 'Actualización de drivers',
                            'red'              => 'Reparación de red / internet',
                            'pantalla'         => 'Cambio de pantalla',

                            // 🔹 Servicios adicionales
                            'placa'            => 'Reparación de tarjeta madre / placa base',
                            'teclado'          => 'Cambio de teclado',
                            'carcasa'          => 'Cambio de bisagras / carcasa',
                            'ventilador'       => 'Reemplazo de ventilador / sistema de refrigeración',
                            'ram'              => 'Ampliación de memoria RAM',
                            'recuperacion'     => 'Recuperación de datos',
                            'perifericos'      => 'Instalación / configuración de impresoras y periféricos',
                            'migracion'        => 'Migración de sistema a otro equipo',
                            'soporte_remoto'   => 'Soporte remoto',
                            'contrato'         => 'Contratos de mantenimiento preventivo',
                            'servidores'       => 'Gestión de servidores y backups',
                            'correo'           => 'Configuración de correo corporativo',
                            'seguridad'        => 'Seguridad informática (antivirus, firewall)',
                            'camaras'          => 'Instalación de cámaras de seguridad',
                            'capacitacion'     => 'Capacitación básica en software',
                            'asesoria'         => 'Asesoría en compra y actualización de equipos',
                        ];
                        $seleccionLabels = array_values(array_intersect_key($tareasMap, array_flip($tareas)));

                        // Catálogo
                        $catalogo = ProductoServicio::query()
                            ->whereIn('nombre', $seleccionLabels)
                            ->where('activo', true)
                            ->get()
                            ->keyBy('nombre');

                        // Construir items
                        $itemsCalc = [];
                        $subtotal = $ivaTotal = $total = 0;

                        foreach ($seleccionLabels as $i => $nombre) {
                            $prod = $catalogo->get($nombre);
                            $cant = 1;
                            $pu   = $prod ? (float) $prod->precio_base : 0.0;
                            $ivaP = $prod ? (float) $prod->iva_pct     : 19.0;

                            $base     = $cant * $pu;
                            $sub      = round($base, 2);
                            $ivaMonto = round($sub * $ivaP / 100, 2);
                            $tot      = round($sub + $ivaMonto, 2);

                            $itemsCalc[] = [
                                'concepto'        => $nombre,
                                'unidad'          => $prod->unidad ?? 'servicio',
                                'cantidad'        => $cant,
                                'precio_unitario' => $pu,
                                'descuento_pct'   => null,
                                'iva_pct'         => $ivaP,
                                'subtotal'        => $sub,
                                'iva_monto'       => $ivaMonto,
                                'total'           => $tot,
                                'orden'           => $i + 1,
                            ];

                            $subtotal += $sub;
                            $ivaTotal += $ivaMonto;
                            $total    += $tot;
                        }

                        if (in_array('otros', $tareas, true) && $otras !== '') {
                            $itemsCalc[] = [
                                'concepto'        => "Otros: " . $otras,
                                'unidad'          => 'servicio',
                                'cantidad'        => 1,
                                'precio_unitario' => 0,
                                'descuento_pct'   => null,
                                'iva_pct'         => 0,
                                'subtotal'        => 0,
                                'iva_monto'       => 0,
                                'total'           => 0,
                                'orden'           => count($itemsCalc) + 1,
                            ];
                        }

                        $payload = [
                            'estado'        => $nuevo,
                            'tareas'        => $tareas,
                            'tareas_otras'  => $otras ?: null,
                            'notas'         => $data['notas'] ?? null,
                            'fotos'         => $data['fotos'] ?? [],
                            'items'         => $itemsCalc,
                            'totales'       => [
                                'subtotal' => round($subtotal, 2),
                                'iva'      => round($ivaTotal, 2),
                                'total'    => round($total, 2),
                                'moneda'   => 'COP',
                            ],
                        ];

                        $resp = RadicadoRespuesta::create([
                            'radicado_id' => $record->radicado?->id,
                            'user_id'     => Auth::id(),
                            'formato'     => 'soporte',
                            'resultado'   => $nuevo,
                            'data'        => $payload,
                            'cierra_caso' => $nuevo === 'cerrado',
                        ]);

                        // Si se cerró, generar PDF
                        if ($nuevo === 'cerrado' && $record->radicado) {
                            $pdf  = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.soporte-cierre', [
                                'radicado'  => $record->radicado->load('user'),
                                'soporte'   => $record,
                                'respuesta' => $resp,
                                'items'     => $payload['items'],
                                'subtotal'  => $payload['totales']['subtotal'],
                                'iva'       => $payload['totales']['iva'],
                                'total'     => $payload['totales']['total'],
                            ]);

                            $path = "radicados/{$record->radicado->id}/cierre-{$resp->id}.pdf";
                            Storage::disk('public')->put($path, $pdf->output());
                            $resp->update(['pdf_path' => $path]);
                        }

                        // >>> FACTURACIÓN: crear/actualizar factura desde el cierre (con diagnóstico)
                        if ($nuevo === 'cerrado' && $record->radicado) {
                            $errorId = Str::uuid()->toString();
                            try {
                                Log::info("[FACTURA][$errorId] inicio", [
                                    'radicado_id' => $record->radicado->id,
                                    'soporte_id'  => $record->id,
                                    'resp_id'     => $resp->id ?? null,
                                ]);

                                $svc = app(FacturacionService::class);
                                $factura = $svc->crearDesdeSoporteCierre(
                                    $record->radicado->fresh(),
                                    $record->fresh(),
                                    $resp->fresh()
                                );

                                Log::info("[FACTURA][$errorId] ok", [
                                    'factura_id' => $factura->id ?? null,
                                    'total'      => $factura->total ?? null,
                                    'pagado'     => $factura->pagado ?? null,
                                    'saldo'      => $factura->saldo ?? null,
                                ]);
                            } catch (\Throwable $e) {
                                Log::error("[FACTURA][$errorId] ".$e->getMessage(), [
                                    'trace'       => $e->getTraceAsString(),
                                    'radicado_id' => $record->radicado->id ?? null,
                                    'soporte_id'  => $record->id ?? null,
                                    'resp_id'     => $resp->id ?? null,
                                    'user_id'     => Auth::id(),
                                ]);

                                $msg = trim($e->getMessage() ?: get_class($e));
                                $first = '';
                                foreach ($e->getTrace() as $t) {
                                    if (!empty($t['file']) && !empty($t['line'])) {
                                        $first = $t['file'].':'.$t['line'];
                                        break;
                                    }
                                }

                                Notification::make()
                                    ->title('Cierre realizado con observación')
                                    ->body("ErrorId: {$errorId}\n\n{$msg}\n\nEn: {$first}")
                                    ->warning()
                                    ->persistent()
                                    ->send();
                            }
                        }

                        Notification::make()->title('Estado actualizado')->success()->send();
                    }),

                // ─────────────────────────────────────────────────────────────
                // DESCARGAR PDF → Forzar descarga por ruta Laravel
                // ─────────────────────────────────────────────────────────────
                Action::make('descargar_pdf')
                    ->label('Descargar PDF')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->url(fn (Soporte $record) => route('soporte.descargar.pdf', $record))
                    ->openUrlInNewTab(false) // evitar nueva pestaña
                    ->visible(function (Soporte $record) {
                        if (! $record->radicado) return false;

                        return RadicadoRespuesta::query()
                            ->where('radicado_id', $record->radicado->id)
                            ->where('formato', 'soporte')
                            ->where('cierra_caso', true)
                            ->whereNotNull('pdf_path')
                            ->exists();
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => SoporteResource\Pages\ListSoportes::route('/'),
            'view'  => SoporteResource\Pages\ViewSoporte::route('/{record}'),
        ];
    }

    public static function getRelations(): array
    {
        return [];
    }
}
