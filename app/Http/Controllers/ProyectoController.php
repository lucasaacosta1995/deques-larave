<?php

namespace App\Http\Controllers;

use App\jo_proyecto;
use Illuminate\Http\Request;
use App\Swiftype;
use Illuminate\Support\Facades\DB;

class ProyectoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function fullTextWildcards($term)
    {
        // removing symbols used by MySQL
        $reservedSymbols = ['-', '+', '<', '>', '@', '(', ')', '~'];
        $term = str_replace($reservedSymbols, '', $term);

        $words = explode(' ', $term);

        foreach($words as $key => $word) {
            /*
             * applying + operator (required word) only big words
             * because smaller ones are not indexed by mysql
             */
            if(strlen($word) > 3) {
                $words[$key] = ' +' . $word . '* ';
            }
            else{
                $words[$key] = ' *' . $word . '* ';
            }
        }

        $searchTerm = implode( ' ', $words);

        return $searchTerm;
    }
    public function index($q = '')
    {
        $_POST['q'] = $this->fullTextWildcards($q);
        $proyetos = DB::table('jo_proyectos')
            ->select('*')
            ->orWhere(function($query)
            {
                $query->orWhereRaw("MATCH (jo_proyectos.name) AGAINST (? IN BOOLEAN MODE)", $_POST['q'])
                    ->orWhereRaw("MATCH (jo_proyectos.expediente) AGAINST (? IN BOOLEAN MODE)", $_POST['q']);
            })->get()
            ->toArray();
        die(var_dump($proyetos));

    }

    public function verEngines(){
        $client = new Swiftype();
        die(var_dump($client->engines()));
    }
    public function verDocuments(){
        $client = new Swiftype();
        die(var_dump($client->document_type('5b19eef6d3b6875c02474bce')));
    }

    public function crearDocument($nameDocument = ''){
        $client = new Swiftype();
        $client->create_document_type('dequesetrata', $nameDocument);
        die(var_dump($client->document_type('5b19eef6d3b6875c02474bce')));
    }

    public function deleteDocument($nameDocument = ''){
        $client = new Swiftype();
        $client->destroy_document_type('dequesetrata', $nameDocument);
        die(var_dump($client->document_type('5b19eef6d3b6875c02474bce')));
    }
    public function validarExistencia($id){
        $client = new Swiftype();
        $configRequest = array(
            'per_page' => 1,
            'filters' => array('proyectos'=>array('external_id'=>$id))
        );
        $proyecto = $client->search('dequesetrata', 'proyectos', '', $configRequest);
        $proyectoRecords = (array) $proyecto;
        $idUltimo = 0;
        if(isset($proyectoRecords['body']->records->proyectos[0]->external_id)){
            $idUltimo = $proyectoRecords['body']->records->proyectos[0]->external_id;
        }
        return $idUltimo;
    }
    public function getUltimoProyecto(){
        $client = new Swiftype();
        $configRequest = array(
//            'per_page' => 1,
            'sort_field'=>array('proyectos'=>'id_proyecto'),
            'sort_direction'=>array('proyectos'=>'desc')
        );
        $proyecto = $client->search('dequesetrata', 'proyectos', '', $configRequest);
        $proyectoRecords = (array) $proyecto;
        $idUltimo = 0;
        if(isset($proyectoRecords['body']->records->proyectos[0]->external_id)){
            $idUltimo = $proyectoRecords['body']->records->proyectos[0]->external_id;
        }
        return $idUltimo;
    }
    public function actualizarProyectosSwiftype($idProyecto = 0){
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        $swiftype = new Swiftype();
        $ultimoProyectoCargadoSwft = $this->getUltimoProyecto();
        if($idProyecto > 0){
            $proyectos = jo_proyecto::with(['autor', 'congreso.provincia','tramite','tipo','firmantes.politico','preguntas_respuestas','etiquetas.etiqueta'])
            ->where('status',1)
            ->where('id_proyecto','=',$idProyecto)->orderBy('id_proyecto', 'asc')->limit(1)->get();
        }
        else{
          $proyectos = jo_proyecto::with(['autor', 'congreso.provincia','tramite','tipo','firmantes.politico','preguntas_respuestas','etiquetas.etiqueta'])
            ->where('status',1)
            ->where('id_proyecto','>',$ultimoProyectoCargadoSwft)->orderBy('id_proyecto', 'asc')->limit(1000)->get();  
        }
        
        $arrayComplete = array();
        foreach ($proyectos as $keyProyecto => $valueProyecto) {
            $externalId = $valueProyecto->id_proyecto;
            $status = $valueProyecto->status == 1 ? 'publicado' : 'despublicado';
            $proyectoUrl = 'https://dequesetrata.com.ar/proyecto/'.$valueProyecto->congreso->seo.'/'.$valueProyecto->seo;

            $detenciones = $valueProyecto->votos()->where('voto','D')->get()->toArray();
            $impulsos = $valueProyecto->votos()->where('voto','I')->get()->toArray();

            $preguntas = $valueProyecto->preguntas_respuestas()->where('match',null)->where('status',1)->get()->toArray();
            $respuestas = $valueProyecto->preguntas_respuestas()->where('match','!=',null)->where('status',1)->get()->toArray();

            $firmantes = $valueProyecto->firmantes;
            $firmantesArray = array();
            foreach ($firmantes as $firmante){
                if(!in_array($firmante->politico->name,$firmantesArray)){
                    $firmantesArray[] = $firmante->politico->name;
                }
            }
            $lugaresArray = array();
            $lugares = $valueProyecto->etiquetas->where('etiqueta.tipo',2);
            foreach ($lugares as $lugar){
                $lugaresArray[] = $lugar->etiqueta->name;
            }
            $temasArray = array();
            $temas = $valueProyecto->etiquetas->where('etiqueta.tipo',1);
            foreach ($temas as $tema){
                $temasArray[] = $tema->etiqueta->name;
            }

            $sinonimosArray = array();
            $etiquetasAll = $valueProyecto->etiquetas;
            foreach ($etiquetasAll as $etiquetaTmp1){
                if(!is_null($etiquetaTmp1->etiqueta)){
                    $sinonimosTmp = $etiquetaTmp1->etiqueta->sinonimos()->get();
                    if($sinonimosTmp->count() > 0){
                        foreach ($sinonimosTmp as $sinonimoTmp1){
                            $sinonimosArray[] = $sinonimoTmp1->sinonimo;
                        }
                    }
                }
            }
            $arrayFields = array(
                array(
                    "name"=>"id_proyecto",
                    "value"=>$valueProyecto->id_proyecto,
                    "type"=>"integer"
                ),array(
                    "name"=>"tipo",
                    "value"=>is_null($valueProyecto->tipo) ? []:[$valueProyecto->tipo->tipo_proyecto],
                    "type"=>"enum"
                ),
                array(
                    "name"=>"nombre",
                    "value"=>is_null($valueProyecto->name) ? '':$valueProyecto->name,
                    "type"=>"string"
                ),
                array(
                    "name"=>"fecha_de_presentacion",
                    "value"=>is_null($valueProyecto->fecha_publicacion) ? '':$valueProyecto->fecha_publicacion,
                    "type"=>"date"
                ),
                array(
                    "name"=>"autor",
                    "value"=>is_null($valueProyecto->autor) ? []:[$valueProyecto->autor->name],
                    "type"=>"enum"
                ),
                array(
                    "name"=>"firmantes",
                    "value"=>$firmantesArray,
                    "type"=>"enum"
                ),
                array(
                    "name"=>"expediente",
                    "value"=>is_null($valueProyecto->expediente) ? []:[$valueProyecto->expediente],
                    "type"=>"enum"
                ),
                array(
                    "name"=>"legislatura",
                    "value"=>is_null($valueProyecto->congreso) ? []:[$valueProyecto->congreso->name],
                    "type"=>"enum"
                ),
                array(
                    "name"=>"sinonimos",
                    "value"=>$sinonimosArray,
                    "type"=>"enum"
                ),
                array(
                    "name"=>"temas",
                    "value"=>$temasArray,
                    "type"=>"enum"
                ),
                array(
                    "name"=>"lugares",
                    "value"=>$lugaresArray,
                    "type"=>"enum"
                ),
                array(
                    "name"=>"estado_parlamentario",
                    "value"=>is_null($valueProyecto->tramite) ? []:[$valueProyecto->tramite->name],
                    "type"=>"enum"
                ),
                array(
                    "name"=>"resumen",
                    "value"=>is_null($valueProyecto->resumen) ? '':$valueProyecto->resumen,
                    "type"=>"text"
                ),
                array(
                    "name"=>"descripcion",
                    "value"=>is_null($valueProyecto->descripcion) ? '':$valueProyecto->descripcion,
                    "type"=>"text"
                ),
                array(
                    "name"=>"fundamentos",
                    "value"=>is_null($valueProyecto->fundamentos) ? '':$valueProyecto->fundamentos,
                    "type"=>"text"
                ),
                array(
                    "name"=>"nro_de_impulsos",
                    "value"=>count($impulsos),
                    "type"=>"integer"
                ),
                array(
                    "name"=>"nro_de_detenciones",
                    "value"=>count($detenciones),
                    "type"=>"integer"
                ),
                array(
                    "name"=>"neto_campania",
                    "value"=>(count($impulsos) - count($detenciones)),
                    "type"=>"integer"
                ),
                array(
                    "name"=>"cantidad_de_campanias",
                    "value"=>(count($impulsos) + count($detenciones)),
                    "type"=>"integer"
                ),
                array(
                    "name"=>"nro_de_preguntas",
                    "value"=>count($preguntas),
                    "type"=>"integer"
                ),
                array(
                    "name"=>"nro_de_respuestas",
                    "value"=>count($respuestas),
                    "type"=>"integer"
                ),
                array(
                    "name"=>"proyecto_url",
                    "value"=>$proyectoUrl,
                    "type"=>"string"
                ),
                array(
                    "name"=>"status",
                    "value"=>$status,
                    "type"=>"string"
                ),
                array(
                    "name"=>"color",
                    "value"=>is_null($valueProyecto->tipo->color_filtro) ? '':$valueProyecto->tipo->color_filtro,
                    "type"=>"string"
                )
            );
            $arrayComplete[] = array(
                'external_id' => $externalId,
                'fields' => $arrayFields
            );
        }
        if(is_array($arrayComplete) && count($arrayComplete) > 0){
            $countCreados = $swiftype->create_or_update_documents('dequesetrata', 'proyectos',$arrayComplete);
        }
        else{
            $countCreados = array('body'=>array());
        }

        die(var_dump(count($countCreados['body'])));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
