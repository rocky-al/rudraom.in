<?php
  
namespace App\Exports;
  
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
  
class Export implements FromCollection, WithHeadings
{   
    public function __construct($collection,$headings)
    {
        $this->collection = $collection;
        $this->headings = $headings;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //return User::select("id", "first_name", "email")->get();
        return $this->collection;
    }
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function headings(): array
    {
        //return ["ID", "Name", "Email"];
        return $this->headings;

    }
}