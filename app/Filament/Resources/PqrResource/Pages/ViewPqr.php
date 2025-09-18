<?php

namespace App\Filament\Resources\PqrResource\Pages;

use App\Filament\Resources\PqrResource;
use App\Models\Pqr;
use App\Models\RadicadoRespuesta;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\URL; // Descomenta si quieres usar ruta firmada en "Descargar PDF"

class ViewPqr extends ViewRecord
{
    protected static string $resource = PqrResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),

            Actions\Action::make('cambiar_estado')
                ->label('Cambiar estado')
                ->icon('heroicon-m-arrow-path')
                ->form([
                    Forms\Components\Select::make('nuevo_estado')
                        ->label('Nuevo estado')
                        ->options(fn (?Pqr $record) => match ($record?->estado) {
                            null, '', 'radicado' => [
                                'radicado'   => 'Radicado',
                                'en_proceso' => 'En proceso',
                                'resuelto'   => 'Resuelto',
                            ],
                            'en_proceso' => [
                                'en_proceso' => 'En proceso',
                                'resuelto'   => 'Resuelto',
                            ],
                            default => [
                                'resuelto' => 'Resuelto',
                            ],
                        })
                        ->required(),
                    Forms\Components\Textarea::make('nota')
                        ->label('Nota interna')
                        ->rows(2),
                ])
                ->action(function (Pqr $record, array $data): void {
                    $nuevo = $data['nuevo_estado'];

                    $validas = match ($record->estado) {
                        null, '', 'radicado' => ['radicado', 'en_proceso', 'resuelto'],
                        'en_proceso'         => ['en_proceso', 'resuelto'],
                        default              => ['resuelto'],
                    };

                    if (! in_array($nuevo, $validas, true)) {
                        Notification::make()
                            ->title('Transición inválida')
                            ->body('No es posible cambiar al estado seleccionado desde el estado actual.')
                            ->warning()
                            ->send();
                        return;
                    }

                    // 1) Actualiza estado
                    $record->update(['estado' => $nuevo]);

                    // 2) Asegura radicado
                    $radicado = $record->radicado
                        ?? $record->radicado()->create([
                            'numero'  => 'PQR-'.date('Y').'-'.str_pad((string) $record->id, 6, '0', STR_PAD_LEFT),
                            'modulo'  => 'pqr',
                            'user_id' => $record->user_id ?? Auth::id(),
                        ]);

                    // 3) Registra respuesta
                    RadicadoRespuesta::create([
                        'radicado_id' => $radicado->id,
                        'user_id'     => Auth::id(),
                        'formato'     => 'pqr',
                        'resultado'   => $nuevo,
                        'data'        => ['nota' => $data['nota'] ?? null],
                        'cierra_caso' => $nuevo === 'resuelto',
                    ]);

                    Notification::make()
                        ->title('Estado actualizado')
                        ->success()
                        ->send();

                    // 4) Refresca el registro en la página y el formulario (¡sin pasar argumentos!)
                    $this->record = $record->fresh(['radicado']);
                    $this->refreshFormData();
                }),

            Actions\Action::make('adjuntar_pdf')
                ->label('Adjuntar/Actualizar PDF')
                ->icon('heroicon-o-paper-clip')
                ->form([
                    Forms\Components\FileUpload::make('pdf')
                        ->label('Archivo PDF')
                        ->acceptedFileTypes(['application/pdf'])
                        ->directory('radicados/pqr')
                        ->disk('public')
                        ->required(),
                    Forms\Components\Textarea::make('nota')->label('Nota')->rows(2),
                ])
                ->action(function (Pqr $record, array $data): void {
                    // Asegura radicado
                    $radicado = $record->radicado
                        ?? $record->radicado()->create([
                            'numero'  => 'PQR-'.date('Y').'-'.str_pad((string) $record->id, 6, '0', STR_PAD_LEFT),
                            'modulo'  => 'pqr',
                            'user_id' => $record->user_id ?? Auth::id(),
                        ]);

                    // Guarda respuesta con PDF
                    RadicadoRespuesta::create([
                        'radicado_id' => $radicado->id,
                        'user_id'     => Auth::id(),
                        'formato'     => 'pqr',
                        'resultado'   => $record->estado,
                        'data'        => ['nota' => $data['nota'] ?? null],
                        'cierra_caso' => $record->estado === 'resuelto',
                        'pdf_path'    => $data['pdf'],
                    ]);

                    Notification::make()
                        ->title('PDF adjuntado')
                        ->success()
                        ->send();

                    $this->record = $record->fresh(['radicado']);
                    $this->refreshFormData();
                }),

            Actions\Action::make('cerrar')
                ->label('Cerrar como Resuelto')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn (Pqr $record) => $record->estado !== 'resuelto')
                ->form([
                    Forms\Components\FileUpload::make('pdf')
                        ->label('Adjuntar PDF de cierre (opcional)')
                        ->acceptedFileTypes(['application/pdf'])
                        ->directory('radicados/pqr')
                        ->disk('public'),
                    Forms\Components\Textarea::make('nota')->label('Nota de cierre')->rows(2),
                ])
                ->action(function (Pqr $record, array $data): void {
                    // 1) Cierra PQR
                    $record->update(['estado' => 'resuelto']);

                    // 2) Asegura radicado
                    $radicado = $record->radicado
                        ?? $record->radicado()->create([
                            'numero'  => 'PQR-'.date('Y').'-'.str_pad((string) $record->id, 6, '0', STR_PAD_LEFT),
                            'modulo'  => 'pqr',
                            'user_id' => $record->user_id ?? Auth::id(),
                        ]);

                    // 3) Respuesta (con o sin PDF)
                    $resp = RadicadoRespuesta::create([
                        'radicado_id' => $radicado->id,
                        'user_id'     => Auth::id(),
                        'formato'     => 'pqr',
                        'resultado'   => 'resuelto',
                        'data'        => ['nota' => $data['nota'] ?? null],
                        'cierra_caso' => true,
                        'pdf_path'    => $data['pdf'] ?? null,
                    ]);

                    // 4) Si no adjuntaron PDF, intenta generar acuse (si existe la vista)
                    if (empty($data['pdf']) && view()->exists('pdf.pqr_acuse')) {
                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.pqr_acuse', [
                            'radicado'  => $radicado->load('user'),
                            'pqr'       => $record,
                            'respuesta' => $resp,
                        ]);

                        $path = "radicados/{$radicado->id}/pqr-{$resp->id}.pdf";
                        Storage::disk('public')->put($path, $pdf->output());
                        $resp->update(['pdf_path' => $path]);
                    }

                    Notification::make()
                        ->title('PQR cerrada')
                        ->success()
                        ->send();

                    $this->record = $record->fresh(['radicado']);
                    $this->refreshFormData();
                }),

            Actions\Action::make('descargar_pdf')
                ->label('Descargar PDF')
                ->icon('heroicon-m-arrow-down-tray')
                ->url(function (Pqr $record) {
                    if (! $record->radicado) {
                        return null;
                    }

                    $ultima = RadicadoRespuesta::query()
                        ->where('radicado_id', $record->radicado->id)
                        ->where('formato', 'pqr')
                        ->whereNotNull('pdf_path')
                        ->latest('id')
                        ->first();

                    if (! $ultima?->pdf_path) {
                        return null;
                    }

                    // A) URL pública del disco:
                    return Storage::disk('public')->url($ultima->pdf_path);

                    // B) O bien ruta firmada a tu controlador (descomenta import y esto):
                    // return URL::signedRoute('radicado.pdf', ['radicado' => $record->radicado->id]);
                })
                ->visible(function (Pqr $record): bool {
                    if (! $record->radicado) {
                        return false;
                    }

                    return RadicadoRespuesta::query()
                        ->where('radicado_id', $record->radicado->id)
                        ->where('formato', 'pqr')
                        ->whereNotNull('pdf_path')
                        ->exists();
                })
                ->openUrlInNewTab(),
        ];
    }
}
