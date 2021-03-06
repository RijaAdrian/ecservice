<?php 

namespace App\Repository;

use App\Models\Lieu;
use App\Models\Moto;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LieuRepository {
    
    /**
     * \App\Models\Lieu
     * @var model
     * */
    protected $model ;
    /**
     * \App\Models\Moto
     * @var Moto
     */
    private $moto;

    public function __construct(Lieu $lieu , Moto $moto)
   {
       $this->model = $lieu;
       $this->moto = $moto;
   }

    /**
     * Retourne une liste d'information au hasard
     * @param $limit integer limite de resultat
     * @return mixed
     */
    public function getByRandom($limit) {

        return $this->moto->random($limit);
    }

    /**
     * @return mixed
     */
    public function countAll() {
        return $this->moto->select(DB::raw('COUNT(*) as all'))->get();
    }

    /**
     * Retourne une liste de lieu au hasard
     * @param $limit
     * @return mixed
     */
    public function getPlaceRadomly($from,$limit = 10, $specification = []) {

        $specification = array_flip($specification);

        return $this->model
            ->join('moto','moto.lieu_id','=','lieu.id')
            ->when(isset($specification['garage']), function($q) {
                return $q->where('moto.garage',true);
            })->when(isset($specification['accessoires']), function($q) {
                return $q->where('moto.accessoires',true);
            })->when(isset($specification['pieces']), function($q) {
                return $q->where('moto.pieces',true);
            })->when(isset($specification['personnalisation']), function($q) {
                return $q->where('moto.personnalisation',true);
            })->when(isset($specification['moto']), function($q) {
                return $q->where('moto.vente_moto',true);
            })->when($limit , function($query) use($from,$limit) {
                return $query->offset($from)->limit($limit);
            },function($query) {
                return $query->take(10);
            })->get();
        
    }

    /**
     * @param int $limit
     * @return mixed
     */
    public function getRandom($limit = 10) {

        return $this->getByRandom($limit)->get();

    }

    /**
     * Retourne une liste de garage
     * @param int $limit
     * @return mixed
     */
    public function getRandomGarage($limit  = 10) {

        return $this->getByRandom($limit)->garage()->with('lieu')->get();

    }

    /**
     * Retourne une liste de vendeur de pieces
     * @param int $limit
     * @return mixed
     */
    public function getRandomPart($limit = 10) {

        return $this->getByRandom($limit)->pieces()->with('lieu')->get();

    }

    /**
     * Retourne une liste de vendeur de moto
     * @param int $limit
     * @return mixed
     */
    public function getRandomCycle($limit = 10) {

        return $this->getByRandom($limit)->moto()->with('lieu')->get();

    }

    /**
     * Retourne une liste de vendeur d'accessoire
     * @param int $limit
     * @return mixed
     */
    public function getRandomAccessory($limit = 10) {

        return $this->getByRandom($limit)->accessoires()->with('lieu')->get();

    }

    /**
     * Retourne une liste de vendeur d'huile
     * @param int $limit
     * @return mixed
     */
    public function getRandomOil($limit = 10) {

        return $this->getByRandom($limit)->huiles()->with('lieu')->get();

    }

    /**
     * Retourne une liste de personnalisateur
     * @param int $limit
     * @return mixed
     */
    public function getRandomPerso($limit = 10) {
        
        return $this->getByRandom($limit)->perso()->with('lieu')->get();
        
    }

    /**
     * Store new data
     * @param $input
     * @return mixed
     */
    public function store($input) {

        $place = new $this->model;

        $place->string_lieu = $input['name'];
        $place->fokontany_id = $input['fokontany'];
        $place->description = $input['description'];
        $place->longitude = $input['longitude'];
        $place->latitude = $input['latitude'];
        $place->commune_id = $input['commune'];
        $place->district_id = $input['district'];
        $place->region_id = $input['region'];
        $place->province_id = $input['province'];
        $place->address = $input['adresse'];
        $place->telephone = $input['telephone'];
        $place->moto = true;
        
        if($place->save())
            return $place;
        
    }

    /**
     * Retourne une liste de lieux
     * @return mixed
     */
    public function latest() {

        return $this->model->latest()->limit(5)->get();

    }

    /**
     * Retourne un lieu par l'ID
     * @param $id
     * @return mixed
     */
    public function getById($id) {

        return $this->model->where('id',$id);
    }

    /**
     * Supprime un lieu par l'ID
     * @param $id
     * @return mixed
     */
    public function deleteById($id) {

        $place = $this->model->where('id',$id)->first();
        return  [
            $place,
            $place->delete()
        ];
        
    }

    /**
     * Modifie un lieu via l'ID
     * @param $id
     * @param $param
     */
    public function updateById($id, $param) {

        $place = $this->getById($id);
        
        $param = collect($param);

        $newdata = $param->only(['name','description','longitude','latitude','fokontany','commune','district','region','province','telephone','adresse']);
        
        $newdata['string_lieu'] = $param->get('name');
        $newdata->forget('name');
        $newdata['fokontany_id'] = $param->get('fokontany');
        $newdata->forget('fokontany');
        $newdata['commune_id'] = $param->get('commune');
        $newdata->forget('commune');
        $newdata['region_id'] = $param->get('region');
        $newdata->forget('region');
        $newdata['province_id'] = $param->get('province');
        $newdata->forget('province');
        $newdata['district_id'] = $param->get('district');
        $newdata->forget('district');
        $newdata['telephone'] = $param->get('telephone');
        $newdata->forget('telephone');
        $newdata['address'] = $param->get('adresse');
        $newdata->forget('adresse');
        
        $place->update($newdata->toArray());
    }

    /**
     * Retourne une liste de lieu
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all() {
        return $this->model->all();
    }

    public function allPlaceWithNoConstraint() {
        return $this->model->whereMoto(true);
    }

    public function byName($name) {
        
        return $this->model->whereNom($name)->with('lieu')->get();
        
    }
    
    public function allWhere($param, $specified = [] , $d = .5, $entity = null) {

        $lat =  isset($param['lat']) ? $param['lat'] : $param->latitude;
        $lng =  isset($param['lng']) ? $param['lng'] : $param->longitude;
        $circonference = 40000;
        $deg = 111;
        $distance_longitude = $circonference * cos($deg) / 360;

        $latnegatif = $lat - ($d / $deg);
        $latpositif = $lat + ($d / $deg);

        $lngnegatif = $lng - ($d / $distance_longitude);
        $lngpositif = $lng + ($d / $distance_longitude);

        $specified = array_flip($specified);
        
        if(count($specified) != 0) {

            $long_query = $this->moto;

            return $long_query

                ->when(isset($specified['garage']) , function($query){
                    return $query->garage();
                })->when(isset($specified['pieces']) , function($query){
                    return $query->pieces();
                })->when(isset($specified['personnalisation']) , function($query){
                    return $query->personnalisation();
                })
                ->when(isset($specified['accessoires']) , function($query){
                    return $query->accessoires();
                })->when(isset($specified['vente_moto']) , function($query){
                    return $query->moto();
                })->when(isset($specified['huiles']) , function($query){
                    return $query->huiles();
                })
                ->whereBetween('lieu.latitude',[$latnegatif,$latpositif])
                ->whereBetween('lieu.longitude',[$lngpositif,$lngnegatif])

            ->join('lieu',function($joins) {
                    $joins
                        ->on('moto.lieu_id','=','lieu.id');
            })
            ->when($entity == "fokontany" , function($query) use ($param) {

                return $query->where('lieu.fokontany_id',$param->id);

            })
            ->when($entity == "district" , function($query) use ($param) {

                return $query->where('lieu.district_id',$param->id);

            })
            ->when($entity == "commune" , function($query) use ($param) {

                return $query->where('lieu.commune_id',$param->id);

            })
            ->when($entity == "region" , function($query) use ($param) {

                return $query->where('lieu.region_id',$param->id);

            })
            ->when($entity == "province" , function($query) use ($param) {

                return $query->where('lieu.province_id',$param->id);

            })
            ->take(10)
            ->get();

        } else {

            $long = $this->moto
                ->whereBetween('lieu.latitude',[$latnegatif,$latpositif])
                ->whereBetween('lieu.longitude',[$lngpositif,$lngnegatif]);

            return  $long
                ->join('lieu',function($joins) {
                    $joins
                        ->on('moto.lieu_id','=','lieu.id');
                })
                ->when($entity == "fokontany" , function($query) use ($param) {

                return $query->where('lieu.fokontany_id',$param->id);

            })
            ->when($entity == "district" , function($query) use ($param) {

                return $query->where('lieu.district_id',$param->id);

            })
            ->when($entity == "commune" , function($query) use ($param) {

                return $query->where('lieu.commune_id',$param->id);

            })
            ->when($entity == "region" , function($query) use ($param) {

                return $query->where('lieu.region_id',$param->id);

            })
            ->when($entity == "province" , function($query) use ($param) {

                return $query->where('lieu.province_id',$param->id);

            })
                ->take(10)
                ->get();

        }

    }


    public function allWhereByLimit($param, $specified = [] , $d = .5, $entity = null,$from = null, $limit = null ) {

        $specified = array_flip($specified);

        if(count($specified) != 0) {

            $long_query = $this->moto;

            return $long_query

                ->join('lieu',function($joins) {
                    $joins
                        ->on('moto.lieu_id','=','lieu.id');
                })
                ->when(isset($specified['garage']) , function($query){
                    return $query->garage();
                })->when(isset($specified['pieces']) , function($query){
                    return $query->pieces();
                })->when(isset($specified['personnalisation']) , function($query){
                    return $query->personnalisation();
                })
                ->when(isset($specified['accessoires']) , function($query){
                    return $query->accessoires();
                })->when(isset($specified['vente_moto']) , function($query){
                    return $query->moto();
                })->when(isset($specified['huiles']) , function($query){
                    return $query->huiles();
                })
                ->when($entity == "fokontany" , function($query) use ($param) {

                    return $query->where('lieu.fokontany_id',$param->id);

                })
                ->when($entity == "district" , function($query) use ($param) {

                    return $query->where('lieu.district_id',$param->id);

                })
                ->when($entity == "commune" , function($query) use ($param) {

                    return $query->where('lieu.commune_id',$param->id);

                })
                ->when($entity == "region" , function($query) use ($param) {

                    return $query->where('lieu.region_id',$param->id);

                })
                ->when($entity == "province" , function($query) use ($param) {

                    return $query->where('lieu.province_id',$param->id);

                })
                ->when(!is_null($from) , function($query) use($from,$limit) {
                    return $query->offset($from)->limit($limit);
                },function($query) {
                    return $query->take(10);
                })
                ->get();

        } else {

            $long = $this->moto;

            return  $long
                ->join('lieu',function($joins) {
                    $joins
                        ->on('moto.lieu_id','=','lieu.id');
                })
                ->when($entity == "fokontany" , function($query) use ($param) {

                    return $query->where('lieu.fokontany_id',$param->id);

                })
                ->when($entity == "district" , function($query) use ($param) {

                    return $query->where('lieu.district_id',$param->id);

                })
                ->when($entity == "commune" , function($query) use ($param) {

                    return $query->where('lieu.commune_id',$param->id);

                })
                ->when($entity == "region" , function($query) use ($param) {

                    return $query->where('lieu.region_id',$param->id);

                })
                ->when($entity == "province" , function($query) use ($param) {

                    return $query->where('lieu.province_id',$param->id);

                })
                ->when($from , function($query) use($from,$limit) {
                    return $query->offset($from)->limit($limit);
                },function($query) {
                    return $query->take(10);
                })
                ->get();

        }

    }
}