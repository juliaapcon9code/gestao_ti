<?php
/**
 * Helper Functions
 * 
 * Funções utilitárias para formatação e manipulação de dados.
 */

/**
 * Formata valor monetário para Real
 * 
 * @param float $value
 * @return string Valor formatado
 */
function formatMoney($value) {
    return 'R$ ' . number_format((float)$value, 2, ',', '.');
}

/**
 * Formata data do banco (YYYY-MM-DD) para português
 * 
 * @param string $date Formato: YYYY-MM-DD
 * @param bool $includeTime Incluir hora se disponível
 * @return string Data formatada
 */
function formatDate($date, $includeTime = false) {
    if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
        return '-';
    }
    
    $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $date);
    if (!$dateTime) {
        $dateTime = DateTime::createFromFormat('Y-m-d', $date);
    }
    
    if (!$dateTime) {
        return '-';
    }
    
    return $includeTime ? 
        $dateTime->format('d/m/Y H:i') : 
        $dateTime->format('d/m/Y');
}

/**
 * Formata hora para exibição
 * 
 * @param string $time Formato: HH:MM:SS
 * @return string Hora formatada
 */
function formatTime($time) {
    if (empty($time)) {
        return '-';
    }
    
    return substr($time, 0, 5); // HH:MM
}

/**
 * Calcula diferença em dias entre duas datas
 * 
 * @param string $startDate Data inicial (YYYY-MM-DD)
 * @param string $endDate Data final (YYYY-MM-DD ou null para hoje)
 * @return int Diferença em dias
 */
function daysDiff($startDate, $endDate = null) {
    $endDate = $endDate ?? date('Y-m-d');
    
    $start = DateTime::createFromFormat('Y-m-d', $startDate);
    $end = DateTime::createFromFormat('Y-m-d', $endDate);
    
    if (!$start || !$end) {
        return 0;
    }
    
    return $end->diff($start)->days;
}

/**
 * Converte status para badge HTML
 * 
 * @param string $status Status do registro
 * @param string $type Tipo: 'default', 'ticket', 'payment', 'user'
 * @return string HTML da badge
 */
function statusBadge($status, $type = 'default') {
    $statusMap = [
        'default' => [
            'ativo' => ['bg' => 'rgba(79,125,255,0.12)', 'color' => '#4f7dff', 'text' => 'Ativo'],
            'inativo' => ['bg' => 'rgba(239, 68, 68, 0.1)', 'color' => '#ef4444', 'text' => 'Inativo'],
        ],
        'ticket' => [
            'aberto' => ['bg' => 'rgba(59, 130, 246, 0.1)', 'color' => '#3b82f6', 'text' => 'Aberto'],
            'andamento' => ['bg' => 'rgba(251, 191, 36, 0.1)', 'color' => '#fbbf24', 'text' => 'Em Andamento'],
            'revisao' => ['bg' => 'rgba(168, 85, 247, 0.1)', 'color' => '#a855f7', 'text' => 'Revisão'],
            'finalizado' => ['bg' => 'rgba(79,125,255,0.12)', 'color' => '#4f7dff', 'text' => 'Finalizado'],
        ],
        'payment' => [
            'pago' => ['bg' => 'rgba(79,125,255,0.12)', 'color' => '#4f7dff', 'text' => 'Pago'],
            'pendente' => ['bg' => 'rgba(251, 191, 36, 0.1)', 'color' => '#fbbf24', 'text' => 'Pendente'],
            'cancelado' => ['bg' => 'rgba(239, 68, 68, 0.1)', 'color' => '#ef4444', 'text' => 'Cancelado'],
        ],
    ];
    
    $config = $statusMap[$type][$status] ?? $statusMap['default'][$status] ?? null;
    
    if (!$config) {
        return '<span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; background: rgba(255,255,255,0.05); color: #94a3b8;">' . 
               htmlspecialchars(ucfirst($status)) . '</span>';
    }
    
    return '<span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; background: ' . $config['bg'] . 
           '; color: ' . $config['color'] . ';">' . $config['text'] . '</span>';
}

/**
 * Trunca texto com elipsis
 * 
 * @param string $text
 * @param int $length
 * @param string $suffix
 * @return string
 */
function truncate($text, $length = 50, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    return substr($text, 0, $length) . $suffix;
}

/**
 * Mapeia valor booleano para português
 * 
 * @param bool $value
 * @return string
 */
function boolToString($value) {
    return $value ? 'Sim' : 'Não';
}

/**
 * Calcula idade em anos
 * 
 * @param string $birthDate Data de nascimento (YYYY-MM-DD)
 * @return int Idade
 */
function calculateAge($birthDate) {
    $today = new DateTime('today');
    $birth = DateTime::createFromFormat('Y-m-d', $birthDate);
    
    if (!$birth) {
        return 0;
    }
    
    return $today->diff($birth)->y;
}

/**
 * Gera slug a partir de texto
 * 
 * @param string $text
 * @return string
 */
function slugify($text) {
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = preg_replace('/[^a-z0-9-]/', '-', strtolower($text));
    $text = preg_replace('/-+/', '-', $text);
    return trim($text, '-');
}

/**
 * Detecta browser do usuário
 * 
 * @return string Nome do browser
 */
function detectBrowser() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    if (strpos($userAgent, 'Firefox') !== false) return 'Firefox';
    if (strpos($userAgent, 'Chrome') !== false) return 'Chrome';
    if (strpos($userAgent, 'Safari') !== false) return 'Safari';
    if (strpos($userAgent, 'Opera') !== false) return 'Opera';
    if (strpos($userAgent, 'Edge') !== false) return 'Edge';
    
    return 'Unknown';
}

/**
 * Obtém IP do cliente considerando proxies
 * 
 * @return string IP
 */
function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    }
    
    // Validar IP
    if (filter_var($ip, FILTER_VALIDATE_IP)) {
        return $ip;
    }
    
    return 'UNKNOWN';
}

/**
 * Calcula tempo desde uma data (ex: "há 2 horas")
 * 
 * @param string $date Data (YYYY-MM-DD HH:MM:SS)
 * @param string $lang Idioma: 'pt' ou 'en'
 * @return string
 */
function timeAgo($date, $lang = 'pt') {
    $timestamp = strtotime($date);
    $seconds = time() - $timestamp;
    
    $periods = $lang === 'pt' ? [
        'semana' => 604800,
        'dia' => 86400,
        'hora' => 3600,
        'minuto' => 60,
        'segundo' => 1
    ] : [
        'week' => 604800,
        'day' => 86400,
        'hour' => 3600,
        'minute' => 60,
        'second' => 1
    ];
    
    foreach ($periods as $period => $value) {
        if ($seconds >= $value) {
            $time = floor($seconds / $value);
            $label = $time > 1 ? ($lang === 'pt' ? 's' : '') : '';
            return $lang === 'pt' ? 
                "há $time " . $period . ($time > 1 ? 's' : '') :
                "$time " . $period . ($time > 1 ? 's' : '') . " ago";
        }
    }
    
    return $lang === 'pt' ? 'Agora' : 'Now';
}
?>
