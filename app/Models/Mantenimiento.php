<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mantenimiento extends Model
{
    protected $fillable = [
        'bus_id',
        'fecha_mantenimiento',
        'km_anterior',
        'km_actual',
        'km_actual_recorrido',
        'tipo_mantenimiento',
        'observaciones',
    ];
    protected static function rangosTipo(): array
    {
        return [
            ['min' =>     0.0, 'max' =>   3500.0, 'tipo' => 'L'],
            ['min' =>  3500.0, 'max' =>   7000.0, 'tipo' => 'MP1'],
            ['min' =>  7000.0, 'max' =>  10500.0, 'tipo' => 'L'],
            ['min' => 10500.0, 'max' =>  14000.0, 'tipo' => 'MP2'],
            ['min' => 14000.0, 'max' =>  17500.0, 'tipo' => 'L'],
            ['min' => 17500.0, 'max' =>  21000.0, 'tipo' => 'MP1'],
            ['min' => 21000.0, 'max' =>  24500.0, 'tipo' => 'L'],
            ['min' => 24500.0, 'max' =>  28000.0, 'tipo' => 'MP2'],
            ['min' => 28000.0, 'max' =>  31500.0, 'tipo' => 'L'],
            ['min' => 31500.0, 'max' =>  35000.0, 'tipo' => 'MP3'],
            ['min' => 35000.0, 'max' =>  38500.0, 'tipo' => 'L'],
            ['min' => 38500.0, 'max' =>  42000.0, 'tipo' => 'MP2'],
            ['min' => 42000.0, 'max' =>  45500.0, 'tipo' => 'L'],
            ['min' => 45500.0, 'max' =>  49000.0, 'tipo' => 'MP1'],
            ['min' => 49000.0, 'max' =>  52500.0, 'tipo' => 'L'],
            ['min' => 52500.0, 'max' =>  56000.0, 'tipo' => 'MP4'],
            ['min' => 56000.0, 'max' =>  59500.0, 'tipo' => 'L'],
            ['min' => 59500.0, 'max' =>  63000.0, 'tipo' => 'MP1'],
            ['min' => 63000.0, 'max' =>  66500.0, 'tipo' => 'L'],
            ['min' => 66500.0, 'max' =>  70000.0, 'tipo' => 'MP5'],
            ['min' => 70000.0, 'max' =>  73500.0, 'tipo' => 'L'],
            ['min' => 73500.0, 'max' =>  77000.0, 'tipo' => 'MP1'],
            ['min' => 77000.0, 'max' =>  80500.0, 'tipo' => 'L'],
            ['min' => 80500.0, 'max' =>  84000.0, 'tipo' => 'MP2'],
            ['min' => 84000.0, 'max' =>  87500.0, 'tipo' => 'L'],
            ['min' => 87500.0, 'max' =>  91000.0, 'tipo' => 'MP1'],
            ['min' => 91000.0, 'max' =>  94500.0, 'tipo' => 'L'],
            ['min' => 94500.0, 'max' =>  98000.0, 'tipo' => 'MP2'],
            ['min' => 98000.0, 'max' => 101500.0, 'tipo' => 'L'],
            ['min' =>101500.0, 'max' => 105000.0, 'tipo' => 'MP3'],
            ['min' =>105000.0, 'max' => 108500.0, 'tipo' => 'L'],
            ['min' =>108500.0, 'max' => 112000.0, 'tipo' => 'MP4'],
            ['min' =>112000.0, 'max' => 115500.0, 'tipo' => 'L'],
            ['min' =>115500.0, 'max' => 119000.0, 'tipo' => 'MP1'],
            ['min' =>119000.0, 'max' => 122500.0, 'tipo' => 'L'],
            ['min' =>122500.0, 'max' => 126000.0, 'tipo' => 'MP2'],
            ['min' =>126000.0, 'max' => 129500.0, 'tipo' => 'L'],
            ['min' =>129500.0, 'max' => 133000.0, 'tipo' => 'MP1'],
            ['min' =>133000.0, 'max' => 136500.0, 'tipo' => 'L'],
            ['min' =>136500.0, 'max' => 140000.0, 'tipo' => 'MP5'],
            ['min' =>140000.0, 'max' => 143500.0, 'tipo' => 'L'],
            ['min' =>143500.0, 'max' => 147000.0, 'tipo' => 'MP1'],
            ['min' =>147000.0, 'max' => 150500.0, 'tipo' => 'L'],
            ['min' =>150500.0, 'max' => 154000.0, 'tipo' => 'MP2'],
            ['min' =>154000.0, 'max' => 157500.0, 'tipo' => 'L'],
            ['min' =>157500.0, 'max' => 161000.0, 'tipo' => 'MP1'],
            ['min' =>161000.0, 'max' => 164500.0, 'tipo' => 'L'],
            ['min' =>164500.0, 'max' => 168000.0, 'tipo' => 'MP4'],
            ['min' =>168000.0, 'max' => 171500.0, 'tipo' => 'L'],
            ['min' =>171500.0, 'max' => 175000.0, 'tipo' => 'MP3'],
            ['min' =>175000.0, 'max' => 178500.0, 'tipo' => 'L'],
            ['min' =>178500.0, 'max' => 182000.0, 'tipo' => 'MP2'],
            ['min' =>182000.0, 'max' => 185500.0, 'tipo' => 'L'],
            ['min' =>185500.0, 'max' => 189000.0, 'tipo' => 'MP1'],
            ['min' =>189000.0, 'max' => 192500.0, 'tipo' => 'L'],
            ['min' =>192500.0, 'max' => 196000.0, 'tipo' => 'MP2'],
            ['min' =>196000.0, 'max' => 199500.0, 'tipo' => 'L'],
            ['min' =>199500.0, 'max' => 203000.0, 'tipo' => 'MP1'],
            ['min' =>203000.0, 'max' => 206500.0, 'tipo' => 'L'],
            ['min' =>206500.0, 'max' => 210000.0, 'tipo' => 'MP5'],
            ['min' =>210000.0, 'max' => 213500.0, 'tipo' => 'L'],
            ['min' =>213500.0, 'max' => 217000.0, 'tipo' => 'MP1'],
            ['min' =>217000.0, 'max' => 220500.0, 'tipo' => 'L'],
            ['min' =>220500.0, 'max' => 224000.0, 'tipo' => 'MP4'],
            ['min' =>224000.0, 'max' => 227500.0, 'tipo' => 'L'],
            ['min' =>227500.0, 'max' => 231000.0, 'tipo' => 'MP1'],
            ['min' =>231000.0, 'max' => 234500.0, 'tipo' => 'L'],
            ['min' =>234500.0, 'max' => 238000.0, 'tipo' => 'MP2'],
            ['min' =>238000.0, 'max' => 241500.0, 'tipo' => 'L'],
            ['min' =>241500.0, 'max' => 245000.0, 'tipo' => 'MP3'],
            ['min' =>245000.0, 'max' => 248500.0, 'tipo' => 'L'],
            ['min' =>248500.0, 'max' => 252000.0, 'tipo' => 'MP2'],
            ['min' =>252000.0, 'max' => 255500.0, 'tipo' => 'L'],
            ['min' =>255500.0, 'max' => 259000.0, 'tipo' => 'MP1'],
        ];
    }

    /**
     * Calcula el tipo de mantenimiento según el km_actual.
     */
    public static function calcularTipoPorKm(float $kmActual): string
    {
        foreach (static::rangosTipo() as $rango) {
            if ($kmActual >= $rango['min'] && $kmActual < $rango['max']) {
                return $rango['tipo'];
            }
        }
        return 'Desconocido';
    }

   

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }
}
