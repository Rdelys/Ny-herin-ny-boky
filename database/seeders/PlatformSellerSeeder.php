<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class PlatformSellerSeeder extends Seeder
{
    private const EMAIL = 'contact@nyherinnyboky.com';
    private const PASSWORD = 'ChangezMoi@2026'; // <-- à remplacer ; modifiable ensuite depuis le profil

    public function run(): void
    {
        $user = User::where('email', self::EMAIL)->first();

        if (! $user) {
            $user = new User([
                'name' => "Ny Herin'ny Boky",
                'email' => self::EMAIL,
                'password' => self::PASSWORD, // haché automatiquement (cast "hashed")
                'role' => 'vendeur',
            ]);
        }

        $user->is_platform = true;
        $user->save();

        $user->sellerProfile()->firstOrCreate([], [
            'nom_entreprise' => "Ny Herin'ny Boky",
            'localisation' => 'Antananarivo',
            'mode_paiement' => 'commission',
            'commission_status' => '10%',
        ]);
    }
}