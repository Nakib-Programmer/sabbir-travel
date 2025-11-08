<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard(){
        $withoutInvoice = $this->countPatientsWithoutInvoice();
        $countInvoices = $this->countInvoices();
        $dueCount = $countInvoices['due_count'];
        $paidCount = $countInvoices['paid_count'];
        return view('dashboard', compact('withoutInvoice','dueCount','paidCount'));
    }
    public function countPatientsWithoutInvoice(){
        $userId = Auth::user();  // Get the authenticated user's ID

        $query = DB::table('patients')
            ->leftJoin('invoices', 'patients.id', '=', 'invoices.patient_id')
            ->whereNull('invoices.patient_id');  // No matching invoice

        // If the user is not admin (ID 1), filter by user_id
        if ($userId->type !== 1) {
            $query->where('patients.user_id', $userId->id);
        }

        $count = $query->count();

        return $count;
    }
    public function countInvoices(){
        $user = Auth::user();  // Get the authenticated user

        $query = DB::table('invoices')
            ->join('patients', 'invoices.patient_id', '=', 'patients.id'); // Join with the patients table

        // Check if the user is not an admin (type !== 1)
        if ($user->type !== 1) {
            $query->where('patients.user_id', $user->id); // Filter by the patient’s user_id for non-admins
        }

        $counts = $query
            ->selectRaw("
                COUNT(CASE WHEN due > 0 THEN 1 END) as due_count,
                COUNT(CASE WHEN due = 0 AND paid > 0 THEN 1 END) as paid_count
            ")
            ->first();

        return [
            'due_count' => $counts->due_count,
            'paid_count' => $counts->paid_count,
        ];
    }
}
