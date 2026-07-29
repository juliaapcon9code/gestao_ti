<?php
/**
 * Input Validator
 * 
 * Centraliza validação e sanitização de inputs seguindo OWASP guidelines.
 * Reduz vulnerabilidades de SQL Injection, XSS e outras injeções.
 * 
 * @package TechManager
 * @subpackage Security
 */

class InputValidator {
    
    // Tipos de validação
    private static $rules = [];
    private static $errors = [];
    
    /**
     * Valida se um valor é email válido
     * 
     * @param string $email
     * @return bool
     */
    public static function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Valida se uma senha atende critérios mínimos
     * 
     * @param string $password
     * @return array Erros se houver, array vazio se válida
     */
    public static function validatePassword($password) {
        $errors = [];
        
        if (strlen($password) < 8) {
            $errors[] = 'Senha deve ter no mínimo 8 caracteres';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Senha deve conter pelo menos 1 número';
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Senha deve conter letras minúsculas';
        }
        
        return $errors;
    }
    
    /**
     * Sanitiza string para HTML (remove tags, previne XSS)
     * 
     * @param string $input
     * @return string
     */
    public static function sanitizeString($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Sanitiza input numérico
     * 
     * @param mixed $input
     * @return int|float|null
     */
    public static function sanitizeNumber($input) {
        $sanitized = filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, 
            FILTER_FLAG_ALLOW_FRACTION);
        return $sanitized !== false ? $sanitized : null;
    }
    
    /**
     * Valida se um valor está entre opções permitidas
     * 
     * @param mixed $value
     * @param array $allowedValues
     * @return bool
     */
    public static function isInAllowedValues($value, array $allowedValues) {
        return in_array($value, $allowedValues, true);
    }
    
    /**
     * Valida um inteiro
     * 
     * @param mixed $value
     * @param int|null $min
     * @param int|null $max
     * @return bool
     */
    public static function isValidInteger($value, $min = null, $max = null) {
        $int = filter_var($value, FILTER_VALIDATE_INT);
        
        if ($int === false) {
            return false;
        }
        
        if ($min !== null && $int < $min) {
            return false;
        }
        
        if ($max !== null && $int > $max) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Valida formato de data (YYYY-MM-DD)
     * 
     * @param string $date
     * @return bool
     */
    public static function isValidDate($date) {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return false;
        }
        
        list($year, $month, $day) = explode('-', $date);
        return checkdate((int)$month, (int)$day, (int)$year);
    }
    
    /**
     * Valida tempo no formato HH:MM:SS ou HH:MM
     * 
     * @param string $time
     * @return bool
     */
    public static function isValidTime($time) {
        return preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $time) === 1;
    }
    
    /**
     * Valida formato de CNPJ (básico)
     * 
     * @param string $cnpj
     * @return bool
     */
    public static function isValidCNPJ($cnpj) {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        return strlen($cnpj) === 14;
    }
    
    /**
     * Valida formato de CPF (básico)
     * 
     * @param string $cpf
     * @return bool
     */
    public static function isValidCPF($cpf) {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        return strlen($cpf) === 11 && $cpf !== str_repeat($cpf[0], 11);
    }
    
    /**
     * Valida comprimento de string
     * 
     * @param string $str
     * @param int $min
     * @param int|null $max
     * @return bool
     */
    public static function isValidLength($str, $min, $max = null) {
        $len = strlen($str);
        
        if ($len < $min) {
            return false;
        }
        
        if ($max !== null && $len > $max) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Coleção de validações com regras
     * 
     * Exemplo:
     * ```php
     * InputValidator::validate([
     *     'email' => ['required', 'email'],
     *     'password' => ['required', 'min:8'],
     *     'role' => ['required', 'in:admin,supervisor,user']
     * ], $_POST);
     * ```
     * 
     * @param array $rules Regras de validação
     * @param array $data Dados a validar
     * @return bool true se válido
     */
    public static function validate(array $rules, array $data) {
        self::$errors = [];
        
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? '';
            
            foreach ((array)$fieldRules as $rule) {
                self::applyRule($field, $value, $rule);
            }
        }
        
        return empty(self::$errors);
    }
    
    /**
     * Aplica uma regra de validação individual
     * 
     * @param string $field
     * @param mixed $value
     * @param string $rule
     */
    private static function applyRule($field, $value, $rule) {
        if (strpos($rule, ':') !== false) {
            list($ruleName, $ruleValue) = explode(':', $rule, 2);
        } else {
            $ruleName = $rule;
            $ruleValue = null;
        }
        
        switch ($ruleName) {
            case 'required':
                if (empty($value)) {
                    self::$errors[$field] = "O campo {$field} é obrigatório";
                }
                break;
            
            case 'email':
                if (!empty($value) && !self::isValidEmail($value)) {
                    self::$errors[$field] = "O email deve ser válido";
                }
                break;
            
            case 'min':
                if (!empty($value) && strlen($value) < (int)$ruleValue) {
                    self::$errors[$field] = "Mínimo {$ruleValue} caracteres";
                }
                break;
            
            case 'max':
                if (!empty($value) && strlen($value) > (int)$ruleValue) {
                    self::$errors[$field] = "Máximo {$ruleValue} caracteres";
                }
                break;
            
            case 'in':
                $allowed = explode(',', $ruleValue);
                if (!empty($value) && !self::isInAllowedValues($value, $allowed)) {
                    self::$errors[$field] = "Valor inválido para {$field}";
                }
                break;
        }
    }
    
    /**
     * Obtém os erros de validação
     * 
     * @return array
     */
    public static function getErrors() {
        return self::$errors;
    }
    
    /**
     * Obtém um erro específico
     * 
     * @param string $field
     * @return string|null
     */
    public static function getError($field) {
        return self::$errors[$field] ?? null;
    }
    
    /**
     * Verifica se há erros
     * 
     * @return bool
     */
    public static function hasErrors() {
        return !empty(self::$errors);
    }
}
?>
