<?php
//activamos almacenamiento en el buffer
ob_start();
if (strlen(session_id()) < 1)
  session_start();

if (!isset($_SESSION['nombre'])) {
  echo "debe ingresar al sistema correctamente para visualizar el reporte";
} else {

  if ($_SESSION['ventas'] == 1) {

    //incluimos el archivo factura
    require('Factura.php');

    //establecemos los datos de la empresa
    $logo = "logo.png";
    $ext_logo = "png";
    $empresa = "Auto Repuestos Richard";
    $documento = "313473930903";
    $direccion = "Escuintla";
    $telefono = "40475290";
    $email = "autorepuetosrichard@corporacion.com";

    //obtiene los datos de la cabecera de la venta actual
    require_once "../modelos/Venta.php";
    $venta = new Venta();
    $rsptav = $venta->ventacabecera($_GET["id"]);

    //recorre todos los valores que obtengamos
    $regv = $rsptav->fetch_object();

    //configuracion de la factura
    $pdf = new PDF_Invoice('p', 'mm', 'A4');
    $pdf->AddPage();

    //envia datos de la empresa al metodo addSociete de la clase factura
    $pdf->addSociete(
      utf8_decode($empresa),
      $documento . "\n" .
        utf8_decode("Direccion: ") . utf8_decode($direccion) . "\n" .
        utf8_decode("Telefono: ") . $telefono . "\n" .
        "Email: " . $email,
      $logo,
      $ext_logo
    );

    $pdf->fact_dev("$regv->tipo_comprobante ", "$regv->serie_comprobante- $regv->num_comprobante");
    $pdf->temporaire("");
    $pdf->addDate($regv->fecha);

    //envia los datos del cliente al metodo addClientAddresse de la clase factura
    $pdf->addClientAdresse(
      utf8_decode($regv->cliente),
      "Domicilio: " . utf8_decode($regv->direccion),
      $regv->tipo_documento . ": " . $regv->num_documento,
      "Email: " . $regv->email,
      "Telefono: " . $regv->telefono
    );

    //establece las columnas que va tener lña seccion donde mostramos los detalles de la venta
    $cols = array(
      "CODIGO" => 23,
      "DESCRIPCION" => 78,
      "CANTIDAD" => 22,
      "P.U." => 25,
      "DSCTO" => 20,
      "SUBTOTAL" => 22
    );
    $pdf->addCols($cols);
    $cols = array(
      "CODIGO" => "L",
      "DESCRIPCION" => "L",
      "CANTIDAD" => "C",
      "P.U." => "R",
      "DSCTO" => "R",
      "SUBTOTAL" => "C"
    );
    $pdf->addLineFormat($cols);
    $pdf->addLineFormat($cols);

    //actualiza el valor de la coordenada "y" quie sera la ubicacion desde donde empecemos a mostrar los datos 
    $y = 85;

    //obtiene todos los detalles del a venta actual
    $rsptad = $venta->ventadetalles($_GET["id"]);

    while ($regd = $rsptad->fetch_object()) {
      $line = array(
        "CODIGO" => "$regd->codigo",
        "DESCRIPCION" => utf8_decode("$regd->articulo"),
        "CANTIDAD" => "$regd->cantidad",
        "P.U." => "$regd->precio_venta",
        "DSCTO" => "$regd->descuento",
        "SUBTOTAL" => "$regd->subtotal"
      );
      $size = $pdf->addLine($y, $line);
      $y += $size + 2;
    }

    /*aqui falta codigo de letras*/
    require_once "Letras.php";
    $V = new EnLetras();

    $total = $regv->total_venta;
    $V = new EnLetras();
    $V->substituir_un_mil_por_mil = true;

    $con_letra = strtoupper($V->ValorEnLetras($total, " Quetzales"));
    $pdf->addCadreTVAs("---" . $con_letra);


    //mostramos el impuesto
    $pdf->addTVAs($regv->impuesto, $regv->total_venta, "Q/ ");
    $pdf->addCadreEurosFrancs("IGV" . " $regv->impuesto %");
    $pdf->Output('Reporte de Venta', 'I');
  } else {
    echo "No tiene permiso para visualizar el reporte";
  }
}

ob_end_flush();
