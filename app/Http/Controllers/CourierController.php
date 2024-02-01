<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Courier;
use DB;
use Validator;

class CourierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search=$request->search;
        $name=$request->name;
        $date=$request->date;
        $level=$request->level;

        //cek level
        if($level!=null){
            $level=explode (",",$level);
            $couriers=Courier::whereIn('level_courier', $level);
        }else{
            $couriers=Courier::select('*');
        }

        //cek search
        if($search!=null){
            $likes=explode (" ",$search);

            $query='';
            foreach($likes as $l){
                $query.='name_courier LIKE "%'.$l.'%" OR ';
            }
            $query=substr($query, 0, -3);
            $level=explode (",",$level);
            $couriers = $couriers->whereRaw("( ".$query." )");
        }

        //cek date and name sort
        if($name!=null && $date==null){
            $couriers = $couriers->orderBy('name_courier',$name);
        }else if($date!=null && $name==null){
            $couriers = $couriers->orderBy('created_at',$date);
        }else if($date!=null && $name!=null){
            $couriers = $couriers->orderBy('name_courier',$name)->orderBy('created_at',$date);
        }else{
            $couriers = $couriers;
        }

        $couriers=$couriers->paginate(3);
        return response()->json($couriers);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_courier' => 'required|unique:couriers',
            'jenis_courier' => 'required',
            'level_courier' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $courier = Courier::create($request->all());
        return response()->json(['status'=>'SUCCESS','message'=>'Data Courier berhasil di simpan','data'=>$courier]);
    }

    public function show()
    {
        $courier = Courier::all();
        return response()->json($courier);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name_courier' => 'unique:couriers,name_courier,'.$id.',id_courier'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $courier = Courier::find($id);
        $courier->update($request->all());
        return response()->json(['status'=>'SUCCESS','message'=>'Data Courier berhasil di ubah','data'=>$courier]);
    }

    public function destroy($id)
    {
        Courier::destroy($id);
        return response()->json(['message' => 'Courier deleted successfully']);
    }
}
