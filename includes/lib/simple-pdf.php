<?php
/**
 * Clase para generación de HTML imprimible como PDF
 * El usuario puede usar Ctrl+P o Imprimir para guardarlo como PDF
 */

class Simple_PDF {
    private $html = '';
    private $title = 'Documento';
    
    public function __construct() {
        $this->html = '';
    }
    
    public function setTitle($title) {
        $this->title = $title;
    }
    
    public function addHTML($html) {
        $this->html .= $html;
    }
    
    public function output($filename) {
        // Generar HTML optimizado para impresión/PDF
        $output = $this->generatePrintableHTML();
        file_put_contents($filename, $output);
        return $filename;
    }
    
    private function generatePrintableHTML() {
        // HTML optimizado para convertir a PDF con el navegador
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>' . htmlspecialchars($this->title) . '</title>
    <style>
        @page {
            size: A4;
            margin: 1.5cm;
        }
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            background: white;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #0073aa;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #0073aa;
            margin: 10px 0;
            font-size: 28pt;
        }
        .cotizacion-info {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .cliente-info {
            margin-bottom: 30px;
        }
        .cliente-info h3 {
            color: #0073aa;
            border-bottom: 2px solid #0073aa;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background: #0073aa;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #ddd;
        }
        .total-row {
            background: #f5f5f5;
            font-weight: bold;
            font-size: 14pt;
        }
        .total-row td {
            padding: 15px 8px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #0073aa;
            text-align: center;
            font-size: 10pt;
            color: #666;
        }
        .validez {
            background: #fff3cd;
            padding: 15px;
            border-left: 4px solid #ffc107;
            margin: 20px 0;
        }
        strong {
            font-weight: bold;
        }
        p {
            margin: 8px 0;
        }
    </style>
</head>
<body>
    ' . $this->html . '
    <script>
        // Auto-print cuando se abre (opcional, comentado para testing)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>';
        
        return $html;
    }
}
