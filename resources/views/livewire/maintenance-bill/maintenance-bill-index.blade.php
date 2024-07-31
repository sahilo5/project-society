
<div class='mt-16'>
    @section('title', 'Maintenance Bill')
    <div class="mb-6 mt-12 border-gray-200 dark:border-gray-700">
        <ul
            class="flex flex-wrap text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:border-gray-700 dark:text-gray-400">
            <li class="me-2">
                <button aria-current="page"
                    class="inline-block p-4 rounded-t-lg hover:text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:text-gray-300"
                    wire:click="goBack">Society
                    Dashboard</button>
            </li>

            <li class="me-2">
                <button aria-current="page"
                    class="inline-block p-4 text-mygreen-600 bg-mygreen-100 rounded-t-lg active dark:bg-mygreen-800 dark:text-mygreen-500">Maintenance
                    Bill</button>
            </li>

        </ul>

    </div>
    <div class='w-full flex flex-row items-center  mb-5 '>
        {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}
        {{--

        <div class='w-1/4'>
            @if ($societiesList->isEmpty())
            <x-alert-no-registered-societies/>
            @else
            <label for="societies" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select
                Society</label>
            <select id="societies" wire:model.live="selected_society"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    disabled>
                <option value="">Select society</option>
                @foreach ($societiesList as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
            @endif
        </div>

        --}}

        <div class='w-1/4 pr-4'>
            <label for="months" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select
                month</label>
            <select id="months" wire:model.live="selected_month"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-mygreen-500 focus:border-mygreen-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:mygreen-500 dark:focus:border-mygreen-500">
                <option value="">Select Month</option>
                @foreach ($months as $index => $month)
                    <option value="{{ $index + 1 }}">{{ $month }}</option>
                @endforeach
            </select>
        </div>


        <div class='w-1/4'>
            <label for="countries" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select
                Year</label>
            <select id="yaer" wire:model.live="selected_year"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-mygreen-500 focus:border-mygreen-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-mygreen-500 dark:focus:border-mygreen-500">

                <option value="">Select Year</option>
                @for ($year = now()->year; $year >= 2000; $year--)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endfor
            </select>
        </div>
    </div>

    @if ($selected_society)
        <div class="relative overflow-x-auto sm:rounded-lg">
            <div
                class="flex items-center justify-between flex-column flex-wrap md:flex-row space-y-4 md:space-y-0 pb-4 bg-white dark:bg-gray-900">
                <div>
                    {{-- button --}}
                    <button type="button" wire:click="downloadSelected" onclick="console.log('Button clicked')"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 text-sm px-3 py-1.5">
                        <svg class="w-3.5 h-3.5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor" viewBox="0 0 18 21">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 13V4M7 14H5a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-4a1 1 0 0 0-1-1h-2m-1-5-4 5-4-5m9 8h.01" />
                        </svg>
                        Download
                    </button>

                    <button type="button" wire:click.live="downloadSelected"
                        class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-center inline-flex items-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800 text-sm px-3 py-1.5">
                        <svg class="w-3.5 h-3.5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path fill="currentColor" fill-rule="evenodd"
                                d="M12 4a8 8 0 0 0-6.895 12.06l.569.718-.697 2.359 2.32-.648.379.243A8 8 0 1 0 12 4ZM2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10a9.96 9.96 0 0 1-5.016-1.347l-4.948 1.382 1.426-4.829-.006-.007-.033-.055A9.958 9.958 0 0 1 2 12Z"
                                clip-rule="evenodd" />
                            <path fill="currentColor"
                                d="M16.735 13.492c-.038-.018-1.497-.736-1.756-.83a1.008 1.008 0 0 0-.34-.075c-.196 0-.362.098-.49.291-.146.217-.587.732-.723.886-.018.02-.042.045-.057.045-.013 0-.239-.093-.307-.123-1.564-.68-2.751-2.313-2.914-2.589-.023-.04-.024-.057-.024-.057.005-.021.058-.074.085-.101.08-.079.166-.182.249-.283l.117-.14c.121-.14.175-.25.237-.375l.033-.066a.68.68 0 0 0-.02-.64c-.034-.069-.65-1.555-.715-1.711-.158-.377-.366-.552-.655-.552-.027 0 0 0-.112.005-.137.005-.883.104-1.213.311-.35.22-.94.924-.94 2.16 0 1.112.705 2.162 1.008 2.561l.041.06c1.161 1.695 2.608 2.951 4.074 3.537 1.412.564 2.081.63 2.461.63.16 0 .288-.013.4-.024l.072-.007c.488-.043 1.56-.599 1.804-1.276.192-.534.243-1.117.115-1.329-.088-.144-.239-.216-.43-.308Z" />
                        </svg>
                        Whatsapp
                    </button>
                </div>


                {{-- search bar --}}
                <label for="table-search" class="sr-only">Search</label>
                <div class="relative">
                    <div class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                    <input type="text" id="table-search-members" wire:model.live.debounce.500ms="search"
                        class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-mygreen-500 focus:border-mygreen-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-mygreen-500 dark:focus:border-blue-500"
                        placeholder="Search for members">
                </div>

            </div>

            {{-- table --}}
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="p-4">
                            <div class="flex items-center">
                                <input id="checkbox-all-search" type="checkbox" wire:model="selectAll"
                                    class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 dark:focus:ring-green-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label for="checkbox-all-search" class="sr-only">checkbox</label>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3">
                            SR No.
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Name
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Invoice Number
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Advance Payment
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($members as $member)
                        <tr
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="w-4 p-4">
                                <div class="flex items-center">
                                    <input id="checkbox-table-search-1" type="checkbox" wire:model="selectedMembers"
                                        value="{{ $member->id }}
                                        class="w-4
                                        h-4 text-mygreen-600 bg-gray-100 border-gray-300 rounded focus:ring-mygreen-500
                                        dark:focus:ring-mygreen-600 dark:ring-offset-gray-800
                                        dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700
                                        dark:border-gray-600">
                                    <label for="checkbox-table-search-1" class="sr-only">checkbox</label>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                {{ $loop->iteration }}
                            </td>
                            <th scope="row"
                                class="flex items-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">

                                <div class="ps-3">
                                    <div class="text-base font-semibold"> {{ $member->name }}</div>
                                    <div class="font-normal text-gray-500"> {{ $member->phone }}</div>
                                </div>
                            </th>
                            <td class="px-6 py-4">
                                {{ $member->bill_id }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($member->advance == 0)
                                    <span class="text-red-500">No</span>
                                @else
                                    <span class="text-green-500">Yes</span>
                                @endif

                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if ($member->status == 1)
                                        <span
                                            class=" w-3.5 h-3.5 bg-green-400 border-2 border-white dark:border-gray-800 rounded-full"></span>
                                    @elseif($member->status == 0)
                                        <span
                                            class=" w-3.5 h-3.5 bg-red-400 border-2 border-white dark:border-gray-800 rounded-full"></span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                {{-- download invoice button --}}

                                <button type="button" wire:click="download({{ $member->bill_id }})"
                                    class=" text-blue-700  focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-center inline-flex items-center  text-sm px-3 py-1.5">

                                    <svg class="w-[30px] h-[30px] me-2" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 21">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 13V4M7 14H5a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-4a1 1 0 0 0-1-1h-2m-1-5-4 5-4-5m9 8h.01" />
                                    </svg>
                                </button>

                                {{-- whatsapp button --}}
                                <button type="button" wire:click="sendWhatsAppMessage({{ $member->member_id }})"
                                    class=" text-green-700  focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-center inline-flex items-center  text-sm px-3 py-1.5">
                                    <svg class="w-[30px] h-[30px] me-2" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        fill="none" viewBox="0 0 24 24">
                                        <path fill="currentColor" fill-rule="evenodd"
                                            d="M12 4a8 8 0 0 0-6.895 12.06l.569.718-.697 2.359 2.32-.648.379.243A8 8 0 1 0 12 4ZM2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10a9.96 9.96 0 0 1-5.016-1.347l-4.948 1.382 1.426-4.829-.006-.007-.033-.055A9.958 9.958 0 0 1 2 12Z"
                                            clip-rule="evenodd" />
                                        <path fill="currentColor"
                                            d="M16.735 13.492c-.038-.018-1.497-.736-1.756-.83a1.008 1.008 0 0 0-.34-.075c-.196 0-.362.098-.49.291-.146.217-.587.732-.723.886-.018.02-.042.045-.057.045-.013 0-.239-.093-.307-.123-1.564-.68-2.751-2.313-2.914-2.589-.023-.04-.024-.057-.024-.057.005-.021.058-.074.085-.101.08-.079.166-.182.249-.283l.117-.14c.121-.14.175-.25.237-.375l.033-.066a.68.68 0 0 0-.02-.64c-.034-.069-.65-1.555-.715-1.711-.158-.377-.366-.552-.655-.552-.027 0 0 0-.112.005-.137.005-.883.104-1.213.311-.35.22-.94.924-.94 2.16 0 1.112.705 2.162 1.008 2.561l.041.06c1.161 1.695 2.608 2.951 4.074 3.537 1.412.564 2.081.63 2.461.63.16 0 .288-.013.4-.024l.072-.007c.488-.043 1.56-.599 1.804-1.276.192-.534.243-1.117.115-1.329-.088-.144-.239-.216-.43-.308Z" />
                                    </svg>
                                </button>
                                <!-- Button to open the modal -->
                                <button type="button" wire:click="openEditModal({{ $member->bill_id }})"
                                    class="text-white  ">
                                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M11.32 6.176H5c-1.105 0-2 .949-2 2.118v10.588C3 20.052 3.895 21 5 21h11c1.105 0 2-.948 2-2.118v-7.75l-3.914 4.144A2.46 2.46 0 0 1 12.81 16l-2.681.568c-1.75.37-3.292-1.263-2.942-3.115l.536-2.839c.097-.512.335-.983.684-1.352l2.914-3.086Z" clip-rule="evenodd"/>
                                        <path fill-rule="evenodd" d="M19.846 4.318a2.148 2.148 0 0 0-.437-.692 2.014 2.014 0 0 0-.654-.463 1.92 1.92 0 0 0-1.544 0 2.014 2.014 0 0 0-.654.463l-.546.578 2.852 3.02.546-.579a2.14 2.14 0 0 0 .437-.692 2.244 2.244 0 0 0 0-1.635ZM17.45 8.721 14.597 5.7 9.82 10.76a.54.54 0 0 0-.137.27l-.536 2.84c-.07.37.239.696.588.622l2.682-.567a.492.492 0 0 0 .255-.145l4.778-5.06Z" clip-rule="evenodd"/>
                                      </svg>
                                </button>
                               
                                  
                                <!-- Edit modal -->


                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>


        </div>

        @if ($isModalOpen)
            <!-- Edit Modal -->
            <div id="crud-modal" tabindex="-1" aria-hidden="true"
                class="fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full h-full bg-black bg-opacity-50"
               >
                <div class="relative p-4 w-full max-w-md max-h-full">
                    <!-- Modal content -->
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                            <!-- Modal header -->
                            <div
                                class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Edit Bill
                                </h3>
                                <button type="button" wire:click="closeEditModal"
                                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>
                            <!-- Modal body -->
                            <form wire:submit.prevent="updateBill" class="p-4 md:p-5">
                                <div class="grid gap-4 mb-4 grid-cols-2">
                                   
                                    {{-- Invoice Number --}}
                                    <div class="col-span-2">
                                        <label for="editid"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Invoice
                                            Number</label>
                                        <input type="text" wire:model="asdid" id="editName"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-mygreen-600 focus:border-mygreen-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-mygreen-500 dark:focus:border-mygreen-500"
                                            placeholder="Invoice number" required readonly>
                                    </div>

                                    {{-- Payment Method  --}}
                                    <div class="col-span-2 sm:col-span-1">
                                        <label for="editPaymentMethod"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Payment
                                            Method</label>
                                        <select wire:model="editPaymentMethod" id="editPaymentMethod"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-mygreen-500 focus:border-mygreen-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-mygreen-500">
                                            <option value="">Select Payment</option>
                                            <option value="1">Online</option>
                                            <option value="2">Cheque</option>
                                            <option value="3">Cash</option>
                                        </select>
                                    </div>

                                    {{-- Advance Payment --}}
                                    <div class="col-span-2 sm:col-span-1">
                                        <label for="editAdvancePayment"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Advance
                                            Payment</label>
                                        <select wire:model="editAdvancePayment" id="editAdvancePayment"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-mygreen-500 focus:border-mygreen-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-mygreen-500 dark:focus:border-mygreen-500">
                                            <option value="">Select</option>
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                    {{-- Reference nos --}}
                                    <div class="col-span-2">
                                        <label for="editChequeNo"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Reference
                                            Number</label>
                                        <input type="text" wire:model="editChequeNo" id="editChequeNo"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-mygreen-600 focus:border-mygreen-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-mygreen-500 dark:focus:border-mygreen-500"
                                            placeholder="Reference Number">
                                    </div>

                                    {{-- Remarks --}}
                                    <div class="col-span-2">
                                        <label for="editRemarks"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Remarks <span class="text-red-500 text-lg">*</span></label>
                                        <input type="text" wire:model="editRemark" id="editChequeNo"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-mygreen-600 focus:border-mygreen-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-mygreen-500 dark:focus:border-mygreen-500"
                                            placeholder="Remarks" required>
                                    </div>
                                </div>

                                <button type="submit"
                                    class="text-white inline-flex items-center bg-mygreen-700 hover:bg-mygreen-800 focus:ring-4 focus:outline-none focus:ring-mygreen-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-mygreen-600 dark:hover:bg-mygreen-700 dark:focus:ring-mygreen-800">
                                    Save
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
        @endif




        {{-- if no bills found --}}
        @if ($members->isEmpty())
            <div class="my-4 p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300"
                role="alert">
                <span class="font-medium">No bills found! Please generate bills.</span>
            </div>

            {{-- form --}}
            <form class="max-w w-3/4 mx-auto flex item-center justify-evenly" wire:submit.prevent="generateBills">
                <div class="mb-5">

                </div>
                <div class="mb-5">
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                            </svg>
                        </div>
                        <input wire:model="due_date" type="date"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Select due date">
                    </div>
                </div>
                {{-- <div class="mb-5">
                    <input wire:model="amount" type="number"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Enter amount">
                </div> --}}
                <div>
                    <button type="submit"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Generate Bills
                        <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9" />
                        </svg>
                    </button>
                    {{-- <span wire:loading>Saving...</span> --}}
                </div>
            </form>
        @endif
    @else
        @if ($societies)
            <div class="my-2 p-4 text-sm text-gray-800 rounded-lg bg-gray-50 dark:bg-gray-800 dark:text-gray-300"
                role="alert">
                <span class="font-medium"> Please choose a society to see members!</span>
            </div>
        @endif
    @endif

    <script>
        document.addEventListener('livewire:load', function() {
            Livewire.on('whatsappMessageSent', function() {
                console.log('WhatsApp message sent successfully!');
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@1.2.3/dist/flowbite.min.js"></script>
    <div>
        <!-- Display the flash message -->
        @if (session()->has('success'))
            <div id="toast-success"
                class="mt-12 fixed top-5 right-5 flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800"
                role="alert">
                <div
                    class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg dark:bg-green-800 dark:text-green-200">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                        viewBox="0 0 20 20">
                        <path
                            d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                    </svg>
                    <span class="sr-only">Check icon</span>
                </div>
                <div class="ms-3 text-sm font-normal">
                    {{ session('success') }}
                </div>
                <button type="button"
                    class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700"
                    data-dismiss-target="#toast-success" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                </button>
            </div>
        @endif
    </div>

</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const flashMessage = document.getElementById('toast-success');
        if (flashMessage) {
            setTimeout(() => {
                flashMessage.style.display = 'none';
            }, 3000); // Adjust the time (in milliseconds) as needed
        }
    });
</script>

{{-- pervious button --}}
{{-- <div class="mt-3 flex">
        <a wire:click="goBack"
           class="flex items-center justify-center px-3 h-8 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
            <svg class="w-3.5 h-3.5 me-2 rtl:rotate-180" aria-hidden="true"
                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 5H1m0 0 4 4M1 5l4-4"/>
            </svg>
            Previous
        </a>
    </div> --}}
