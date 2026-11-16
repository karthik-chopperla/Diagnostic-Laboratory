<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Corporate;
use App\Models\Coupon;
use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Test;
use App\Models\TestPackage;
use App\Services\LabAudit;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $invoices = Invoice::with(['branch', 'patient', 'doctor', 'corporate', 'items', 'payments'])
            ->when($status, fn ($q) => $q->where('payment_status', $status))
            ->latest()
            ->paginate(15);

        $patients = Patient::where('is_merged', false)->orderBy('name')->get();
        $doctors = Doctor::where('status', 'Active')->orderBy('name')->get();
        $branches = Branch::where('is_active', true)->get();
        $corporates = Corporate::where('status', 'Active')->get();
        $tests = Test::where('is_active', true)->orderBy('name')->get();
        $packages = TestPackage::where('is_active', true)->orderBy('name')->get();
        $coupons = Coupon::where('is_active', true)->get();

        $summary = [
            'today_cash' => Payment::whereDate('paid_at', today())->where('payment_method', 'Cash')->sum('amount'),
            'today_upi_card' => Payment::whereDate('paid_at', today())->whereIn('payment_method', ['UPI', 'Card', 'Bank Transfer'])->sum('amount'),
            'outstanding' => Invoice::whereIn('payment_status', ['Unpaid', 'Partial'])->sum('balance_amount'),
            'branch_revenue' => Branch::withSum('payments', 'amount')->get(),
        ];

        return view('billing.index', compact(
            'invoices', 'patients', 'doctors', 'branches', 'corporates',
            'tests', 'packages', 'coupons', 'summary', 'status'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'nullable|exists:doctors,id',
            'corporate_id' => 'nullable|exists:corporates,id',
            'counter_name' => 'required|string',
            'test_ids' => 'nullable|array',
            'package_ids' => 'nullable|array',
            'coupon_code' => 'nullable|string',
            'discount_amount' => 'nullable|numeric|min:0',
            'gst_percent' => 'nullable|numeric|min:0|max:28',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:Cash,UPI,Card,Bank Transfer,Advance',
        ]);

        $subtotal = 0;
        $lineItems = [];

        foreach ($data['test_ids'] ?? [] as $testId) {
            if ($t = Test::find($testId)) {
                $subtotal += $t->price;
                $lineItems[] = ['test_id' => $t->id, 'item_name' => $t->name, 'item_type' => 'Test', 'quantity' => 1, 'unit_price' => $t->price, 'total_price' => $t->price];
            }
        }
        foreach ($data['package_ids'] ?? [] as $pkgId) {
            if ($pk = TestPackage::find($pkgId)) {
                $subtotal += $pk->final_price;
                $lineItems[] = ['test_package_id' => $pk->id, 'item_name' => $pk->name, 'item_type' => 'Package', 'quantity' => 1, 'unit_price' => $pk->final_price, 'total_price' => $pk->final_price];
            }
        }

        if (empty($lineItems)) {
            return back()->with('error', 'Please select at least one test or package.');
        }

        $discount = (float) ($data['discount_amount'] ?? 0);
        if (!empty($data['coupon_code'])) {
            $coupon = Coupon::where('code', strtoupper($data['coupon_code']))->where('is_active', true)->first();
            if ($coupon && $subtotal >= $coupon->min_amount) {
                $discount += $coupon->discount_type === 'percent' ? ($subtotal * $coupon->discount_value / 100) : $coupon->discount_value;
            }
        }
        $discount = min($discount, $subtotal);
        $taxable = $subtotal - $discount;
        $gstPercent = (float) ($data['gst_percent'] ?? 0);
        $gstAmount = round($taxable * $gstPercent / 100, 2);
        $total = round($taxable + $gstAmount, 2);
        $paid = min((float) ($data['paid_amount'] ?? 0), $total);
        $balance = max(0, $total - $paid);
        $status = $paid >= $total ? 'Paid' : ($paid > 0 ? 'Partial' : 'Unpaid');

        $nextId = (Invoice::max('id') ?? 0) + 1;
        $invoice = Invoice::create([
            'branch_id' => $data['branch_id'],
            'patient_id' => $data['patient_id'],
            'doctor_id' => $data['doctor_id'] ?? null,
            'corporate_id' => $data['corporate_id'] ?? null,
            'invoice_no' => 'INV-' . date('Y') . '-' . str_pad((string) $nextId, 4, '0', STR_PAD_LEFT),
            'counter_name' => $data['counter_name'],
            'subtotal' => $subtotal,
            'discount_amount' => $discount,
            'coupon_code' => $data['coupon_code'] ?? null,
            'gst_percent' => $gstPercent,
            'gst_amount' => $gstAmount,
            'total_amount' => $total,
            'paid_amount' => $paid,
            'balance_amount' => $balance,
            'payment_status' => $status,
            'invoice_type' => !empty($data['corporate_id']) ? 'Corporate' : 'Retail',
        ]);

        foreach ($lineItems as $item) {
            InvoiceItem::create(array_merge($item, ['invoice_id' => $invoice->id]));
        }

        if ($paid > 0) {
            Payment::create([
                'invoice_id' => $invoice->id,
                'branch_id' => $invoice->branch_id,
                'receipt_no' => 'RCP-' . date('Y') . '-' . str_pad((string) ((Payment::max('id') ?? 0) + 1), 4, '0', STR_PAD_LEFT),
                'amount' => $paid,
                'payment_method' => $data['payment_method'],
                'payment_type' => 'Payment',
                'paid_at' => now(),
            ]);
        }

        LabAudit::log('Created Invoice', 'Billing & Invoicing', $invoice->invoice_no, "Total: ₹{$total}, Paid: ₹{$paid}");

        return redirect()->route('billing.show', $invoice)->with('success', "Invoice {$invoice->invoice_no} generated.");
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['branch', 'patient', 'doctor', 'corporate', 'items', 'payments']);
        return view('billing.show', compact('invoice'));
    }

    public function recordPayment(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:Cash,UPI,Card,Bank Transfer,Advance',
            'payment_type' => 'required|in:Payment,Refund,Credit Note',
            'transaction_ref' => 'nullable|string',
        ]);

        Payment::create([
            'invoice_id' => $invoice->id,
            'branch_id' => $invoice->branch_id,
            'receipt_no' => 'RCP-' . date('Y') . '-' . str_pad((string) ((Payment::max('id') ?? 0) + 1), 4, '0', STR_PAD_LEFT),
            'amount' => $data['amount'],
            'payment_method' => $data['payment_method'],
            'payment_type' => $data['payment_type'],
            'transaction_ref' => $data['transaction_ref'] ?? null,
            'paid_at' => now(),
        ]);

        if ($data['payment_type'] === 'Payment') {
            $invoice->paid_amount += $data['amount'];
        } else {
            $invoice->paid_amount = max(0, $invoice->paid_amount - $data['amount']);
        }
        $invoice->balance_amount = max(0, $invoice->total_amount - $invoice->paid_amount);
        $invoice->payment_status = $data['payment_type'] === 'Refund' ? 'Refunded' : ($invoice->balance_amount <= 0 ? 'Paid' : 'Partial');
        $invoice->save();

        LabAudit::log('Recorded ' . $data['payment_type'], 'Billing & Invoicing', $invoice->invoice_no, "Amount: ₹{$data['amount']} via {$data['payment_method']}");

        return back()->with('success', "{$data['payment_type']} of ₹{$data['amount']} recorded.");
    }
}

