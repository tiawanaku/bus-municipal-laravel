<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RangoMantenimientoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['km_min' => 0.0, 'km_max' => 3500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 3500.0, 'km_max' => 7000.0, 'tipo_mantenimiento' => 'MP1'],
            ['km_min' => 7000.0, 'km_max' => 10500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 10500.0, 'km_max' => 14000.0, 'tipo_mantenimiento' => 'MP2'],
            ['km_min' => 14000.0, 'km_max' => 17500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 17500.0, 'km_max' => 21000.0, 'tipo_mantenimiento' => 'MP1'],
            ['km_min' => 21000.0, 'km_max' => 24500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 24500.0, 'km_max' => 28000.0, 'tipo_mantenimiento' => 'MP2'],
            ['km_min' => 28000.0, 'km_max' => 31500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 31500.0, 'km_max' => 35000.0, 'tipo_mantenimiento' => 'MP3'],
            ['km_min' => 35000.0, 'km_max' => 38500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 38500.0, 'km_max' => 42000.0, 'tipo_mantenimiento' => 'MP2'],
            ['km_min' => 42000.0, 'km_max' => 45500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 45500.0, 'km_max' => 49000.0, 'tipo_mantenimiento' => 'MP1'],
            ['km_min' => 49000.0, 'km_max' => 52500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 52500.0, 'km_max' => 56000.0, 'tipo_mantenimiento' => 'MP4'],
            ['km_min' => 56000.0, 'km_max' => 59500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 59500.0, 'km_max' => 63000.0, 'tipo_mantenimiento' => 'MP1'],
            ['km_min' => 63000.0, 'km_max' => 66500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 66500.0, 'km_max' => 70000.0, 'tipo_mantenimiento' => 'MP5'],
            ['km_min' => 70000.0, 'km_max' => 73500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 73500.0, 'km_max' => 77000.0, 'tipo_mantenimiento' => 'MP1'],
            ['km_min' => 77000.0, 'km_max' => 80500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 80500.0, 'km_max' => 84000.0, 'tipo_mantenimiento' => 'MP2'],
            ['km_min' => 84000.0, 'km_max' => 87500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 87500.0, 'km_max' => 91000.0, 'tipo_mantenimiento' => 'MP1'],
            ['km_min' => 91000.0, 'km_max' => 94500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 94500.0, 'km_max' => 98000.0, 'tipo_mantenimiento' => 'MP2'],
            ['km_min' => 98000.0, 'km_max' => 101500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 101500.0, 'km_max' => 105000.0, 'tipo_mantenimiento' => 'MP3'],
            ['km_min' => 105000.0, 'km_max' => 108500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 108500.0, 'km_max' => 112000.0, 'tipo_mantenimiento' => 'MP4'],
            ['km_min' => 112000.0, 'km_max' => 115500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 115500.0, 'km_max' => 119000.0, 'tipo_mantenimiento' => 'MP1'],
            ['km_min' => 119000.0, 'km_max' => 122500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 122500.0, 'km_max' => 126000.0, 'tipo_mantenimiento' => 'MP2'],
            ['km_min' => 126000.0, 'km_max' => 129500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 129500.0, 'km_max' => 133000.0, 'tipo_mantenimiento' => 'MP1'],
            ['km_min' => 133000.0, 'km_max' => 136500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 136500.0, 'km_max' => 140000.0, 'tipo_mantenimiento' => 'MP5'],
            ['km_min' => 140000.0, 'km_max' => 143500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 143500.0, 'km_max' => 147000.0, 'tipo_mantenimiento' => 'MP1'],
            ['km_min' => 147000.0, 'km_max' => 150500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 150500.0, 'km_max' => 154000.0, 'tipo_mantenimiento' => 'MP2'],
            ['km_min' => 154000.0, 'km_max' => 157500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 157500.0, 'km_max' => 161000.0, 'tipo_mantenimiento' => 'MP1'],
            ['km_min' => 161000.0, 'km_max' => 164500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 164500.0, 'km_max' => 168000.0, 'tipo_mantenimiento' => 'MP4'],
            ['km_min' => 168000.0, 'km_max' => 171500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 171500.0, 'km_max' => 175000.0, 'tipo_mantenimiento' => 'MP3'],
            ['km_min' => 175000.0, 'km_max' => 178500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 178500.0, 'km_max' => 182000.0, 'tipo_mantenimiento' => 'MP2'],
            ['km_min' => 182000.0, 'km_max' => 185500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 185500.0, 'km_max' => 189000.0, 'tipo_mantenimiento' => 'MP1'],
            ['km_min' => 189000.0, 'km_max' => 192500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 192500.0, 'km_max' => 196000.0, 'tipo_mantenimiento' => 'MP2'],
            ['km_min' => 196000.0, 'km_max' => 199500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 199500.0, 'km_max' => 203000.0, 'tipo_mantenimiento' => 'MP1'],
            ['km_min' => 203000.0, 'km_max' => 206500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 206500.0, 'km_max' => 210000.0, 'tipo_mantenimiento' => 'MP5'],
            ['km_min' => 210000.0, 'km_max' => 213500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 213500.0, 'km_max' => 217000.0, 'tipo_mantenimiento' => 'MP1'],
            ['km_min' => 217000.0, 'km_max' => 220500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 220500.0, 'km_max' => 224000.0, 'tipo_mantenimiento' => 'MP4'],
            ['km_min' => 224000.0, 'km_max' => 227500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 227500.0, 'km_max' => 231000.0, 'tipo_mantenimiento' => 'MP1'],
            ['km_min' => 231000.0, 'km_max' => 234500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 234500.0, 'km_max' => 238000.0, 'tipo_mantenimiento' => 'MP2'],
            ['km_min' => 238000.0, 'km_max' => 241500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 241500.0, 'km_max' => 245000.0, 'tipo_mantenimiento' => 'MP3'],
            ['km_min' => 245000.0, 'km_max' => 248500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 248500.0, 'km_max' => 252000.0, 'tipo_mantenimiento' => 'MP2'],
            ['km_min' => 252000.0, 'km_max' => 255500.0, 'tipo_mantenimiento' => 'L'],
            ['km_min' => 255500.0, 'km_max' => 259000.0, 'tipo_mantenimiento' => 'MP1'],
        ];

        // Insertar los datos en la tabla
        DB::table('rango_mantenimientos')->insert($data);
    }
}
