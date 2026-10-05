<?php
// app/Services/InvoiceGenerator.php

namespace App\Services;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Storage;

class InvoiceGenerator
{
    /**
     * - commande issue d'un panier (groupe_reference) : UNE facture pour toutes
     *   les lignes du panier, rattachée à chacune d'elles ;
     * - ancienne commande (sans groupe) : une facture par commande, comme avant.
     */
    public static function generate(Order $order): string
    {
        $orders = $order->groupe_reference
            ? Order::where('groupe_reference', $order->groupe_reference)->orderBy('id')->get()
            : new EloquentCollection([$order]);

        $orders->loadMissing(['buyer', 'seller.sellerProfile', 'delivery']);

        $numero = $order->groupe_reference ?? $order->reference;

        $pdf = Pdf::loadView('invoices.order', ['orders' => $orders, 'numero' => $numero])
            ->setPaper('a5', 'portrait');

        $path = 'factures/' . $numero . '.pdf';

        Storage::disk('public')->put($path, $pdf->output());

        Order::whereIn('id', $orders->modelKeys())->update(['facture_path' => $path]);

        $order->setAttribute('facture_path', $path);
        $order->syncOriginalAttribute('facture_path');

        return $path;
    }
}