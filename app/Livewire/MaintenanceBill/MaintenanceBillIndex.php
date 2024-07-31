<?php

namespace App\Livewire\MaintenanceBill;

use DateTime;
use App\Models\Member;
use App\Livewire\DatePicker;
use App\Models\Societies;
use Livewire\Component;
use Twilio\Rest\Client;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use App\Models\MaintenanceBill;
use App\Helpers\AmountHelper;
use App\Models\Payment;
use App\Models\Receipts;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MaintenanceBillIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    #[Title('Maintenance Bill - mySocietyERP')]
    public $society;
    public $societiesList;
    public $months;
    public $search;
    public $selected_society;
    public $selected_year;
    public $selected_month;
    public $members;
    public $selectedMembers = [];
    public $selectAll = false;
    public $amount;
    public $due_date;

    public $editingBillId = null;
    public $editPaymentStatus;
    public $editPaymentMode;
    public $editAdvance;
    public $isEditModalOpen = false;

    public $isModalOpen = false;
    public $selectedBillIndex;
    public $selectedBills = [];

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedMembers = $this->members->pluck('id')->toArray();
        } else {
            $this->selectedMembers = [];
        }
    }

    public function mount(Societies $society)
    {
        $this->society = $society;
        $this->societiesList = Societies::where('accountant_id', Auth::user()->id)->pluck('name', 'id');
        $this->months = $this->returnMonths();
        $this->members = collect();
        $this->selected_society = $society->id;
    }

    public function goBack()
    {
        return redirect('/accountant/manage/societies/' . $this->society->id . '/society-details');
    }

    public $editingBill;
    public $editName;
    public $editPaymentMethod;
    public $editAdvancePayment;
    public $editChequeNo;
    public $asdid;
    public $editRemark;

    public function openEditModal($billId)
    {
        $this->editingBill = MaintenanceBill::with('member.user')->findOrFail($billId);
        $this->editName = $this->editingBill->member->user->name;
        $this->editPaymentMethod = $this->editingBill->payment_mode_id;
        $this->asdid = $this->editingBill->id;
        $this->editRemark = $this->editingBill->remark;
        $this->editAdvancePayment = $this->editingBill->advance ? '1' : '0';
        $this->editChequeNo = $this->editingBill->payment ? $this->editingBill->payment->reference_no : '';
        $this->isModalOpen = true;
    }

    public function closeEditModal()
    {
        $this->isModalOpen = false;
        $this->resetEditFields();
    }

    public function resetEditFields()
    {
        $this->reset(['editingBill','editName', 'editPaymentMethod', 'editRemark', 'editChequeNo', 'editAdvancePayment']);
        // $this->editingBill = null;
        // $this->editName = '';
        // $this->editPaymentMethod = '';
        // $this->editRemark = '';
        // $this->editChequeNo = '';
        // $this->editAdvancePayment = '';
    }



    public function updateBill()
    {
        // Validate form inputs
        $validatedData = $this->validate([
            'editName' => 'required|string|max:255',
            'editPaymentMethod' => 'required',
            'editAdvancePayment' => 'required',
        ]);

        // Update member's user name
        $this->editingBill->member->user->update([
            'name' => $this->editName,
        ]);

        $this->editingBill->advance = $this->editAdvancePayment === '1' || $this->editAdvancePayment === 1;
        $this->editPaymentMethod = $this->editPaymentMethod;
        $this->editingBill->remark = $this->editRemark;


        // Use the amount from the maintenance bills table
        $amountToPay = $this->editingBill->amount ?? 0;

        // Check if a payment entry exists
        $payment = Payment::where('maintenance_bills_id', $this->editingBill->id)->first();

        // Check if a payment entry exists
        if (!$payment) {
            // Create a new payment entry
            $payment = new Payment();
            $payment->maintenance_bills_id = $this->editingBill->id;
            $payment->amount_paid = $amountToPay;
            $payment->payment_date = now();
            $payment->transaction_id = ''; // You may want to generate a unique transaction ID here
            $payment->save();
            // Create a new receipt
            if (in_array($this->editPaymentMethod, [1, 2])) {
                $payment->reference_no = $this->editChequeNo;
            } else {
                $payment->reference_no = ''; // Clear cheque number if payment method is not cheque and online
            }

            $receipt = new Receipts();
            $receipt->payment_id = $payment->id;
            $receipt->save();

            // Set the status to 1 (paid)
            $this->editingBill->status = 1;
        }
        if (in_array($this->editPaymentMethod, [1, 2])) { // Online or Cheque
            $payment->reference_no = $this->editChequeNo;
        } else { // Cash or any other method
            $payment->reference_no = null;
        }
        // Update existing payment entry
        $payment->update([
            'amount_paid' => $amountToPay,
            'payment_date' => now(),
            // 'reference_no' => $this->editPaymentMethod == 2 || $this->editPaymentMethod == 1 ? $this->editChequeNo : null, // Update cheque number
        ]);

        // Check if a receipt already exists for this payment
        $receipt = Receipts::where('payment_id', $payment->id)->first();
        if (!$receipt) {
            // Create a new receipt if it doesn't exist
            $receipt = new Receipts();
            $receipt->payment_id = $payment->id;
            $receipt->save();
        }

        // Set the status to 1 (paid) if it's not already
        $this->editingBill->status = 1;


        // Update maintenance bill details
        $this->editingBill->update([
            'payment_mode_id' => $this->editPaymentMethod,
            'advance' => $this->editAdvancePayment == '1',
            'status' => 1, // Ensure status is set to 1 (paid)
        ]);

        if (in_array($this->editPaymentMethod, [1, 2])) {
            $payment->reference_no = $this->editChequeNo;
        } else {
            $payment->reference_no = ''; // Clear cheque number if payment method is not cheque and online
        }

        // Save the changes
        $this->editingBill->save();

        // Close the edit modal after updating
        $this->closeEditModal();

        session()->flash('success', 'The bill has been successfully updated!');
    }


    public function returnMonths()
    {
        return [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];
    }

    public function updatedSelectedSociety()
    {
        $this->fetchMembers();
    }

    public function updatedSelectedYear()
    {
        $this->fetchMembers();
    }

    public function updatedSelectedMonth()
    {
        $this->fetchMembers();
    }


    public function fetchMembers()
    {
        if ($this->selected_society && $this->selected_year && $this->selected_month) {
            $this->members = Member::join('maintenance_bills', 'members.id', '=', 'maintenance_bills.member_id')
                ->join('users', 'members.user_id', '=', 'users.id')
                ->where('members.society_id', $this->selected_society)
                ->where('maintenance_bills.billing_year', $this->selected_year)
                ->where('maintenance_bills.billing_month', $this->selected_month)
                ->where(function ($query) {
                    $query->where('users.name', 'like', "%{$this->search}%")
                        ->orWhere('users.phone', 'like', "%{$this->search}%")
                        ->orWhere('users.email', 'like', "%{$this->search}%");
                })
                ->select(
                    'members.id as member_id',
                    'members.society_id',
                    'members.user_id',
                    'users.name',
                    'users.phone',
                    'users.email',
                    'maintenance_bills.id as bill_id',
                    'maintenance_bills.billing_month',
                    'maintenance_bills.amount',
                    'maintenance_bills.advance',
                    'maintenance_bills.status',
                    'members.created_at'
                )
                ->latest('members.created_at')
                ->get();
        } else {
            $this->members = collect();
        }
    }

    public function download($billId)
    {
        try {
            // Find the specific bill
            $bill = MaintenanceBill::findOrFail($billId);

            // Find the associated member
            $member = Member::with(['user', 'society'])->findOrFail($bill->member_id);

            // Get the current payment for this bill
            $currentPayment = Payment::where('maintenance_bills_id', $bill->id)->first();

            // Get the most recent previous payment for this member
            $previousPayment = Payment::where('maintenance_bills_id', '<>', $bill->id)
                ->whereHas('maintenanceBill', function ($query) use ($bill) {
                    $query->where('member_id', $bill->member_id);
                })
                ->latest('payment_date')
                ->first();
            $amountInWords = AmountHelper::amountToWords($currentPayment ? $currentPayment->amount_paid : $bill->amount);

            $data = [
                'member' => $member,
                'bill' => $bill,
                'society' => $member->society,
                'currentPayment' => $currentPayment,
                'previousPayment' => $previousPayment,
                'amountInWords' => $amountInWords,
                'payment_mode_id' => $bill->payment_mode_id,
                'reference_no' => $currentPayment ? $currentPayment->reference_no : null,
                'transaction_id' => $currentPayment ? $currentPayment->transaction_id : null,
            ];

            if ($bill->status == 1) {
                $pdf = Pdf::loadView('pdfs.receipt', $data);
                $filename = "receipt_{$bill->id}.pdf";
            } elseif ($bill->status == 0) {
                $pdf = Pdf::loadView('pdfs.invoice', $data);
                $filename = "invoice_{$bill->id}.pdf";
            } else {
                return response()->json(['error' => 'Invalid bill status'], 400);
            }

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->stream();
            }, $filename);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Bill or Member not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while processing your request'], 500);
        }
    }

    public function applyLateFees()
    {
        $today = now();
        $overdueBills = MaintenanceBill::where('status', 0)
            ->where('due_date', '<', $today->subDays(15))
            ->get();

        foreach ($overdueBills as $bill) {
            $society = Societies::find($bill->member->society_id);
            $lateFee = $society->late_fee;

            $bill->amount += $lateFee;
            $bill->late_fee_applied = true;
            $bill->save();
        }

        session()->flash('success', 'Late fees applied successfully!');
    }


    public function downloadSelected()
    {
        if (empty($this->selectedMembers)) {
            return;
        }

        $members = Member::with('user')->whereIn('id', $this->selectedMembers)->get();

        if ($members->isEmpty()) {
            return;
        }

        $society = Societies::find($members->first()->society_id);

        if (!$society) {
            return;
        }

        $bills = MaintenanceBill::whereIn('member_id', $this->selectedMembers)->get();

        $data = [
            'members' => $members,
            'bills' => $bills,
            'society' => $society,
        ];

        $pdf = Pdf::loadView('pdfs.invoices', $data);
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'invoices.pdf');
    }

    private function amountToWords($amount)
    {
        $ones = [
            1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four', 5 => 'five',
            6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine', 10 => 'ten',
            11 => 'eleven', 12 => 'twelve', 13 => 'thirteen', 14 => 'fourteen',
            15 => 'fifteen', 16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
            19 => 'nineteen'
        ];
        $tens = [
            2 => 'twenty', 3 => 'thirty', 4 => 'forty', 5 => 'fifty',
            6 => 'sixty', 7 => 'seventy', 8 => 'eighty', 9 => 'ninety'
        ];
        $scales = [
            '', 'thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion'
        ];

        if ($amount == 0) {
            return 'zero';
        }

        $amount = number_format($amount, 2, '.', '');
        $numberWords = [];
        $wholeNumber = floor($amount);
        $decimal = round(($amount - $wholeNumber) * 100);

        $groups = str_split(strrev(strval($wholeNumber)), 3);

        for ($i = 0; $i < count($groups); $i++) {
            $number = strrev($groups[$i]);
            if ($number != '000') {
                $groupWords = [];
                if ($number > 99) {
                    $groupWords[] = $ones[substr($number, 0, 1)] . ' hundred';
                    $number = substr($number, 1);
                }
                if ($number > 19) {
                    $groupWords[] = $tens[substr($number, 0, 1)];
                    $number = substr($number, 1);
                }
                if ($number > 0) {
                    $groupWords[] = $ones[$number];
                }
                $numberWords[] = implode(' ', $groupWords) . ($i > 0 ? ' ' . $scales[$i] : '');
            }
        }

        $wholeWords = implode(' ', array_reverse($numberWords));

        if ($decimal > 0) {
            return $wholeWords . ' and ' . $decimal . '/100';
        }

        return $wholeWords;
    }

    public function sendWhatsAppMessage($memberId)
    {
        $member = Member::with('user')->find($memberId);
        if (!$member) {
            return;
        }

        $bill = MaintenanceBill::where('member_id', $memberId)->first();
        if (!$bill) {
            return;
        }

        // Convert billing month number to month name
        $billingMonth = DateTime::createFromFormat('!m', $bill->billing_month)->format('F');

        // Adjust the message to use the month name
        $message = $bill->status
            ? "Dear {$member->user->name}, your maintenance bill for the period {$billingMonth} {$bill->billing_year} is paid. Your invoice number is {$bill->id}. Thank you!"
            : "Dear {$member->user->name}, your maintenance bill for the period {$billingMonth} {$bill->billing_year} is pending. Please pay by {$bill->due_date}. Your invoice number is {$bill->id}.";


        // Sending the WhatsApp message
        $this->sendWhatsApp($member->user->phone, $message);

        // Generate and attach the invoice PDF
        $data = [
            'member' => $member,
            'bill' => $bill,
            'society' => Societies::find($member->society_id),
        ];

        $pdf = Pdf::loadView('pdfs.invoice', $data);
        $filePath = storage_path('app/public/invoice-' . $bill->id . '.pdf');
        $pdf->save($filePath);

        // Send the PDF as an attachment
        $this->sendWhatsAppWithMedia($member->user->phone, $message, $filePath);

        // Dispatch an event to indicate that the message was sent
        $this->dispatch('whatsappMessageSent');
    }

    protected function sendWhatsApp($phone, $message)
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from = config('services.twilio.whatsapp_from');
        $to = 'whatsapp:' . $phone;

        $client = new Client($sid, $token);
        $client->messages->create($to, [
            'from' => $from,
            'body' => $message,
        ]);
    }

    protected function sendWhatsAppWithMedia($phone, $message, $filePath)
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from = config('services.twilio.whatsapp_from');
        $to = 'whatsapp:' . $phone;

        $client = new Client($sid, $token);
        $client->messages->create($to, [
            'from' => $from,
            'body' => $message,
            'mediaUrl' => [url('storage/invoice-' . basename($filePath))],
        ]);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }


    // ... other properties
    // public function generateBills()
    // {

    //     //   dd('generateBills called', $this->members,  $this->due_date, $this->selected_month, $this->selected_year);

    //     $this->validate([
    //         'amount' => 'required|numeric|min:0',
    //         'due_date' => 'required|date',
    //     ]);

    //     // dd('generateBills called', $this->members,  $this->due_date, $this->selected_month, $this->selected_year);
    //     $members = Member::All();
    //     try {
    //         foreach ($members as $member) {
    //             $society = Societies::find($member->society_id);
    //             $parkingCharges = $society->parking_charges;
    //             $servicesCharges = $society->services_charges;
    //             $maintenance_due_date = $society->maintenance_due_date;
    //             $maintenanceAmount = $member->isRented
    //                 ? $society->maintenance_amount_rented
    //                 : $society->maintenance_amount_owner;

    //             $amount = $parkingCharges + $servicesCharges + $maintenanceAmount;
    //             //dd('Amount:', $amount, 'Society ID:', $society->id);

    //             MaintenanceBill::create([
    //                 'member_id' => $member->id,
    //                 'amount' => $amount,
    //                 'status' => 0,
    //                 'due_date' => $maintenance_due_date,
    //                 'billing_month' => $this->selected_month,
    //                 'billing_year' => $this->selected_year,
    //             ]);
    //         }
    //         session()->flash('success', 'Post Created Successfully!!');
    //     } catch (\Exception $ex) {
    //         session()->flash('error', 'Something goes wrong!!');
    //     }


    //     $this->fetchMembers();

    //     // for temp uses only
    //     // TODO -> fix redirect
    //     return redirect()->to('accountant/manage/societies/1/society-details/bills/maintenance-bill');
    // }

    public function generateBills()
    {
        $this->validate([
            'due_date' => 'required|date',
    
        ]);

        try {
            $members = Member::where('society_id', $this->selected_society)->get();
            $society = Societies::findOrFail($this->selected_society);

            foreach ($members as $member) {
                $parkingCharges = $society->parking_charges;
                $servicesCharges = $society->services_charges;
                $maintenanceAmount = $member->isRented
                    ? $society->maintenance_amount_rented
                    : $society->maintenance_amount_owner;

                $amount = $parkingCharges + $servicesCharges + $maintenanceAmount;

                MaintenanceBill::create([
                    'member_id' => $member->id,
                    'amount' => $amount,
                    'status' => 0,
                    'due_date' => $this->due_date,
                    'billing_month' => $this->selected_month,
                    'billing_year' => $this->selected_year,
                ]);
            }

            session()->flash('success', 'Bills generated successfully!');
            $this->fetchMembers(); // Refresh the members list
        } catch (\Exception $ex) {
            session()->flash('error', 'Error generating bills: ' . $ex->getMessage());
        }
    }


    public function render()
    {
        $this->fetchMembers();
        $members = Member::paginate(5); // This converts it to a collection

        $bills = MaintenanceBill::with('member.user')->get();
        return view('livewire.maintenance-bill.maintenance-bill-index', [
            'months' => $this->months,
            'members' => $this->members,
            'members' => $members,
            'bills' => $bills,
            'currentSociety' => $this->society,
        ])->layout('layouts.app', ['society' => $this->society]);
    }
}
