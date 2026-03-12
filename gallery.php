<?php
header('Content-Type: application/json; charset=utf-8');

$allowedFolders = ['tokarnaya', 'frezernaya', 'lazernaya'];
$folder = $_GET['folder'] ?? '';

if (!in_array($folder, $allowedFolders, true)) {
    echo json_encode([]);
    exit;
}

$sourceDir = __DIR__ . "/gallery/$folder/";
$webpDir   = __DIR__ . "/gallery-webp/$folder/";
$webpUrl   = "./gallery-webp/$folder/";

if (!is_dir($sourceDir)) {
    echo json_encode([]);
    exit;
}

if (!is_dir($webpDir)) {
    mkdir($webpDir, 0755, true);
}

$files = glob($sourceDir . '*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}', GLOB_BRACE);
sort($files, SORT_NATURAL | SORT_FLAG_CASE);

$images = [];

foreach ($files as $filePath) {
    if (!is_file($filePath)) {
        continue;
    }

    $filename = basename($filePath);
    $nameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    $webpFilename = $nameWithoutExt . '.webp';
    $webpPath = $webpDir . $webpFilename;
    $webpPublicPath = $webpUrl . $webpFilename;

    // Если исходник уже webp — просто копируем в gallery-webp при необходимости
    if ($extension === 'webp') {
        if (!file_exists($webpPath)) {
            copy($filePath, $webpPath);
        }
    } else {
        // Генерируем webp только если его ещё нет
        if (!file_exists($webpPath)) {
            convertToWebp($filePath, $webpPath, 82);
        }
    }

    if (file_exists($webpPath)) {
        $images[] = [
            'src' => $webpPublicPath,
            'alt' => makeAlt($folder, $nameWithoutExt),
        ];
    }
}

echo json_encode($images, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit;


/**
 * Конвертация JPG/PNG в WebP
 */
function convertToWebp(string $sourcePath, string $destinationPath, int $quality = 82): bool
{
    $extension = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));

    switch ($extension) {
        case 'jpg':
        case 'jpeg':
            $image = @imagecreatefromjpeg($sourcePath);
            break;

        case 'png':
            $image = @imagecreatefrompng($sourcePath);
            if ($image !== false) {
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
            }
            break;

        default:
            return false;
    }

    if ($image === false) {
        return false;
    }

    $result = imagewebp($image, $destinationPath, $quality);
    imagedestroy($image);

    return $result;
}

/**
 * Генерация alt
 */
function makeAlt(string $folder, string $filename): string
{
    $titles = [
        'tokarnaya'  => 'Токарная обработка',
        'frezernaya' => 'Фрезерная обработка',
        'lazernaya'  => 'Лазерная резка и гибка',
    ];

    $baseTitle = $titles[$folder] ?? 'Галерея';
    $cleanName = str_replace(['-', '_'], ' ', $filename);

    return trim($baseTitle . ' - ' . $cleanName);
}