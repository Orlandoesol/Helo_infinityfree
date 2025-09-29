<?php
require_once __DIR__ . '/includes/init.php';

// Verificar autenticación
requireAuth();

try {
    $db = Database::getInstance();

    $sql = "SELECT id, date, name, phone, email, text, deleted 
            FROM registers 
            WHERE deleted = 0 
            ORDER BY date DESC";
    $stmt = $db->query($sql);
    $registers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Configurar cabeceras para descargar el CSV
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=registros.csv');

    // Crear el archivo CSV en memoria
    $output = fopen('php://output', 'w');

    // Escribir encabezados del CSV
    fputcsv($output, ['ID', 'Fecha', 'Nombre', 'Teléfono', 'Email', 'Objetivos de la empresa', 'Estado']);

    // Escribir los datos
    foreach ($registers as $row) {
        fputcsv($output, [
            $row['id'],
            $row['date'],
            $row['name'],
            $row['phone'],
            $row['email'],
            $row['text'],
            $row['deleted'] ? 'Inactivo' : 'Activo'
        ]);
    }

    fclose($output);
    exit;

} catch (Exception $e) {
    error_log("Error al exportar CSV: " . $e->getMessage());
    echo "Ocurrió un error al generar el archivo CSV.";
}
