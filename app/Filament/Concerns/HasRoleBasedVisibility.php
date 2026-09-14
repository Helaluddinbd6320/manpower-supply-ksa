<?php

namespace App\Filament\Concerns;

use Illuminate\Support\Facades\Auth;

trait HasRoleBasedVisibility
{
    /**
     * বর্তমান লগইন করা ইউজার sensitive financial ডাটা
     * (Billing Rate, Payout Rate, Margin) দেখার অনুমতি রাখে কিনা।
     * শুধুমাত্র Super Admin ও Accounts Staff।
     */
    public static function canViewFinancialFields(): bool
    {
        return Auth::check() && Auth::user()->hasAnyRole([
            'Super Admin',
            'Accounts Staff',
        ]);
    }

    /**
     * Worker Profile এডিট করার অনুমতি — Accounts Staff Worker Profile
     * এডিট করতে পারবে না (স্পেকের নিয়ম অনুযায়ী)।
     */
    public static function canEditWorkerProfile(): bool
    {
        return Auth::check() && Auth::user()->hasAnyRole([
            'Super Admin',
            'Manager',
            'Office Staff',
        ]);
    }

    /**
     * Placement/Billing ডাটা এডিট করার অনুমতি — Office Staff পারবে না।
     */
    public static function canEditPlacementFinancials(): bool
    {
        return Auth::check() && Auth::user()->hasAnyRole([
            'Super Admin',
            'Accounts Staff',
        ]);
    }

    /**
     * Manager-এর জন্য সীমিত মার্জিন/প্রফিট রিপোর্ট দেখার অনুমতি।
     * (Manager দেখতে পারবে কিন্তু ডিটেইলড margin breakdown না — যেমন
     * টোটাল সামারি দেখাবে, per-worker margin না)
     */
    public static function canViewDetailedMargin(): bool
    {
        return Auth::check() && Auth::user()->hasAnyRole([
            'Super Admin',
            'Accounts Staff',
        ]);
    }
}