<?php

namespace App\Http\Requests;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Foundation\Http\FormRequest;

class ControladorVendedorRequest extends FormRequest
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
            'cedula' => 'cedula',            
            'nombres' => 'nombres',
            'apellidos' => 'apellidos',
            'telefono' => 'telefono'     
        ];
    }
    
    
    
        public function messages()
        {
            return [
                'cedula.required' => 'La cédula es obligatorio.',
                'cedula.numeric' => 'La cédula debe ser numérica',
                'nombres.required' => 'El nombre es obligatorio.',
                'nombres.string' => 'El nombre  debe ser cadena de datos',
                'apellidos.required' => 'El apellido es obligatorio.',
                'apellidos.string' => 'El apellido  debe ser cadena de datos',               
                'telefono.numeric' => 'El telefono debe ser numerico '
                
            ];
        }
    public function rules()
    {
        return [
            'cedula' => 'required|numeric',            
            'nombres' => 'required|string',
            'apellidos' => 'required|string',          
            'telefono'=>'numeric'            
        ];
    }
    

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(response()->json(["estado"=>"error",'mensaje' => $errors
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}
