@extends('layouts.app')

@section('content')
<div x-show="showRefundModal"
    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">

    <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl p-6 max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="flex justify-between items-start mb-4">
            <div>
                <h2 class="text-xl font-bold">Refund Details</h2>
                <p class="text-sm text-gray-500">{{ $refund->refund_number }}</p>
            </div>
            <span class="px-3 py-1 text-xs font-semibold rounded-lg
        bg-{{ $refund->status_color }}-100
        text-{{ $refund->status_color }}-800">
                {{ ucfirst($refund->status) }}
            </span>
        </div>

        <!-- Refund Info -->
        <div class="border rounded-xl p-4 space-y-2 text-sm mb-4">
            <div class="flex justify-between">
                <span>Order</span>
                <a href="{{ route('order.show',$refund->order_id) }}"
                    class="font-semibold text-blue-600">
                    #{{ $refund->order->order_number }}
                </a>
            </div>
            <div class="flex justify-between">
                <span>Amount</span>
                <span class="font-bold text-green-600">
                    Rp {{ number_format($refund->refund_amount,0,',','.') }}
                </span>
            </div>
            <div class="flex justify-between">
                <span>Reason</span>
                <span class="font-semibold">
                    {{ $refund->reason_label }}
                </span>
            </div>
            <div class="flex justify-between">
                <span>Date</span>
                <span>
                    {{ $refund->created_at->format('d M Y H:i') }}
                </span>
            </div>
            @if($refund->reason_detail)
            <div class="pt-2 border-t">
                <p class="text-gray-600 text-xs mb-1">Details</p>
                <p class="bg-gray-50 p-2 rounded text-xs">
                    {{ $refund->reason_detail }}
                </p>
            </div>
            @endif
        </div>


        <!-- Bank Info -->
        @if($refund->hasBuyerBankDetails())
        <div class="border rounded-xl p-4 mb-4 text-sm space-y-2">
            <h4 class="font-semibold">Bank Account</h4>
            <div class="flex justify-between">
                <span>Bank</span>
                <span class="font-semibold">
                    {{ $refund->bank_name }}
                </span>
            </div>
            <div class="flex justify-between">
                <span>No</span>
                <span class="font-mono">
                    {{ $refund->bank_account_number }}
                </span>
            </div>
            <div class="flex justify-between">
                <span>Name</span>
                <span>
                    {{ $refund->bank_account_name }}
                </span>
            </div>
        </div>
        @endif


        <!-- Buyer -->
        <div class="border rounded-xl p-4 mb-4 text-sm">
            <h4 class="font-semibold mb-2">Buyer</h4>
            <p>{{ $refund->user->name }}</p>
            <p class="text-gray-600 text-xs">
                {{ $refund->user->email }}
            </p>
        </div>


        <!-- Upload Proof -->
        @if($refund->status=='pending' && $refund->hasBuyerBankDetails())
        <form @submit.prevent="approveRefund()" class="space-y-3">
            <input type="file"
                @change="handleProofUpload($event)"
                required
                class="text-sm">
            <textarea
                x-model="approvalData.notes"
                rows="2"
                placeholder="Notes (optional)"
                class="w-full border rounded-lg p-2 text-sm">
        </textarea>
            <div class="flex gap-2">
                <button type="submit"
                    :disabled="isSubmitting"
                    class="flex-1 bg-green-600 text-white py-2 rounded-lg">
                    Approve
                </button>
                <button type="button"
                    @click="showRejectModal=true"
                    class="flex-1 bg-red-600 text-white py-2 rounded-lg">
                    Reject
                </button>
            </div>
        </form>
        @endif


        <!-- Approved Proof -->
        @if($refund->status=='approved' && $refund->refund_proof)
        <div class="mt-4">
            <h4 class="font-semibold mb-2 text-sm">
                Transfer Proof
            </h4>
            <img src="{{ asset('storage/refunds/'.$refund->refund_proof) }}"
                class="rounded-lg border">
        </div>
        @endif


        <!-- Close -->
        <button
            @click="showRefundModal=false"
            class="w-full mt-4 border py-2 rounded-lg">
            Close
        </button>
    </div>
</div>

<!-- Reject Modal -->
<div
    x-show="showRejectModal"
    @keydown.escape.window="showRejectModal = false"
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;">
    <div class="fixed inset-0 bg-black bg-opacity-60" @click="showRejectModal = false"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-2xl max-w-md w-full p-6" @click.stop>
            <h3 class="text-xl font-bold text-gray-900 mb-4">Reject Refund</h3>
            <form @submit.prevent="rejectRefund()">
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">Rejection Reason *</label>
                    <textarea
                        x-model="rejectionData.notes"
                        required
                        rows="4"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-red-100 focus:border-red-500"
                        placeholder="Explain why you're rejecting this refund..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button
                        type="submit"
                        :disabled="isSubmitting"
                        class="flex-1 px-6 py-3 bg-red-600 text-white font-semibold rounded-xl hover:bg-red-700 disabled:bg-gray-400">
                        <span x-show="!isSubmitting">Reject Refund</span>
                        <span x-show="isSubmitting">Processing...</span>
                    </button>
                    <button
                        type="button"
                        @click="showRejectModal = false"
                        class="px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-300">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function sellerRefundDetail() {
        return {
            showRejectModal: false,
            isSubmitting: false,
            proofFile: null,
            proofPreview: null,

            approvalData: {
                notes: ''
            },

            rejectionData: {
                notes: ''
            },

            init() {
                console.log('Seller refund detail initialized');
            },

            handleProofUpload(event) {
                const file = event.target.files[0];
                if (!file) return;

                this.proofFile = file;

                // Preview
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.proofPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            },

            async approveRefund() {
                if (!this.proofFile) {
                    alert('Please upload transfer proof');
                    return;
                }

                if (!confirm('Are you sure you want to approve this refund? Make sure you have transferred the money to buyer.')) {
                    return;
                }

                this.isSubmitting = true;

                try {
                    const formData = new FormData();
                    formData.append('refund_proof', this.proofFile);
                    formData.append('notes', this.approvalData.notes);

                    const response = await fetch(`{{ route('seller.refund.approve', $refund->id) }}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert('Refund approved successfully!');
                        window.location.reload();
                    } else {
                        alert(data.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred');
                } finally {
                    this.isSubmitting = false;
                }
            },

            async rejectRefund() {
                if (!this.rejectionData.notes) {
                    alert('Please provide a rejection reason');
                    return;
                }

                if (!confirm('Are you sure you want to reject this refund?')) {
                    return;
                }

                this.isSubmitting = true;

                try {
                    const response = await fetch(`{{ route('seller.refund.reject', $refund->id) }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(this.rejectionData)
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert('Refund rejected');
                        window.location.reload();
                    } else {
                        alert(data.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred');
                } finally {
                    this.isSubmitting = false;
                    this.showRejectModal = false;
                }
            },

            copyToClipboard(text) {
                const cleanText = text.toString().replace(/\./g, '');
                navigator.clipboard.writeText(cleanText).then(() => {
                    this.showToast('Copied to clipboard!');
                });
            },

            copyBankDetails() {
                const text = `
Transfer Details
━━━━━━━━━━━━━━━━━━━━━
Bank: {{ $refund->bank_name }}
Account: {{ $refund->bank_account_number }}
Name: {{ $refund->bank_account_name }}
Amount: Rp {{ number_format($refund->refund_amount, 0, ',', '.') }}
━━━━━━━━━━━━━━━━━━━━━
            `.trim();

                navigator.clipboard.writeText(text).then(() => {
                    this.showToast('All details copied!');
                });
            },

            showToast(message) {
                const toast = document.createElement('div');
                toast.className = 'fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50';
                toast.innerHTML = `
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                    </svg>
                    <span>${message}</span>
                </div>
            `;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 2000);
            }
        }
    }
</script>
@endsection