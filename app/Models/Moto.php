<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Moto extends Model
{
    protected $table = "moto";

    protected $guarded = ['created_at','updated_at'];

    public $timestamps = false;

    /**
     * Retourne les lieux qui sont des garages
     * @param $query
     * @return mixed
     */
    public function scopeGarage($query) {

        return $query->where($this->table.'.garage',true);

    }

    /**
     * Retourne les lieux qui vendent des pieces
     * @param $query
     * @return mixed
     */
    public function scopePieces($query) {

        return $query->where($this->table.'.pieces',true);

    }

    /**
     * Retourne les lieux qui vendent des accessoires
     * @param $query
     * @return mixed
     */
    public function scopeAccessoires($query) {

        return $query->where($this->table.'.accessoires',true);

    }

    /**
     * Retourne les lieux qui vendent des motos
     * @param $query
     * @return mixed
     */
    public function scopeMoto($query) {

        return $query->where($this->table.'.vente_moto',true);

    }

    /**
     * Retourne les lieux qui font de la personnalisation moto
     * @param $query
     * @return mixed
     */
    public function scopePersonnalisation($query) {

        return $query->where($this->table.'.personnalisation',true);

    }

    /**
     * Retourne les lieux qui vendent de l'huile moto
     * @param $query
     * @return mixed
     */
    public function scopeHuiles($query) {

        return $query->where($this->table.'.huiles',true);

    }


    public function scopeRandom($query ,$take) {

        return $query->orderBy(DB::raw('RAND()'))->take($take);

    }

    /**
     * The one to one relationship
     *
     * */
    public function lieu() {

        return $this->hasOne(Lieu::class,'id','lieu_id');

    }
    
    

}
