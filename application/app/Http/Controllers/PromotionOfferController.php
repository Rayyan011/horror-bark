<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use App\Services\PromotionOfferService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PromotionOfferController extends Controller
{
    public function show(Promotion $promotion, PromotionOfferService $promotionOfferService): View|RedirectResponse
    {
        abort_unless($promotion->isLive(), 404);

        $offer = $promotionOfferService->buildOffer($promotion);

        if (! $offer) {
            $fallbackUrl = $promotionOfferService->fallbackUrl($promotion);

            abort_unless($fallbackUrl, 404);

            return redirect($fallbackUrl);
        }

        return view('pages.promotions.show', [
            'promotion' => $promotion,
            'offer' => $offer,
        ]);
    }
}
