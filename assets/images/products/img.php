<?php
/**
 * TechStore - Servicio de Imágenes Dinámico
 * Archivo: assets/images/products/img.php
 * Uso: <img src="assets/images/products/img.php?f=nombre.jpg&t=laptop&m=HP">
 * Genera un SVG placeholder si el archivo no existe
 */

$archivo = basename($_GET['f'] ?? 'no-image.jpg');
$tipo    = $_GET['t'] ?? 'producto';
$marca   = $_GET['m'] ?? '';
$ruta    = __DIR__ . '/' . $archivo;

// Si el archivo existe, servirlo directamente
if (file_exists($ruta) && is_file($ruta)) {
    $ext  = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
    $mime = match($ext) {
        'png'  => 'image/png',
        'webp' => 'image/webp',
        'gif'  => 'image/gif',
        default => 'image/jpeg',
    };
    header('Content-Type: ' . $mime);
    header('Cache-Control: public, max-age=86400');
    readfile($ruta);
    exit;
}

// Si no existe, generar SVG placeholder
$colores = [
    'laptop'   => ['bg' => '#e8f0fe', 'icon' => '#0d6efd', 'label' => 'Laptop'],
    'gaming'   => ['bg' => '#fce4ec', 'icon' => '#b71c1c', 'label' => 'Gaming'],
    'pc'       => ['bg' => '#e3f2fd', 'icon' => '#1565c0', 'label' => 'PC'],
    'monitor'  => ['bg' => '#e8eaf6', 'icon' => '#283593', 'label' => 'Monitor'],
    'printer'  => ['bg' => '#e0f7fa', 'icon' => '#00697a', 'label' => 'Impresora'],
    'router'   => ['bg' => '#e8f5e9', 'icon' => '#2e7d32', 'label' => 'Red'],
    'keyboard' => ['bg' => '#f3e5f5', 'icon' => '#6a1b9a', 'label' => 'Accesorio'],
    'cpu'      => ['bg' => '#fff3e0', 'icon' => '#e65100', 'label' => 'Componente'],
    'ssd'      => ['bg' => '#f9fbe7', 'icon' => '#558b2f', 'label' => 'Almacenamiento'],
    'headset'  => ['bg' => '#fce4ec', 'icon' => '#880e4f', 'label' => 'Audio'],
    'default'  => ['bg' => '#f8f9fa', 'icon' => '#6c757d', 'label' => 'Producto'],
];

$c     = $colores[$tipo] ?? $colores['default'];
$bg    = $c['bg'];
$color = $c['icon'];
$label = $marca ?: $c['label'];

// Iconos SVG por tipo de producto
$iconosSvg = [
    'laptop'   => '<rect x="70" y="95" width="160" height="105" rx="6" fill="' . $color . '" opacity=".9"/>
                   <rect x="78" y="103" width="144" height="89" rx="3" fill="#1a1a2e"/>
                   <rect x="55" y="200" width="190" height="12" rx="4" fill="' . $color . '"/>',

    'gaming'   => '<rect x="70" y="95" width="160" height="105" rx="6" fill="' . $color . '"/>
                   <rect x="78" y="103" width="144" height="89" rx="3" fill="#0a0a1a"/>
                   <rect x="55" y="200" width="190" height="12" rx="4" fill="' . $color . '"/>
                   <line x1="55" y1="198" x2="245" y2="198" stroke="#ff1744" stroke-width="2"/>',

    'monitor'  => '<rect x="65" y="90" width="170" height="115" rx="8" fill="' . $color . '"/>
                   <rect x="73" y="98" width="154" height="99" rx="3" fill="#0d1117"/>
                   <rect x="130" y="205" width="40" height="18" rx="2" fill="' . $color . '"/>
                   <rect x="110" y="223" width="80" height="8" rx="3" fill="' . $color . '"/>',

    'pc'       => '<rect x="120" y="75" width="60" height="130" rx="8" fill="' . $color . '"/>
                   <circle cx="150" cy="110" r="14" fill="#1a1a2e"/>
                   <rect x="128" y="135" width="44" height="8" rx="3" fill="#1a1a2e" opacity=".7"/>
                   <rect x="128" y="150" width="44" height="8" rx="3" fill="#1a1a2e" opacity=".5"/>',

    'cpu'      => '<rect x="100" y="100" width="100" height="100" rx="6" fill="' . $color . '"/>
                   <rect x="112" y="112" width="76" height="76" rx="3" fill="#e8f0ff"/>
                   <line x1="115" y1="90" x2="115" y2="100" stroke="' . $color . '" stroke-width="4"/>
                   <line x1="135" y1="90" x2="135" y2="100" stroke="' . $color . '" stroke-width="4"/>
                   <line x1="155" y1="90" x2="155" y2="100" stroke="' . $color . '" stroke-width="4"/>
                   <line x1="175" y1="90" x2="175" y2="100" stroke="' . $color . '" stroke-width="4"/>
                   <line x1="115" y1="200" x2="115" y2="210" stroke="' . $color . '" stroke-width="4"/>
                   <line x1="135" y1="200" x2="135" y2="210" stroke="' . $color . '" stroke-width="4"/>
                   <line x1="155" y1="200" x2="155" y2="210" stroke="' . $color . '" stroke-width="4"/>
                   <line x1="175" y1="200" x2="175" y2="210" stroke="' . $color . '" stroke-width="4"/>',

    'ssd'      => '<rect x="120" y="80" width="60" height="140" rx="5" fill="' . $color . '"/>
                   <rect x="128" y="92" width="44" height="50" rx="3" fill="#1a2a1a"/>
                   <rect x="128" y="150" width="44" height="50" rx="3" fill="#1a2a1a"/>
                   <rect x="124" y="215" width="52" height="8" rx="2" fill="#c8a020"/>',

    'printer'  => '<rect x="65" y="115" width="170" height="80" rx="8" fill="' . $color . '"/>
                   <rect x="80" y="85" width="140" height="35" rx="4" fill="' . $color . '" opacity=".7"/>
                   <rect x="88" y="90" width="124" height="25" rx="2" fill="#fff" opacity=".9"/>
                   <line x1="80" y1="150" x2="220" y2="150" stroke="#1a1a2e" stroke-width="2" opacity=".4"/>',

    'router'   => '<rect x="65" y="150" width="170" height="55" rx="8" fill="' . $color . '"/>
                   <line x1="100" y1="150" x2="95" y2="90" stroke="' . $color . '" stroke-width="6" stroke-linecap="round"/>
                   <line x1="150" y1="150" x2="150" y2="80" stroke="' . $color . '" stroke-width="6" stroke-linecap="round"/>
                   <line x1="200" y1="150" x2="205" y2="90" stroke="' . $color . '" stroke-width="6" stroke-linecap="round"/>
                   <circle cx="100" cy="165" r="5" fill="#00e676"/>
                   <circle cx="118" cy="165" r="5" fill="#00e676"/>
                   <circle cx="136" cy="165" r="5" fill="#ffeb3b"/>',

    'keyboard' => '<rect x="55" y="125" width="190" height="70" rx="8" fill="' . $color . '"/>
                   <rect x="63" y="133" width="24" height="18" rx="3" fill="#1a1a2e"/>
                   <rect x="92" y="133" width="24" height="18" rx="3" fill="#00e5ff"/>
                   <rect x="121" y="133" width="24" height="18" rx="3" fill="#1a1a2e"/>
                   <rect x="150" y="133" width="24" height="18" rx="3" fill="#ff1744"/>
                   <rect x="63" y="157" width="24" height="18" rx="3" fill="#1a1a2e"/>
                   <rect x="92" y="157" width="55" height="18" rx="3" fill="#1a1a2e"/>
                   <rect x="152" y="157" width="43" height="18" rx="3" fill="#1a1a2e"/>',

    'headset'  => '<path d="M100 170 Q100 100 200 100 Q300 100 200 170" fill="none" stroke="' . $color . '" stroke-width="10" stroke-linecap="round"/>
                   <ellipse cx="100" cy="178" rx="22" ry="28" fill="' . $color . '"/>
                   <ellipse cx="200" cy="178" rx="22" ry="28" fill="' . $color . '"/>
                   <ellipse cx="100" cy="178" rx="14" ry="19" fill="#1a1a2e"/>
                   <ellipse cx="200" cy="178" rx="14" ry="19" fill="#1a1a2e"/>',

    'default'  => '<rect x="100" y="100" width="100" height="80" rx="8" fill="' . $color . '" opacity=".6"/>
                   <circle cx="130" cy="125" r="12" fill="' . $color . '"/>
                   <path d="M105 175 L130 148 L155 162 L175 140 L195 175Z" fill="' . $color . '"/>',
];

$iconoSvg = $iconosSvg[$tipo] ?? $iconosSvg['default'];
$labelY   = $marca ? '250' : '248';

$svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="300" height="300" viewBox="0 0 300 300">
  <defs>
    <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="{$bg}"/>
      <stop offset="100%" stop-color="#ffffff"/>
    </linearGradient>
    <filter id="shadow">
      <feDropShadow dx="0" dy="2" stdDeviation="3" flood-opacity="0.12"/>
    </filter>
  </defs>
  
  <!-- Fondo -->
  <rect width="300" height="300" fill="url(#bg)"/>
  
  <!-- Círculo decorativo -->
  <circle cx="150" cy="148" r="95" fill="{$color}" opacity="0.07"/>
  <circle cx="150" cy="148" r="75" fill="{$color}" opacity="0.06"/>
  
  <!-- Icono del producto -->
  <g filter="url(#shadow)">
    {$iconoSvg}
  </g>
  
  <!-- Banda inferior -->
  <rect x="0" y="258" width="300" height="42" fill="{$color}" opacity="0.9" rx="0"/>
  
  <!-- Texto marca/tipo -->
  <text x="150" y="284" 
        font-family="'Segoe UI', Arial, sans-serif" 
        font-size="14" 
        font-weight="700"
        fill="white" 
        text-anchor="middle"
        letter-spacing="1">{$label}</text>
</svg>
SVG;

header('Content-Type: image/svg+xml');
header('Cache-Control: public, max-age=3600');
echo $svg;
exit;
