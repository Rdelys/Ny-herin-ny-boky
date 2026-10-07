<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\InvoiceGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class RenumberOrders extends Command
{
    protected $signature = 'orders:renumber
        {--dry-run : Affiche le plan sans rien modifier}
        {--sans-factures : Ne régénère pas les factures PDF}
        {--supprimer-anciennes : Supprime les anciens PDF après régénération}';

    protected $description = 'Convertit les anciennes références (CMD-… / PAN-…) au format NHB000001';

    public function handle(): int
    {
        // Tout ce qui n'est pas déjà au format NHB, du plus ancien au plus récent.
        $orders = Order::query()
            ->where(function ($q) {
                $q->whereNull('groupe_reference')->orWhere('groupe_reference', 'not like', 'NHB%');
            })
            ->orderBy('created_at')->orderBy('id')
            ->get();

        if ($orders->isEmpty()) {
            $this->info('Rien à renuméroter.');
            return self::SUCCESS;
        }

        // Un panier = un numéro. Une ancienne commande sans panier = son propre numéro.
        $groups = $orders->groupBy(fn (Order $o) => $o->groupe_reference ?? 'solo-' . $o->id);

        $n = (int) DB::table('sequences')->where('nom', 'commande')->value('valeur');
        $plan = [];

        foreach ($groups as $oldKey => $lines) {
            $n++;
            $plan[] = [
                'old' => str_starts_with($oldKey, 'solo-') ? null : $oldKey,
                'new' => 'NHB' . str_pad((string) $n, 6, '0', STR_PAD_LEFT),
                'lines' => $lines->sortBy('id')->values(),
            ];
        }

        $this->table(
            ['Ancien panier', 'Nouveau', 'Lignes', 'Date'],
            collect($plan)->take(25)->map(fn ($p) => [
                $p['old'] ?? '(sans panier : ' . $p['lines'][0]->reference . ')',
                $p['new'],
                $p['lines']->count(),
                $p['lines'][0]->created_at->format('d/m/Y'),
            ])->all()
        );
        $this->line(count($plan) . ' panier(s) à renuméroter, ' . $orders->count() . ' ligne(s).');

        if ($this->option('dry-run')) {
            $this->warn('Simulation : rien n\'a été modifié.');
            return self::SUCCESS;
        }

        if (! $this->confirm('Avez-vous fait une sauvegarde de la base ? Appliquer maintenant ?')) {
            return self::FAILURE;
        }

        DB::transaction(function () use ($plan, $n) {
            foreach ($plan as $p) {
                foreach ($p['lines'] as $i => $o) {
                    DB::table('orders')->where('id', $o->id)->update([
                        'groupe_reference' => $p['new'],
                        'reference' => $p['new'] . '-' . ($i + 1),
                    ]);
                }

                if ($p['old'] && Schema::hasTable('deliveries')) {
                    DB::table('deliveries')
                        ->where('groupe_reference', $p['old'])
                        ->update(['groupe_reference' => $p['new']]);
                }
            }

            // Les prochaines commandes continueront après le dernier numéro.
            DB::table('sequences')->where('nom', 'commande')->update(['valeur' => $n]);
        });

        $this->info('Références mises à jour.');

        if (! $this->option('sans-factures')) {
            $this->regenererFactures($plan);
        }

        return self::SUCCESS;
    }

    /** Régénère un PDF par panier sous le nouveau numéro (factures/NHB000001.pdf). */
    private function regenererFactures(array $plan): void
    {
        $bar = $this->output->createProgressBar(count($plan));

        foreach ($plan as $p) {
            $anciens = $p['lines']->pluck('facture_path')->filter()->unique();
            $premiere = Order::where('groupe_reference', $p['new'])->orderBy('id')->first();

            try {
                InvoiceGenerator::generate($premiere);

                if ($this->option('supprimer-anciennes')) {
                    foreach ($anciens as $fichier) {
                        Storage::disk('public')->delete($fichier);
                    }
                }
            } catch (\Throwable $e) {
                report($e);
                $this->warn(' Facture non régénérée pour ' . $p['new'] . ' : ' . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }
}