<?php

use App\Models\AmbassadorLegalStatus;
use Illuminate\Database\Seeder;

class AmbassadorLegalStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AmbassadorLegalStatus::create([
            'id' => AmbassadorLegalStatus::TYPE_UNKNOWN,
            'name' => 'Unknown'
        ]);
        AmbassadorLegalStatus::create([
            'id' => AmbassadorLegalStatus::TYPE_SELF_EMPLOYED,
            'name' => 'self-employed'
        ]);
        AmbassadorLegalStatus::create([
            'id' => AmbassadorLegalStatus::TYPE_LTD,
            'name' => 'ltd'
        ]);
        AmbassadorLegalStatus::create([
            'id' => AmbassadorLegalStatus::TYPE_PLC,
            'name' => 'plc'
        ]);
    }
}
