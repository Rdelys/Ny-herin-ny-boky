<?php

namespace Database\Seeders;

use App\Models\DeliveryQuartier;
use App\Models\DeliveryZone;
use Illuminate\Database\Seeder;

class DeliverySeeder extends Seeder
{
    public function run(): void
    {
        // nom => [capitale, frais, délai min h, délai max h]  (valeurs d'exemple)
        $zones = [
            'Antananarivo' => [true, 0, 24, 72],
            'Toamasina' => [false, 5000, 48, 96],
            'Fianarantsoa' => [false, 5000, 48, 96],
            'Mahajanga' => [false, 5000, 48, 96],
            'Toliara' => [false, 5000, 72, 120],
            'Antsiranana' => [false, 5000, 72, 120],
            'Manakara' => [false, 5000, 72, 120],
            'Nosy Be' => [false, 8000, 72, 144],
            'Sainte-Marie' => [false, 8000, 72, 144],
        ];

        $position = 0;
        foreach ($zones as $nom => [$capitale, $frais, $min, $max]) {
            DeliveryZone::firstOrCreate(['nom' => $nom], [
                'est_capitale' => $capitale, 'frais' => $frais,
                'delai_min_h' => $min, 'delai_max_h' => $max,
                'actif' => true, 'position' => $position++,
            ]);
        }

        $tana = DeliveryZone::where('est_capitale', true)->first();

        $quartiers = [
            'Analakely' => 3000, 'Isoraka' => 3000, 'Andohalo' => 3000, 'Behoririka' => 3000,
            'Tsaralalana' => 3000, 'Ampefiloha' => 3500, '67 Ha' => 3500, 'Isotry' => 3500,
            'Ankorondrano' => 4000, 'Mahamasina' => 4000, 'Ivandry' => 4500,
            'Ambohimanarina' => 5000, 'Itaosy' => 6000, 'Andoharanofotsy' => 6000,
        ];

        foreach ($quartiers as $nom => $frais) {
            DeliveryQuartier::firstOrCreate(['zone_id' => $tana->id, 'nom' => $nom], ['frais' => $frais]);
        }
    }
}