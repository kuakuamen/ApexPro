<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Cpf implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Extrai apenas os números
        $cpf = preg_replace('/[^0-9]/', '', $value);

        // Verifica se foi informado
        if (strlen($cpf) != 11) {
            $fail('O campo :attribute deve ter 11 dígitos.');
            return;
        }

        // Verifica se foi informada uma sequência de dígitos repetidos. Ex: 111.111.111-11
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            $fail('O campo :attribute não pode conter todos os dígitos iguais.');
            return;
        }

        // Faz o calculo para validar o CPF
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                $fail('O campo :attribute não é um CPF válido.');
                return;
            }
        }
    }
}
