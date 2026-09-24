<?php
// app/Services/InvoiceGenerator.php

namespace App\Services;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoiceGenerator
{
    public static function generate(Order $order): string
    {
        $pdf = Pdf::loadView('invoices.order', ['order' => $order])
            ->setPaper('a5', 'portrait');

        $path = 'factures/' . $order->reference . '.pdf';

        Storage::disk('public')->put($path, $pdf->output());

        $order->update(['facture_path' => $path]);

        return $path;
    }
}