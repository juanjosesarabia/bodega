<?php

namespace App\Http\Requests;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Foundation\Http\FormRequest;

class ControladorIngresoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [           
            'nombre' => 'nombre',            
            'nombreRecibe' => 'Nombre de quien Recibe',
            'fechaIngreso' => 'fecha de Ingreso',
            'cantidadIngresada' => 'cantidadIngresada',
            'ubicacionOperativo'=>'ubicacionOperativo'     
        ];
    }
    
    
    
        public function messages()
        {
            return [
                'cedulaNombreRecibe.required' => 'La cédula  es obligatoria.',
                'cedulaNombreRecibe.numeric' => 'La cédula debe ser numérica',
                'nombreRecibe.required' => 'El nombre de quien recibe es obligatorio.',
                'nombreRecibe.string' => 'La nombre de quien recibe debe ser cadena de datos',
                'fechaIngreso.required' => 'La fecha de Ingreso es obligatoria.',
                'fechaIngreso.date' => 'La fecha de Ingreso debe tener formato AAAA-MM-DD',
                'cantidadIngresada.required' => 'La cantidad de articulos es obligatoria.',
                'cantidadIngresada.numeric' => 'La cantidad de articulos debe ser numerica',
                'ubicacionOperativo.required' => 'La ubicación del operativo debe ser obligatoria.',
                'ubicacionOperativo.string' => 'La ubicación del operativo debe ser una cadena de datos',

            ];
        }
    public function rules()
    {
        return [            
            'cedulaNombreRecibe' => 'required|numeric',            
            'nombreRecibe' => 'required|string',
            'fechaIngreso' => 'required|date',
            'numero_acta' => 'required|numeric',
            'cantidadIngresada'=>'required|numeric',
            'ubicacionOperativo'=>'required|string'           
        ];
    }
    

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(response()->json(["estado"=>"error",'mensaje' => $errors
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}
