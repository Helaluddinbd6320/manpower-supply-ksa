<?php

namespace App\Filament\Concerns;

use Illuminate\Support\Facades\Auth;

trait HasRoleBasedVisibility
{
    /**
     * বর্তমান লগইন করা ইউজার sensitive financial ডাটা
     * (Billing Rate, Payout Rate, Margin) দেখার অনুমতি রাখে কিনা।
     */
    public static function canViewFinancialFields(): bool
    {
        return Auth::check() && Auth::user()->can('view billing rates');
    }

    /**
     * Worker Profile এডিট করার অনুমতি।
     */
    public static function canEditWorkerProfile(): bool
    {
        return Auth::check() && Auth::user()->can('edit workers');
    }

    /**
     * Placement/Billing ডাটা এডিট করার অনুমতি।
     */
    public static function canEditPlacementFinancials(): bool
    {
        return Auth::check() && Auth::user()->can('edit billing rates');
    }

    /**
     * ডিটেইলড মার্জিন/প্রফিট রিপোর্ট দেখার অনুমতি
     * (per-worker margin breakdown সহ)।
     */
    public static function canViewDetailedMargin(): bool
    {
        return Auth::check() && Auth::user()->can('view financial dashboard');
    }
}