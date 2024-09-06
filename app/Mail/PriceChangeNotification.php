<?php
namespace App\Mail;

use App\Models\Ad;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PriceChangeNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $ad;
    public $oldPrice;
    public $newPrice;

    public function __construct(Ad $ad, $oldPrice, $newPrice)
    {
        $this->ad = $ad;
        $this->oldPrice = $oldPrice;
        $this->newPrice = $newPrice;
    }

    public function build()
    {
        return $this->subject('Price Update for Your OLX Ad Subscription')
            ->view('emails.price_change');
    }
}
