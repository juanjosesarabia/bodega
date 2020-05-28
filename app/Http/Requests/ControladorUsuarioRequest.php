<?php

namespace App\Http\Requests;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Foundation\Http\FormRequest;

class ControladorUsuarioRequest extends FormRequest
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
            'name' => 'name',            
            'email' => 'email',
            'password'=>'password'     
        ];
    }
    
    
    
        public function messages()
        {
            return [
                'cedula.required' => 'La cédula es obligatorio.',
                'cedula.numeric' => 'La cédula debe ser numérica',
                'name.required' => 'El nombre es obligatorio.',
                'name.string' => 'El nombre  debe ser cadena de datos',              
                'email.required' => 'El correo es obligatorio.',
                'email.regex' => 'No corresponde a un correo válido',
                'password.required' => 'La contrasena es obligatorio.',
                'password.string' => 'No contrasena debe ser una cadena de datos',

            ];
        }
    public function rules()
    {
        return [
            'cedula' => 'required|numeric',            
            'name' => 'required|string',         
            'email' => 'required|regex:/^.+@.+$/i',
            'password'=>'required|string'            
        ];
    }
    

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(response()->json(["estado"=>"error",'mensaje' => $errors
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}



