<?php
//activamos almacenamiento en el buffer
ob_start();
session_start();
if (!isset($_SESSION['nombre'])) {
  header("Location: login.html");
} else {


  require 'header.php';
}
if ($_SESSION['escritorio'] == 1) {

  require_once "../modelos/Consultas.php";
  $consulta = new Consultas();
  $rsptac = $consulta->totalcomprahoy();
  $regc = $rsptac->fetch_object();
  $totalc = $regc->total_compra;

  $rsptav = $consulta->totalventahoy();
  $regv = $rsptav->fetch_object();
  $totalv = $regv->total_venta;

  //obtener valores para cargar al grafico de barras
  $compras10 = $consulta->comprasultimos_10dias();
  $fechasc = '';
  $totalesc = '';
  while ($regfechac = $compras10->fetch_object()) {
    $fechasc = $fechasc . '"' . $regfechac->fecha . '",';
    $totalesc = $totalesc . $regfechac->total . ',';
  }


  //quitamos la ultima coma
  $fechasc = substr($fechasc, 0, -1);
  $totalesc = substr($totalesc, 0, -1);
}

//obtener valores para cargar al grafico de barras
$ventas12 = $consulta->ventasultimos_12meses();
$fechasv = '';
$totalesv = '';
while ($regfechav = $ventas12->fetch_object()) {
  $fechasv = $fechasv . '"' . $regfechav->fecha . '",';
  $totalesv = $totalesv . $regfechav->total . ',';
}


//quitamos la ultima coma
$fechasv = substr($fechasv, 0, -1);
$totalesv = substr($totalesv, 0, -1);
?>
<div class="content-wrapper">
  <!-- Main content -->
  <section class="content">

    <!-- Default box -->
    <div class="row">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h1 class="box-title">Escritorio</h1>
            <div class="box-tools pull-right">

            </div>
          </div>
          <!--box-header-->
          <!--centro-->
          <div class="panel-body">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
              <div class="small-box bg-aqua">
                <div class="inner">
                  <h4 style="font-size: 17px;">
                    <strong>Q/. <?php echo $totalc; ?> </strong>
                  </h4>
                  <p>Compras</p>
                </div>
                <div class="icon">
                  <i class="ion ion-bag"></i>
                </div>
                <a href="ingreso.php" class="small-box-footer">Compras <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
              <div class="small-box bg-green">
                <div class="inner">
                  <h4 style="font-size: 17px;">
                    <strong>Q/. <?php echo $totalv; ?> </strong>
                  </h4>
                  <p>Ventas</p>
                </div>
                <div class="icon">
                  <i class="ion ion-bag"></i>
                </div>
                <a href="venta.php" class="small-box-footer">Ventas <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div>
          </div>