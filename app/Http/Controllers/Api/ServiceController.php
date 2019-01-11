<?php
namespace App\Http\Controllers\Api;

use App\Core\Services\Reniec\Dni;
use App\Core\Services\Sunat\Ruc;
use App\Http\Controllers\Controller;
use App\Models\Location\Department;
use App\Models\Location\District;
use App\Models\Location\Province;

class ServiceController extends Controller
{
    public function ruc($number)
    {
        $service = new Ruc();
        $res = $service->get($number);
        if ($res) {
            return [
                'success' => true,
                'data' => [
                    'name' => $res->razonSocial,
                    'trade_name' => $res->nombreComercial,
                    'address' => $res->direccion,
                    'phone' => implode(' / ', $res->telefonos),
                    'department' => ($res->departamento)?:'LIMA',
                    'department_id' => Department::idByDescription($res->departamento),
                    'province' => ($res->provincia)?:'LIMA',
                    'province_id' => Province::idByDescription($res->provincia),
                    'district' => ($res->distrito)?:'LIMA',
                    'district_id' => District::idByDescription($res->distrito),
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => $service->getError()
            ];
        }
    }

    public function dni($number)
    {
        $service = new Dni();
        $res = $service->get($number);
        if ($res) {
            return [
                'success' => true,
                'data' => [
                    'number' => $res->dni,
                    'names' => $res->nombres,
                    'first_name' => $res->apellidoPaterno,
                    'last_name' => $res->apellidoMaterno,
                    'name' => $res->apellidoPaterno.' '.$res->apellidoMaterno.', '.$res->nombres,
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => $service->getError()
            ];
        }
    }
}