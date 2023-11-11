<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Oferta;
use App\Models\Postulacion;
use App\Models\Estudiante;
use App\Models\Facultad;
use App\Models\Carrera;


use Livewire\WithFileUploads;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class OfertasEstudiantes extends Component
{
    use WithPagination;
	use WithFileUploads;

	protected $paginationTheme = 'bootstrap';
    public $selected_id, $keyWord,  $resumenPuesto, $nombrePuesto,$responsabilidadesPuesto,$requisitosEducativos, $experienciaLaboral, $sueldoMax, $sueldoMinimo, $jornadaLaboral, $condicionesLaborales, $beneficios, $oportunidadesDesarrollo, $fechaMax, $imagenPuesto, $cantVacantes, $modalidadTrabajo, $edadRequerida, $generoRequerido, $comentarioCierre, $empresa_id, $facultad_id;
	public $fechaPostulacion, $oferta_id;
	public $user_id, $user, $mensaje, $estudiante, $ofertas;
	public $perfil, $postulacionExistente;

    public function mount()
    {
        $this->fechaPostulacion = Carbon::now()->toDate()->format('Y-m-d');
		
    }

    public function render()
    {
        $this->Postulaciones = Postulacion::all();
	   $this->user_id = Auth::id();
	   $this->user = User::find($this->user_id); 
	   if ($this->user && $this->user->Estudiante){
			$this->mensaje = "El usuario si tiene una propiedad Estudiante";
			$this->estudiante = $this->user->Estudiante;
    
			if ($this->estudiante->Carrera && $this->estudiante->Carrera->Facultad) {
				$this->ofertas = $this->estudiante->Carrera->Facultad->Ofertas;
				
        $keyWord = '%'.$this->keyWord .'%';
        return view('livewire.ofertasestudiantes.ofertas-estudiantes', [
            'ofertasestudiantes' => Oferta::latest()
									->orWhere('resumenPuesto', 'LIKE', $keyWord)
									->orWhere('nombrePuesto', 'LIKE', $keyWord)
									->orWhere('imagenPuesto', 'LIKE', $keyWord)
									->orWhere('sueldoMinimo', 'LIKE', $keyWord)
									->orWhere('fechaMax', 'LIKE', $keyWord)
									->orWhere('cantVacantes', 'LIKE', $keyWord)
									->orWhere('modalidadTrabajo', 'LIKE', $keyWord)
									->orWhere('edadRequerida', 'LIKE', $keyWord)
									->orWhere('generoRequerido', 'LIKE', $keyWord)
									->orWhere('comentarioCierre', 'LIKE', $keyWord)
									->orWhere('sueldoMax', 'LIKE', $keyWord)
									->orWhere('empresa_id', 'LIKE', $keyWord)
									->orWhere('facultad_id', 'LIKE', $keyWord)
									->paginate(10),
									'user_id' => $this->user_id, 'mensaje' => $this->mensaje, 'ofertas' => $this->ofertas] );

		}
	   } else {
		$this->mensaje = "No existe el usuario o no es un estudiante";
	   };
		
    }

   
	/*

	public function mostrarOferta($ofertaId)
    {
        $record = Oferta::findOrFail($ofertaId);
        $this->selected_id = $ofertaId; 
		$this->resumenPuesto = $record-> resumenPuesto;
		$this->nombrePuesto = $record-> nombrePuesto;
		$this->responsabilidadesPuesto = $record-> responsabilidadesPuesto;
		$this->requisitosEducativos = $record-> requisitosEducativos;
		$this->experienciaLaboral = $record-> experienciaLaboral;
		$this->sueldoMax = $record-> sueldoMax;
		$this->sueldoMinimo = $record-> sueldoMinimo;
		$this->jornadaLaboral = $record-> jornadaLaboral;
		$this->condicionesLaborales = $record -> condicionesLaborales;
		$this->beneficios = $record -> beneficios;
		$this->oportunidadesDesarrollo = $record -> oportunidadesDesarrollo;
		$this->fechaMax = $record-> fechaMax;
		$this->imagenPuesto = $record-> imagenPuesto;
		$this->cantVacantes = $record-> cantVacantes;
		$this->modalidadTrabajo = $record-> modalidadTrabajo;
		$this->edadRequerida = $record-> edadRequerida;
		$this->generoRequerido = $record-> generoRequerido;
		$this->comentarioCierre = $record-> comentarioCierre;
		$this->empresa_id = $record-> empresa_id;
		$this->facultad_id = $record-> facultad_id;
    }
	*/
    
	//FUNCION PARA OBTNER EL ID DESDE LA TABLA OFERTA
	public function setOfertaId($ofertaId)
    {
        $this->oferta_id = $ofertaId;
    }
	
	//FUNCION PARA CREAR LA POSTULACION
	public function postular(){
        $this->perfil = auth()->user()->Estudiante;

		if ($this->perfil) {
            $this->postulacionExistente = Postulacion::where('oferta_id', $this->oferta_id)
                ->where('estudiante_id', $this->perfil->estudianteId)
                ->exists();

            if ($this->postulacionExistente) {
                session()->flash('message', 'Ya te has postulado previamente a esta oferta.');
            } else {
					Postulacion::create([
						'fechaPostulacion' =>  $this->fechaPostulacion,
						'oferta_id' => $this->oferta_id,
						'estudiante_id' => $this->perfil->estudianteId,
					]);
					$this->dispatchBrowserEvent('closeModal');
					session()->flash('message', 'Te has postulado exitosamente a esta oferta.');
				}
			} else {
				session()->flash('message', 'No se encontró un perfil asociado al usuario.');
			}
	}
}
