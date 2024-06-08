<?php

namespace App\Livewire\Societies;

use Livewire\Component;
use App\Models\Societies;
use Illuminate\Http\Request;

use Ixudra\Curl\Facades\Curl;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;
use Rap2hpoutre\FastExcel\Facades\FastExcel;

class ManageSocietiesIndex extends Component
{
    use WithFileUploads;


    #[Title('Manage societies - mySocietyERP')]
    public $societyOptions;

    #[Validate('required')]
    public $name = '';

    #[Validate('required')]
    public $phone = '';

    #[Validate('required')]
    public $address = '';

    #[Validate('required')]
    public $bank_name = '';

    #[Validate('required')]
    public $bank_ifsc_code = '';

    #[Validate('required')]
    public $bank_account_number = '';

    #[Validate('required|integer')]
    public $member_count = '';

    #[Validate('required')]
    public $accountant_id = '';

    public $president_name = '';
    public $vice_president_name = '';
    public $treasurer_name = '';
    public $secretary_name = '';

    public $upload;
    public $s_id;

    public function mount()
    {
        $this->accountant_id = Auth::user()->id;
    }

    public function import()
    {
        $this->upload->store('excel', 'files');
    }


    /* $societies = (new FastExcel)->import(storage_path('app/files/'.'excel.xlsx'), function ($line) {
               return Society::create([
                   'name' => $line['name'],
                   'phone' => $line['phone'],
                   'address' => $line['address'],
                   'bank_name' => $line['bank_name'],
                   'bank_ifsc_code' => $line['bank_ifsc_code'],
                   'bank_account_number' => $line['bank_account_number'],
                   'member_count' => $line['member_count'],
                   'accountant_id' => $line['accountant_id'],
               ]);
           }); */

    // ...

    public function acceptPayment()
    {
        $data = [
            "merchantId" => "PGTESTPAYUAT",
            "merchantTransactionId" => "MT7850590068188104",
            "merchantUserId" => "MUID123",
            "amount" => $this->member_count * 120,
            "redirectUrl" => route('societies'),
            "redirectMode" => "REDIRECT",
            "callbackUrl" => 'https://webhook.site/callback-url',
            "mobileNumber" => "9999999999",
            "paymentInstrument" => [
                "type" => "PAY_PAGE"
            ]
        ];

        $encode = base64_encode(json_encode($data));

        $saltKey = '099eb0cd-02cf-4e2a-8aca-3e6c6aff0399';
        $saltIndex = 1;

        $string = $encode . '/pg/v1/pay' . $saltKey;
        $sha256 = hash('sha256', $string);

        $finalXHeader = $sha256 . '###' . $saltIndex;

        $url = "https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/pay";

        $response = Curl::to($url)
            ->withHeader('Content-Type:application/json')
            ->withHeader('X-VERIFY:' . $finalXHeader)
            ->withData(json_encode(['request' => $encode]))
            ->post();

        $rData = json_decode($response);

        if (isset($rData->data->instrumentResponse->redirectInfo->url)) {
            // Dispatch an event with the payment data
            $this->dispatch('paymentInitiated', ['paymentData' => $rData]);

            $redirectUrl = $rData->data->instrumentResponse->redirectInfo->url;
            return redirect()->to($redirectUrl);
        } else {
            return redirect()->route('societies')->with('error', 'Payment initialization failed.');
        }
    }

    protected $listeners = ['paymentInitiated' => 'checkPaymentStatus'];

    public function checkPaymentStatus($paymentStatus)
    {
        // Check if the payment was successful
        if ($paymentStatus['success'] == true && $paymentStatus['code'] == 'PAYMENT_SUCCESS') {
            // If the payment was successful, save the data
            $this->save();
            return redirect()->route('societies')->with('success', 'Payment successful and data saved.');
        } else {
            // If the payment was not successful, redirect with an error message
            return redirect()->route('societies')->with('error', 'Payment failed or unsuccessful.');
        }
    }

    public $update = false;

    public function updateSociety($id)
    {
        $society = societies::find($id);
        $this->s_id = $society->id;
        $this->name = $society->name;
        $this->phone = $society->phone;
        $this->address = $society->address;
        $this->bank_name = $society->bank_name;
        $this->bank_ifsc_code = $society->bank_ifsc_code;
        $this->bank_account_number = $society->bank_account_number;
        $this->member_count = $society->member_count;
        $this->president_name = $society->president_name;
        $this->vice_president_name = $society->vice_president_name;
        $this->secretary_name = $society->secretary_name;
        $this->treasurer_name = $society->treasurer_name;

        $this->update = true;
    }

    public function upData()
    {
        // Assuming you have a Society model
        $society = Societies::findOrFail($this->s_id);
        $society->name = $this->name;
        $society->phone = $this->phone;
        $society->address = $this->address;
        $society->bank_name = $this->bank_name;
        $society->bank_ifsc_code = $this->bank_ifsc_code;
        $society->bank_account_number = $this->bank_account_number;
        $society->member_count = $this->member_count;
        $society->accountant_id = Auth::user()->id;
        $society->secretary_name = $this->secretary_name;
        $society->president_name = $this->president_name;
        $society->vice_president_name = $this->vice_president_name;
        $society->treasurer_name = $this->treasurer_name;
        // Update other properties as needed

        $society->save();

        $this->reset(['name', 'phone', 'address', 'bank_name', 'bank_ifsc_code', 'bank_account_number', 'member_count', 'president_name', 'vice_president_name', 'secretary_name', 'treasurer_name']);
        $this->update = false;
    }

    // public function submit(){

    //     $this->validate([
    //         'name' => 'required',
    //         'phone' => 'required',
    //         'address' => 'required',
    //         'member_count' => 'required',
    //         'bank_name' => 'required',
    //         'bank_ifsc_code' => 'required',
    //         'bank_account_number' => 'required',
    //         'president_name' => 'required',
    //         'vice_president_name' => 'required',
    //         'secretary_name' => 'required',
    //         'treasurer_name' => 'required',
    //     ]);
    //     $socities = new Societies();
    //     $socities->name = $this->name;
    //     $socities->phone = $this->phone;
    //     $socities->address = $this->address;
    //     $socities->member_count = $this->member_count;
    //     $socities->bank_name = $this->bank_name;
    //     $socities->bank_ifsc_code = $this->bank_ifsc_code;
    //     $socities->bank_account_number = $this->bank_account_number;
    //     $socities->accountant_id = Auth::user()->id;
    //     $socities->president_name = $this->president_name;
    //     $socities->vice_president_name = $this->vice_president_name;
    //     $socities->secretary_name = $this->secretary_name;
    //     $socities->treasurer_name = $this->treasurer_name;
    //     $socities->save();
    // }

    // public function resetFilters(){
    //     $this->reset(['name', 'phone', 'address', 'member_count', 'bank_name', 'bank_ifsc_code', 'bank_account_number', 'president_name', 'vice_president_name', 'secretary_name', 'treasurer_name']);
    // }

    public function save()
    {

        $this->validate();


        Societies::create($this->only([
            'name',
            'phone',
            'address',
            'member_count',
            'bank_name',
            'bank_ifsc_code',
            'bank_account_number',
            'accountant_id',
        ]));


        return redirect('/accountant/manage/societies')->with([
            'button' => 'Create new user',
            'success' => 'Society saved'
        ]);
    }

    // public function edit($id)
    // {
    //     $data = Societies::find($id);
    //     return view('livewire.societies.manage-societies-edit', [
    //         'data' => $data
    //     ]);
    // }

    public function render()

    {
        return view('livewire.societies.manage-societies-index', [
            'societies' => Societies::latest()->where('accountant_id', Auth::user()->id)->paginate(5),
        ])
            ->with([
                'button' => 'Create new user',
                'success' => 'Society saved'
            ]);
    }
}
