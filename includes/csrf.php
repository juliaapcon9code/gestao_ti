<?php
/**
 * CSRF Token Manager
 * 
 * Gerencia tokens CSRF para proteção contra ataques cross-site request forgery.
 * Implementa best practices de segurança conforme OWASP.
 * 
 * @package TechManager
 * @subpackage Security
 */

class CSRFToken {
    private const TOKEN_SESSION_KEY = '_csrf_token';
    private const TOKEN_EXPIRY = 3600; // 1 hora
    
    /**
     * Inicia a sessão se não estiver ativa
     */
    private static function ensureSession() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }
    
    /**
     * Gera um novo token CSRF
     * 
     * @return string Token CSRF seguro
     */
    public static function generate() {
        self::ensureSession();
        
        // Verifica se token existe e ainda é válido
        if (isset($_SESSION[self::TOKEN_SESSION_KEY])) {
            $data = $_SESSION[self::TOKEN_SESSION_KEY];
            if (time() - $data['created_at'] < self::TOKEN_EXPIRY) {
                return $data['token'];
            }
        }
        
        // Gera novo token
        $token = bin2hex(random_bytes(32));
        $_SESSION[self::TOKEN_SESSION_KEY] = [
            'token' => $token,
            'created_at' => time()
        ];
        
        return $token;
    }
    
    /**
     * Valida um token CSRF
     * 
     * @param string $token Token a validar
     * @return bool true se válido, false caso contrário
     */
    public static function validate($token) {
        self::ensureSession();
        
        if (!isset($_SESSION[self::TOKEN_SESSION_KEY])) {
            return false;
        }
        
        $data = $_SESSION[self::TOKEN_SESSION_KEY];
        
        // Verifica expiração
        if (time() - $data['created_at'] > self::TOKEN_EXPIRY) {
            return false;
        }
        
        // Comparação segura contra timing attacks
        return hash_equals($data['token'], $token);
    }
    
    /**
     * Obtém o token CSRF atual
     * 
     * @return string|null
     */
    public static function getToken() {
        self::ensureSession();
        return $_SESSION[self::TOKEN_SESSION_KEY]['token'] ?? null;
    }
    
    /**
     * Invalida o token (força geração de novo)
     */
    public static function invalidate() {
        self::ensureSession();
        unset($_SESSION[self::TOKEN_SESSION_KEY]);
    }
}
?>
