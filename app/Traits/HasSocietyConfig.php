<?php

namespace App\Traits;

use App\Models\SystemConfig;
use BaconQrCode\Encoder\QrCode;
use Vinkla\Hashids\Facades\Hashids;

trait HasSocietyConfig
{
    private function societyConfig(): array
    {
        return [
        'name'           => \App\Models\SystemConfig::get('society_name',        'Zamar Valley'),
        'tagline'        => \App\Models\SystemConfig::get('society_tagline',     'Premium Residential Society'),
        'phone'          => \App\Models\SystemConfig::get('society_phone',       ''),
        'phone2'         => \App\Models\SystemConfig::get('society_phone2',      ''),
        'phone3'         => \App\Models\SystemConfig::get('society_phone3',      ''),
        'email'          => \App\Models\SystemConfig::get('society_email',       ''),
        'address'        => \App\Models\SystemConfig::get('society_address',     ''),
        'logo'           => $this->logoBase64(),
        'show_logo'      => \App\Models\SystemConfig::get('show_logo_on_receipt', '1') === '1',
        'watermark'      => \App\Models\SystemConfig::get('doc_watermark_text',  ''),
        'receipt_footer' => \App\Models\SystemConfig::get('receipt_footer_note', ''),
    ];
    }

    private function logoBase64(): ?string
{
    $logo = \App\Models\SystemConfig::get('society_logo', null);
    if (!$logo) return null;

    $path = storage_path('app/public/' . ltrim($logo, '/'));
    if (!file_exists($path)) return null;

    $mime = mime_content_type($path);
    $data = base64_encode(file_get_contents($path));

    return "data:{$mime};base64,{$data}";
}

    private function generateQrCode(int $bookingId): ?string
    {
        try {
            $url = route('downloadPDF', Hashids::encode($bookingId));
            return base64_encode(QrCode::format('svg')->size(200)->generate($url));
        } catch (\Exception $e) {
            return null;
        }
    }
}
