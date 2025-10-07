<?php
// Archivo: clases.php

interface Inventariable{
    public function obtenerInformacionInventario();
}
abstract class Producto implements Inventariable {
    public $id;
    public $nombre;
    public $descripcion;
    public $estado;
    public $stock;
    public $fechaIngreso;
    public $categoria;

    public function __construct($datos) {
        foreach ($datos as $clave => $valor) {
            if (property_exists($this, $clave)) {
                $this->$clave = $valor;
            }
        }
    }
}

class ProductoElectronico extends Producto{
    public $garantiaMeses;
    public function __construct($datos){
        parent::__construct($datos);
        $this->garantiaMeses = $datos['garantiaMeses'] ?? null;
    }
    function obtenerInformacionInventario(){
        return "Meses de garanía: " . $this->garantiaMeses;
    }
}

class ProductoAlimento extends Producto{
    public $fechaVencimiento;
    public function __construct($datos){
        parent::__construct($datos);
        $this->fechaVencimiento = $datos['fechaVencimiento'] ?? null;
    }
    function obtenerInformacionInventario(){
        return "Fecha de vemcimiento: " . $this->fechaVencimiento;
    }
}

class ProductoRopa extends Producto{
    public $talla;
    public function __construct($datos){
        parent::__construct($datos);
        $this->talla = $datos['talla'] ?? null;
    }
    function obtenerInformacionInventario(){
        return "Talla: " . $this->talla;
    }
}

class GestorInventario {
    private $items = [];
    private $rutaArchivo = 'productos.json';

    public function obtenerTodos() {
        if (empty($this->items)) {
            $this->cargarDesdeArchivo();
        }
        return $this->items;
    }

    private function cargarDesdeArchivo() {
        if (!file_exists($this->rutaArchivo)) {
            return;
        }
        
        $jsonContenido = file_get_contents($this->rutaArchivo);
        $arrayDatos = json_decode($jsonContenido, true);
        
        if ($arrayDatos === null) {
            return;
        }
        
        foreach ($arrayDatos as $datos) {
            $this->crearInstancia($datos);
        }
    }

    private function persistirEnArchivo() {
        $arrayParaGuardar = array_map(function($item) {
            return get_object_vars($item);
        }, $this->items);
        
        file_put_contents(
            $this->rutaArchivo, 
            json_encode($arrayParaGuardar, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    public function obtenerMaximoId() {
        if (empty($this->items)) {
            return 0;
        }
        
        $ids = array_map(function($item) {
            return $item->id;
        }, $this->items);
        
        return max($ids);
    }
    function agregar($nuevoProducto){
       
        $id = $this->obtenerMaximoId() + 1;
        $this->persistirEnArchivo();
        return;
    }
 
    function eliminar($idProducto){
        $aux=[];
 
        foreach ($this->items as $item){
            if ($item['id']==$idProducto){
               
            }else{
                $aux[]=$item;
            }
 
        }  
        $this->items=$aux;
        $this->persistirEnArchivo();
        return;
    }
 
    function actualizar($productoActualizado){
            foreach ($this->items as $i => $item) {
            if ($item->id == $productoActualizado->id) {
                $this->items[$i] = $productoActualizado;
                break;
            }
        }
        $this->persistirEnArchivo();
    }

    function crearInstancia($itemsData){
    $item="";
    switch($itemsData['categoria']) {
        case 'electronico': 
            $item = new ProductoElectronico($itemsData);
            break;
        case 'alimento': 
            $item = new ProductoAlimento($itemsData);
            break;
        case 'ropa': 
            $item = new ProductoRopa($itemsData);
            break;
        }
    return $item;
    }

    public function obtenerPorId($id) {
        foreach ($this->items as $item) {
            if ($item->id == $id) {
                return $item;
            }
        }
        return null;
    }

    public function cambiarEstado($idProducto, $estadoNuevo){
     foreach ($this->items as $recurso) {
            if ($recurso->id == $idProducto) {
                $recurso->estado = $estadoNuevo;
                break;
            }
        }
        $this->persistirEnArchivo();
    }
}
        

