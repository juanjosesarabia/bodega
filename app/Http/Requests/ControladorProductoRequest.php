<?php

namespace App\Http\Requests;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Foundation\Http\FormRequest;


class ControladorProductoRequest extends FormRequest
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
            'descripcion' => 'descripcion',
            'codigoBarra' => 'codigoBarra',
            'id_vendedor' => 'id_vendedor',
            'riesgo'=>'riesgo'     
        ];
    }
    
    
    
        public function messages()
        {
            return [
                'nombre.required' => 'El nombre es obligatorio.',
                'nombre.string' => 'El nombre debe ser una cadena de datos',
                'descripcion.required' => 'La descripcion es obligatoria.',
                'descripcion.string' => 'La descripcion  debe ser cadena de datos',
                'codigoBarra.required' => 'El codigo de barra es obligatorio.',
                'codigoBarra.numeric' => 'El codigo de barra  debe númerico',
                'id_vendedor.required' => 'El id del vendedor es obligatorio.',
                'id_vendedor.numeric' => 'El id del vendedor debe ser númerico',
                'riesgo.required' => 'El riesgo es obligatorio.',
                'riesgo.string' => 'El riesgo debe ser una cadena de datos',
                'cantidadUnitaria.required'=>'La cantidad unitaria es obligatorio',
                'cantidadUnitaria.numeric'=>'La cantidad unitaria debe ser númerico'
            ];
        }
    public function rules()
    {
        return [            
            'nombre' => 'required|string',            
            'descripcion' => 'required|string',
            'codigoBarra' => 'required|numeric',
            'id_vendedor' => 'required|numeric',
            'riesgo'=>'required|string',
            'cantidadUnitaria'=>'required|numeric'             
        ];
    }
    

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(response()->json(["estado"=>"error",'mensaje' => $errors
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}
