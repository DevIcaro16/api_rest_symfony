<?php

namespace App\DTO;

class CarDTO extends AbstractDTO implements DTOInterface
{
    public function __construct(
        public $brand,
        public $model,
        public $manufacture_year,
        public $model_year,
        public $km,
    ) {
    }

    public function rules(?array $onlyFields = null): array
    {
        $allRules = [
            'brand' => [
                'required' => true,
                'type' => 'string',
                'message' => 'A marca é obrigatória'
            ],
            'model' => [
                'required' => true,
                'type' => 'string',
                'message' => 'O modelo é obrigatório'
            ],
            'manufacture_year' => [
                'required' => true,
                'type' => 'integer',
                'message' => 'O ano de fabricação deve ser um número inteiro válido'
            ],
            'model_year' => [
                'required' => true,
                'type' => 'integer',
                'message' => 'O ano do modelo deve ser um número inteiro válido'
            ],
            'km' => [
                'required' => true,
                'type' => 'float',
                'message' => 'A quilometragem deve ser um número válido'
            ]
        ];

        if ($onlyFields !== null) {
            $filteredRules = [];
            foreach ($onlyFields as $field => $value) {
                if (isset($allRules[$field])) {
                    $filteredRules[$field] = $allRules[$field];
                }
            }
            return $filteredRules;
        }

        return $allRules;
    }

    public function validate(?array $onlyFields = null): array
    {
        $errors = [];
        $data = $onlyFields !== null ? $onlyFields : $this->all();
        $rules = $this->rules($onlyFields);

        foreach ($rules as $field => $rule) {
            // Verifica se o campo é obrigatório
            if (isset($rule['required']) && $rule['required']) {
                if (empty($data[$field])) {
                    $errors[$field] = $rule['message'] ?? "O campo {$field} é obrigatório";
                    continue;
                }
            }

            // Verifica o tipo do campo
            if (isset($rule['type'])) {
                $value = $data[$field];
                $isValidType = false;

                switch ($rule['type']) {
                    case 'string':
                        $isValidType = is_string($value) && !empty(trim($value));
                        break;
                    case 'integer':
                        if (strlen((string) $value) !== 4) {
                            $errors[$field] = "O campo {$field} deve ter 4 dígitos!";
                            continue;
                        }
                        $isValidType = is_int($value) && (int) $value > 0;
                        break;
                    case 'float':
                        $isValidType = is_float($value) && (float) $value >= 0;
                        break;
                }

                if (!$isValidType) {
                    $errors[$field] = $rule['message'] ?? "O campo {$field} deve ser do tipo {$rule['type']}";
                }
            }
        }

        return $errors;
    }

    /**
     * Valida apenas campos específicos (útil para PATCH)
     */
    public function validatePartial(array $fields): array
    {
        return $this->validate($fields);
    }

    /**
     * Valida todos os campos (útil para POST/PUT)
     */
    public function validateAll(): array
    {
        return $this->validate();
    }

    public function isValid(): bool
    {
        return empty($this->validate());
    }

    /**
     * Verifica se é válido para atualização parcial
     */
    public function isValidForPartialUpdate(array $fields): bool
    {
        return empty($this->validatePartial($fields));
    }
}

?>