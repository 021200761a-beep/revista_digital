<?php

/**
 * Sube una imagen al directorio de reportajes.
 *
 * Retorna la ruta relativa de la imagen subida.
 * Lanza una excepción si existe algún problema.
 */
function subirImagenReportaje(array $archivo): string
{
    if (
        !isset($archivo['error']) ||
        !isset($archivo['tmp_name']) ||
        !isset($archivo['size'])
    ) {
        throw new RuntimeException(
            'No se recibió correctamente la imagen.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Verificar errores de subida
    |--------------------------------------------------------------------------
    */

    if ($archivo['error'] !== UPLOAD_ERR_OK) {

        switch ($archivo['error']) {

            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException(
                    'La imagen supera el tamaño máximo permitido.'
                );

            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException(
                    'No se seleccionó ninguna imagen.'
                );

            default:
                throw new RuntimeException(
                    'Ocurrió un error al subir la imagen.'
                );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Tamaño máximo
    |--------------------------------------------------------------------------
    */

    $maximoBytes = 5 * 1024 * 1024;

    if ($archivo['size'] > $maximoBytes) {

        throw new RuntimeException(
            'La imagen no puede superar los 5 MB.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Verificar que realmente sea una imagen
    |--------------------------------------------------------------------------
    */

    $informacionImagen = @getimagesize(
        $archivo['tmp_name']
    );

    if ($informacionImagen === false) {

        throw new RuntimeException(
            'El archivo seleccionado no es una imagen válida.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Tipos MIME permitidos
    |--------------------------------------------------------------------------
    */

    $tiposPermitidos = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif'
    ];

    $mime = $informacionImagen['mime'];

    if (!isset($tiposPermitidos[$mime])) {

        throw new RuntimeException(
            'Formato no permitido. Usa JPG, PNG, WEBP o GIF.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Crear directorio
    |--------------------------------------------------------------------------
    */

    $directorio = __DIR__
        . '/../assets/uploads/reportajes/';

    if (!is_dir($directorio)) {

        if (!mkdir($directorio, 0755, true)) {

            throw new RuntimeException(
                'No se pudo crear el directorio de imágenes.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Nombre aleatorio
    |--------------------------------------------------------------------------
    */

    $extension = $tiposPermitidos[$mime];

    $nombreArchivo =
        'reportaje_' .
        bin2hex(random_bytes(16)) .
        '.' .
        $extension;

    $rutaDestino = $directorio . $nombreArchivo;

    /*
    |--------------------------------------------------------------------------
    | Mover archivo
    |--------------------------------------------------------------------------
    */

    if (
        !move_uploaded_file(
            $archivo['tmp_name'],
            $rutaDestino
        )
    ) {

        throw new RuntimeException(
            'No se pudo guardar la imagen.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Ruta que guardaremos en MySQL
    |--------------------------------------------------------------------------
    */

    return 'assets/uploads/reportajes/' . $nombreArchivo;
}


/**
 * Elimina una imagen de reportaje si pertenece
 * al directorio de uploads correspondiente.
 */
function eliminarImagenReportaje(?string $ruta): void
{
    if (!$ruta) {
        return;
    }

    $prefijoPermitido = 'assets/uploads/reportajes/';

    if (
        strpos($ruta, $prefijoPermitido) !== 0
    ) {
        return;
    }

    $rutaFisica = __DIR__ . '/../' . $ruta;

    if (is_file($rutaFisica)) {
        @unlink($rutaFisica);
    }
}

function subirImagenNoticia(array $archivo): string
{
    if (
        !isset($archivo['error']) ||
        !isset($archivo['tmp_name']) ||
        !isset($archivo['size'])
    ) {
        throw new RuntimeException(
            'No se recibió correctamente la imagen.'
        );
    }

    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        switch ($archivo['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException(
                    'La imagen supera el tamaño máximo permitido.'
                );

            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException(
                    'No se seleccionó ninguna imagen.'
                );

            default:
                throw new RuntimeException(
                    'Ocurrió un error al subir la imagen.'
                );
        }
    }

    $maximoBytes = 5 * 1024 * 1024;

    if ($archivo['size'] > $maximoBytes) {
        throw new RuntimeException(
            'La imagen no puede superar los 5 MB.'
        );
    }

    $informacionImagen = @getimagesize(
        $archivo['tmp_name']
    );

    if ($informacionImagen === false) {
        throw new RuntimeException(
            'El archivo seleccionado no es una imagen válida.'
        );
    }

    $tiposPermitidos = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif'
    ];

    $mime = $informacionImagen['mime'];

    if (!isset($tiposPermitidos[$mime])) {
        throw new RuntimeException(
            'Formato no permitido. Usa JPG, PNG, WEBP o GIF.'
        );
    }

    $directorio = __DIR__ . '/../assets/uploads/noticias/';

    if (!is_dir($directorio)) {
        if (!mkdir($directorio, 0755, true)) {
            throw new RuntimeException(
                'No se pudo crear el directorio de imágenes.'
            );
        }
    }

    $extension = $tiposPermitidos[$mime];

    $nombreArchivo =
        'noticia_' .
        bin2hex(random_bytes(16)) .
        '.' .
        $extension;

    $rutaDestino = $directorio . $nombreArchivo;

    if (!move_uploaded_file(
        $archivo['tmp_name'],
        $rutaDestino
    )) {
        throw new RuntimeException(
            'No se pudo guardar la imagen.'
        );
    }

    return 'assets/uploads/noticias/' . $nombreArchivo;
}


function eliminarImagenNoticia(?string $ruta): void
{
    if (!$ruta) {
        return;
    }

    $prefijoPermitido = 'assets/uploads/noticias/';

    if (
        strpos($ruta, $prefijoPermitido) !== 0
    ) {
        return;
    }

    $rutaFisica =
        __DIR__ . '/../' . $ruta;

    if (is_file($rutaFisica)) {
        @unlink($rutaFisica);
    }
}

/**
 * Sube la portada de un boletín.
 */
function subirPortadaBoletin(array $archivo): string
{
    if (
        !isset($archivo['error']) ||
        !isset($archivo['tmp_name']) ||
        !isset($archivo['size'])
    ) {
        throw new RuntimeException(
            'No se recibió correctamente la portada.'
        );
    }

    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        switch ($archivo['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException(
                    'La portada supera el tamaño máximo permitido.'
                );

            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException(
                    'No se seleccionó ninguna portada.'
                );

            default:
                throw new RuntimeException(
                    'Ocurrió un error al subir la portada.'
                );
        }
    }

    $maximoBytes = 5 * 1024 * 1024;

    if ($archivo['size'] > $maximoBytes) {
        throw new RuntimeException(
            'La portada no puede superar los 5 MB.'
        );
    }

    $informacionImagen = @getimagesize($archivo['tmp_name']);

    if ($informacionImagen === false) {
        throw new RuntimeException(
            'El archivo seleccionado no es una imagen válida.'
        );
    }

    $tiposPermitidos = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif'
    ];

    $mime = $informacionImagen['mime'];

    if (!isset($tiposPermitidos[$mime])) {
        throw new RuntimeException(
            'Formato no permitido. Usa JPG, PNG, WEBP o GIF.'
        );
    }

    $directorio = __DIR__ . '/../assets/uploads/boletines/';

    if (!is_dir($directorio)) {
        if (!mkdir($directorio, 0755, true)) {
            throw new RuntimeException(
                'No se pudo crear el directorio de boletines.'
            );
        }
    }

    $extension = $tiposPermitidos[$mime];

    $nombreArchivo =
        'boletin_portada_' .
        bin2hex(random_bytes(16)) .
        '.' .
        $extension;

    $rutaDestino = $directorio . $nombreArchivo;

    if (!move_uploaded_file(
        $archivo['tmp_name'],
        $rutaDestino
    )) {
        throw new RuntimeException(
            'No se pudo guardar la portada.'
        );
    }

    return 'assets/uploads/boletines/' . $nombreArchivo;
}


/**
 * Sube el archivo PDF de un boletín.
 */
function subirPdfBoletin(array $archivo): string
{
    if (
        !isset($archivo['error']) ||
        !isset($archivo['tmp_name']) ||
        !isset($archivo['size'])
    ) {
        throw new RuntimeException(
            'No se recibió correctamente el archivo PDF.'
        );
    }

    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        switch ($archivo['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException(
                    'El PDF supera el tamaño máximo permitido.'
                );

            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException(
                    'No se seleccionó ningún PDF.'
                );

            default:
                throw new RuntimeException(
                    'Ocurrió un error al subir el PDF.'
                );
        }
    }

    $maximoBytes = 10 * 1024 * 1024;

    if ($archivo['size'] > $maximoBytes) {
        throw new RuntimeException(
            'El PDF no puede superar los 10 MB.'
        );
    }

    $extension = strtolower(
        pathinfo(
            $archivo['name'],
            PATHINFO_EXTENSION
        )
    );

    if ($extension !== 'pdf') {
        throw new RuntimeException(
            'El archivo debe tener formato PDF.'
        );
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);

    $mime = $finfo->file($archivo['tmp_name']);

    if ($mime !== 'application/pdf') {
        throw new RuntimeException(
            'El archivo seleccionado no es un PDF válido.'
        );
    }

    $contenidoInicial = file_get_contents(
        $archivo['tmp_name'],
        false,
        null,
        0,
        4
    );

    if ($contenidoInicial !== '%PDF') {
        throw new RuntimeException(
            'El archivo no contiene una estructura PDF válida.'
        );
    }

    $directorio = __DIR__ . '/../assets/uploads/boletines/';

    if (!is_dir($directorio)) {
        if (!mkdir($directorio, 0755, true)) {
            throw new RuntimeException(
                'No se pudo crear el directorio de boletines.'
            );
        }
    }

    $nombreArchivo =
        'boletin_pdf_' .
        bin2hex(random_bytes(16)) .
        '.pdf';

    $rutaDestino = $directorio . $nombreArchivo;

    if (!move_uploaded_file(
        $archivo['tmp_name'],
        $rutaDestino
    )) {
        throw new RuntimeException(
            'No se pudo guardar el archivo PDF.'
        );
    }

    return 'assets/uploads/boletines/' . $nombreArchivo;
}


/**
 * Elimina un archivo perteneciente a un boletín.
 */
function eliminarArchivoBoletin(?string $ruta): void
{
    if (!$ruta) {
        return;
    }

    $prefijoPermitido =
        'assets/uploads/boletines/';

    if (strpos($ruta, $prefijoPermitido) !== 0) {
        return;
    }

    $rutaFisica =
        __DIR__ . '/../' . $ruta;

    if (is_file($rutaFisica)) {
        @unlink($rutaFisica);
    }
}