<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Medicin;
use App\Models\User;
use App\Models\Admin;
class DeleteExpiredMedicins extends Command
{

    protected $signature = 'medicins:delete-expired-medicins';


    protected $description = 'delete-expired-medicins';

    public function __construct(){
        parent::__construct();
    }


    public function handle()
    {
        $expiredMedicins= Medicin::whereDate('Expiry_data','<' , '2023-01-01')->get();
         foreach ($expiredMedicins as $medicin) {
           $medicin->delete();

         }
    }
}
