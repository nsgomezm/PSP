<?php
  Class GestionProyecto extends Controller{
    function __construct(){
      parent::__construct();
      $this->view->sesion->sesionStart();
      if($this->view->sesion->getSesion('usuario') == -1){
        $this->view->redirect('');
      }
      $this->view->gestionProyecto = $this->view->sesion->getSesion('proyecto');

      if($this->view->sesion->getSesion('usuario')[4] == 'administrador'){
        $this->view->redirect('');
      }

      $this->cedula = $this->view->sesion->getSesion('usuario')[3];
    }

    function render(){
      $this->loadModel('gestionProyetoModel');
      $this->view->data = $this->model->getListado($this->cedula);

      $this->loadModel('medidasModel');
      $this->view->listMedidas = $this->model->getListado();

      $this->loadModel('procesosModel');
      $this->view->listProcesos = $this->model->getListado();

      $this->view->render('gestionProyecto/lista');

    }

    function terminarProyecto($codigo){

      $this->loadModel('gestionProyetoModel');
      $this->model->terminar($this->cedula, $codigo[0]);
      $this->render();
      echo "<script>swal('PSP', 'El proyecto a terminado exitosamente', 'success')</script>";
    }

    function abrirProyecto($codigo = 0){

      $fecha = getdate()['year']. getdate()['mon'].getdate()['mday'];

      $this->loadModel('gestionProyetoModel');

      if($this->view->sesion->getSesion('proyecto') != -1 ) {
        $this->view->redirect('gestionProyecto/summary');
      }else{
        $resultValid = $this->model->validarAsociado($this->cedula, $codigo[0]);

        $this->loadModel('proyectosModel');
        $this->view->sesion->setSesion('proyecto', ['idProyecto' => $codigo[0], 'proceso' => $this->model->get($codigo)['idProceso'],  'terminado' => $resultValid['terminado']]);
        $this->view->redirect('gestionProyecto/summary');
      }

    }

    function cerrarProyecto(){
      $this->view->sesion->unset('proyecto');
      $this->view->redirect('');
    }

    function summary($codigo = 0){

      $codigo = ($codigo == 0) ? " " : $codigo ;

      if($this->view->sesion->getSesion('proyecto') != -1){
        $codigo = [$this->view->sesion->getSesion('proyecto')['idProyecto']];
      }else if($codigo == " "){
        $this->view->redirect('');
      }

      $this->loadModel('ProyectosModel');
      $proyectoData =  $this->model->get($codigo);

      $this->loadModel('lenguajesModel');
      $this->view->listLenguajes = $this->model->getListado();
      $nombreLenguaje = "";
      foreach ($this->view->listLenguajes as $lenguaje ){
        if($lenguaje['idLenguaje'] == $proyectoData['idLenguaje']){
          $nombreLenguaje = $lenguaje['nombre'];
        }
      }

      $this->view->encabezado = [
        'proyecto' => $proyectoData['nombre'],
        'director' => $proyectoData['cedulaAdministrador'],
        'fechaIn' => $proyectoData['fechaIn'],
        'fechaOut' => $proyectoData['fechaOut'],
        'lenguaje' => $nombreLenguaje,
      ];

      $this->view->render('gestionProyecto/summary');


    }



  }
?>