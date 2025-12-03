<?php

namespace App\Http\Controllers;

use App\Models\UserCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PointsController extends Controller
{
    private const REWARDS = [
        ['key' => 'coupon_5', 'label' => 'Cupón de 5€', 'cost' => 50, 'value' => 5],
        ['key' => 'coupon_15', 'label' => 'Cupón de 15€', 'cost' => 100, 'value' => 15],
        ['key' => 'coupon_30', 'label' => 'Cupón de 30€', 'cost' => 150, 'value' => 30],
    ];

    // Muestra los puntos del usuario y las recompensas disponibles.
    public function index(Request $request)
    {
        return view('points.index', [
            'points' => $request->user()->points ?? 0,
            'rewards' => self::REWARDS,
        ]);
    }

    // Canjea una recompensa si hay puntos suficientes y genera el cupon.
    public function redeem(Request $request)
    {
        $request->validate([
            'reward' => ['required', 'in:'.collect(self::REWARDS)->pluck('key')->implode(',')],
        ]);

        $reward = collect(self::REWARDS)->firstWhere('key', $request->input('reward'));
        $user = $request->user();

        if (($user->points ?? 0) < $reward['cost']) {
            return back()->withErrors(['reward' => 'No tienes puntos suficientes para esta recompensa.']);
        }

        DB::transaction(function () use ($user, $reward) {
            $user->decrement('points', $reward['cost']);
            UserCoupon::create([
                'user_id' => $user->id,
                'label' => $reward['label'],
                'value' => $reward['value'],
            ]);
        });

        return back()->with('status', 'Has canjeado '.$reward['label']);
    }
}
