<?php
/**
 * TechStore - Generador de imagen placeholder
 * Ejecutar una vez para crear no-image.jpg
 * Archivo: assets/images/products/create-placeholder.php
 */

// Crear imagen placeholder SVG como HTML
$svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="400" height="400" viewBox="0 0 400 400">
  <rect width="400" height="400" fill="#f8f9fa"/>
  <rect x="150" y="140" width="100" height="80" rx="8" fill="#dee2e6"/>
  <circle cx="185" cy="165" r="12" fill="#adb5bd"/>
  <path d="M155 210 L180 185 L205 205 L230 180 L245 210Z" fill="#ced4da"/>
  <text x="200" y="260" font-family="sans-serif" font-size="14" fill="#adb5bd" text-anchor="middle">Sin imagen</text>
</svg>
SVG;

file_put_contents(__DIR__ . '/no-image.svg', $svg);
echo "Placeholder creado.";
