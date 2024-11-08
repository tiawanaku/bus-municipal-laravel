<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Parada;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class ParadaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        $paradas = [
            ['id_paradas' => 1, 'nombre_parada' => 'CEIBO Ceja', 'sentido' => 'Vuelta', 'lat_long' => ['lat' => -16.5030954189945, 'lng' => -68.1637698034521], 'id_ruta' => null],
            ['id_paradas' => 2, 'nombre_parada' => 'EX TRANSITO', 'sentido' => 'Ida', 'lat_long' => ['lat' => -16.5025642602212, 'lng' => -68.1657770309667], 'id_ruta' => null],
            ['id_paradas' => 3, 'nombre_parada' => 'CRUZ PAPAL', 'sentido' => 'Vuelta', 'lat_long' => ['lat' => -16.5014538555029, 'lng' => -68.1684752095178], 'id_ruta' => null],
            ['id_paradas' => 4, 'nombre_parada' => 'Cruz Papal', 'sentido' => 'Ida', 'lat_long' => ['lat' => -16.5018781949364, 'lng' => -68.1683127398256], 'id_ruta' => null],
            ['id_paradas' => 5, 'nombre_parada' => 'Av. La Paz (Estacion Teleferico Azul Plaza La Paz)', 'sentido' => 'Vuelta', 'lat_long' => ['lat' => -16.4972208268171, 'lng' => -68.1841657946729], 'id_ruta' => null],
            ['id_paradas' => 6, 'nombre_parada' => 'Av. La Paz (Estacion Teleferico Azul Plaza La Paz)', 'sentido' => 'Ida', 'lat_long' => ['lat' => -16.4974993996736, 'lng' => -68.1845042760555], 'id_ruta' => null],
            ['id_paradas' => 7, 'nombre_parada' => 'UPEA', 'sentido' => 'Vuelta', 'lat_long' => ['lat' => -16.4935677892554, 'lng' => -68.1953448439776], 'id_ruta' => null],
            ['id_paradas' => 8, 'nombre_parada' => 'UPEA', 'sentido' => 'Ida', 'lat_long' => ['lat' => -16.4945189770596, 'lng' => -68.1942930611935], 'id_ruta' => null],
            ['id_paradas' => 9, 'nombre_parada' => 'Planta de Luz Rio Seco', 'sentido' => 'Vuelta', 'lat_long' => ['lat' => -16.4857055218524, 'lng' => -68.2069656893377], 'id_ruta' => null],
            ['id_paradas' => 10, 'nombre_parada' => 'Planta de Luz Rio Seco', 'sentido' => 'Ida', 'lat_long' => ['lat' => -16.4859194296456, 'lng' => -68.2069655335793], 'id_ruta' => null],
            ['id_paradas' => 11, 'nombre_parada' => 'Ex Parada 8', 'sentido' => 'Vuelta', 'lat_long' => ['lat' => -16,4854475505789, 'lng' => -68.2286266371315] , 'id_ruta' => null],
            ['id_paradas' => 12, 'nombre_parada' => 'Ex Parada 8', 'sentido' => 'Ida', 'lat_long' => ['lat' => -16.4857531636886, 'lng' => -68.2285557561655], 'id_ruta' => null],
            ['id_paradas' => 13, 'nombre_parada' => 'Cruce Lagunas', 'sentido' => 'Vuelta', 'lat_long' => ['lat' => -16.4808191864884, 'lng' => -68.248212470592], 'id_ruta' => null],
            ['id_paradas' => 14, 'nombre_parada' => 'Cruce Lagunas', 'sentido' => 'Ida', 'lat_long' => ['lat' => -16.4812902078119, 'lng' => -68.2482450917833], 'id_ruta' => null],
            ['id_paradas' => 15, 'nombre_parada' => 'Cruce Bella Vista', 'sentido' => 'Vuelta', 'lat_long' => ['lat' => -16.4786220730462, 'lng' => -68.2586853678683], 'id_ruta' => null],
            ['id_paradas' => 16, 'nombre_parada' => 'Cruce Bella Vista', 'sentido' => 'Ida', 'lat_long' => ['lat' => -16.4788519784453, 'lng' => -68.2596744019985], 'id_ruta' => null],
            ['id_paradas' => 17, 'nombre_parada' => 'San Roque', 'sentido' => 'Vuelta', 'lat_long' => ['lat' => -16.4759784093592, 'lng' => -68.2719042715678], 'id_ruta' => null],
            ['id_paradas' => 18, 'nombre_parada' => 'San Roque', 'sentido' => 'Ida', 'lat_long' => ['lat' => -16.4763570344837, 'lng' => -68.2720747609313], 'id_ruta' => null],
            ['id_paradas' => 19, 'nombre_parada' => 'Playa Verde', 'sentido' => 'Vuelta', 'lat_long' => ['lat' => -16.472926800867, 'lng' => -68.2851439299171], 'id_ruta' => null],
            ['id_paradas' => 20, 'nombre_parada' => 'Playa Verde', 'sentido' => 'Ida', 'lat_long' => ['lat' => -16.4730157243316, 'lng' => -68.2861322609836], 'id_ruta' => null],


            ['id_paradas' => 21, 'nombre_parada' => 'CALLE 3', 'sentido' => 'Vuelta', 'lat_long' => ['lat' => -16.5089254817242, 'lng' => -68.164527062943], 'id_ruta' => null],
            ['id_paradas' => 22, 'nombre_parada' => 'CALLE 7', 'sentido' => 'Vuelta', 'lat_long' => ['lat' => -16.5125534317669, 'lng' => -68.1657850313944], 'id_ruta' => null],
            ['id_paradas' => 23, 'nombre_parada' => 'CALLE 7', 'sentido' => 'Ida', 'lat_long' => ['lat' => -16.511712223241, 'lng' => -68.1657027441876], 'id_ruta' => null],
            ['id_paradas' => 24, 'nombre_parada' => 'CRUCE VIACHA', 'sentido' => 'Vuelta', 'lat_long' => ['lat' => -16.5168279012498, 'lng' => -68.1672877882178], 'id_ruta' => null],
            ['id_paradas' => 25, 'nombre_parada' => 'CRUCE VIACHA', 'sentido' => 'Ida', 'lat_long' => ['lat' => -16.5166802547916, 'lng' => -68.1668666352884], 'id_ruta' => null],
            ['id_paradas' => 26, 'nombre_parada' => 'ESTACION TELEFERICO MORADO', 'sentido' => 'Vuelta', 'lat_long' => ['lat' => -16.5225241194252, 'lng' => -68.1690922386302], 'id_ruta' => null],
            ['id_paradas' => 27, 'nombre_parada' => 'ESTACION TELEFERICO MORADO', 'sentido' => 'Ida', 'lat_long' => ['lat' => -16.5231869532795, 'lng' => -68.169017085064], 'id_ruta' => null],
            ['id_paradas' => 28, 'nombre_parada' => 'PUENTE BOLIVIA', 'sentido' => 'Vuelta', 'lat_long' => ['lat' => -16.5440532162665, 'lng' => -68.1765295572606], 'id_ruta' => null],
            ['id_paradas' => 29, 'nombre_parada' => 'PUENTE BOLIVIA', 'sentido' => 'Ida', 'lat_long' => ['lat' => -16.5434566980732, 'lng' => -68.1759275787868], 'id_ruta' => null],
            ['id_paradas' => 30, 'nombre_parada' => 'MOLINO ANDINO', 'sentido' => 'Vuelta', 'lat_long' => ['lat' => -16.5558473651563, 'lng' => -68.1805298799354], 'id_ruta' => null],
            ['id_paradas' => 31, 'nombre_parada' => 'MOLINO ANDINO', 'sentido' => 'Ida', 'lat_long' => ['lat' => -16.5531665463413, 'lng' => -68.1790469849795], 'id_ruta' => null],
            ['id_paradas' => 32, 'nombre_parada' => 'RIELES SENKATA', 'sentido' => 'Vuelta', 'lat_long' => ['lat' => -16.566496911327, 'lng' => -68.185624777119], 'id_ruta' => null],
            ['id_paradas' => 33, 'nombre_parada' => 'ESCUELA JOSE MANUEL PANDO', 'sentido' => 'Ida', 'lat_long' => ['lat' => -16.5657084794713, 'lng' => -68.184061631776], 'id_ruta' => null],
            ['id_paradas' => 34, 'nombre_parada' => 'CRUCE SENKATA', 'sentido' => 'Vuelta', 'lat_long' => ['lat' => -16.5828940733829, 'lng' => -68.1856737759332], 'id_ruta' => null],
            ['id_paradas' => 35, 'nombre_parada' => 'CRUCE SENKATA', 'sentido' => 'Ida', 'lat_long' => ['lat' => -16.5811561036719, 'lng' => -68.1813336383866], 'id_ruta' => null],
            ['id_paradas' => 36, 'nombre_parada' => 'PUENTE VELA', 'sentido' => 'Vuelta', 'lat_long' => ['lat' => -16.5990965729138, 'lng' => -68.1843730570688], 'id_ruta' => null],
            ['id_paradas' => 37, 'nombre_parada' => 'PUENTE VELA', 'sentido' => 'Ida', 'lat_long' => ['lat' => -16.5993265703528, 'lng' => -68.1837244670003], 'id_ruta' => null],
            ['id_paradas' => 38, 'nombre_parada' => 'CRUCE VENTILLA', 'sentido' => 'Vuelta', 'lat_long' => ['lat' => -16.6188155994979, 'lng' => -68.1829624431524], 'id_ruta' => null],
            ['id_paradas' => 39, 'nombre_parada' => 'CRUCE VENTILLA', 'sentido' => 'Ida', 'lat_long' => ['lat' => -16.6182264362554, 'lng' => -68.182276164891], 'id_ruta' => null],
            ['id_paradas' => 40, 'nombre_parada' => 'CRUCE LAYURI', 'sentido' => 'Vuelta', 'lat_long' => ['lat' => -16.6327635304107, 'lng' => -68.1817026180065], 'id_ruta' => null],
            ['id_paradas' => 41, 'nombre_parada' => 'CRUCE LAYURI', 'sentido' => 'Ida', 'lat_long' =>['lat' => -16.6327504129671, 'lng' => -68.1813903041594] , 'id_ruta' => null],
            ['id_paradas' => 42, 'nombre_parada' => 'SAMO', 'sentido' => 'Ida', 'lat_long' => ['lat' => -16.6480450566707, 'lng' => -68.1799662570455], 'id_ruta' => null],























        ];

        foreach ($paradas as $parada) {
            DB::table('paradas')->insert([
                'id_paradas' => $parada['id_paradas'],
                'nombre_parada' => $parada['nombre_parada'],
                'sentido' => $parada['sentido'],
                'lat_long' => json_encode($parada['lat_long']),
                'id_ruta' => $parada['id_ruta'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}