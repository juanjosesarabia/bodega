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
            'numero_acta' =>'Número de acta',
            'fechaIngreso' => 'fecha de Ingreso',
            'ubicacionOperativo'=>'ubicacionOperativo'     
        ];
    }
    
    
    
        public function messages()
        {
            return [
                
                'fechaIngreso.required' => 'La fecha de Ingreso es obligatoria.',
                'fechaIngreso.date' => 'La fecha de Ingreso debe tener formato AAAA-MM-DD',
                'numero_acta.required' => 'El número de acta  es obligatorio.',
                'numero_acta.numeric' => 'El número acta debe ser entero ',
                'ubicacionOperativo.required' => 'La ubicación del operativo debe ser obligatoria.',
                'ubicacionOperativo.string' => 'La ubicación del operativo debe ser una cadena de datos',

            ];
        }
    public function rules()
    {
        return [            
            'fechaIngreso' => 'required|date',
            'numero_acta' => 'required|numeric',
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
