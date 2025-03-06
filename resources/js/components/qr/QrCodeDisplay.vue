<template>
  <div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
          <div class="text-center">
            <h2 class="text-2xl font-bold mb-6">Your Conference QR Code</h2>
            
            <div v-if="qrCode" class="mb-8">
              <img :src="'data:image/png;base64,' + qrCode" alt="QR Code" class="mx-auto mb-4" />
              <p class="text-sm text-gray-600">Use this QR code for accreditation and meal validation</p>
            </div>

            <div v-if="receipt" class="mt-8 text-left max-w-md mx-auto">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Receipt</h3>
              <div class="bg-gray-50 p-4 rounded-lg">
                <div class="grid grid-cols-2 gap-4">
                  <div class="text-sm text-gray-500">Name:</div>
                  <div class="text-sm font-medium">{{ receipt.participant_name }}</div>
                  
                  <div class="text-sm text-gray-500">Ticket Type:</div>
                  <div class="text-sm font-medium">{{ receipt.ticket_type }}</div>
                  
                  <div class="text-sm text-gray-500">Amount Paid:</div>
                  <div class="text-sm font-medium">₦{{ receipt.amount_paid.toLocaleString() }}</div>
                  
                  <div class="text-sm text-gray-500">Reference:</div>
                  <div class="text-sm font-medium">{{ receipt.payment_reference }}</div>
                  
                  <div class="text-sm text-gray-500">Date:</div>
                  <div class="text-sm font-medium">{{ new Date(receipt.payment_date).toLocaleDateString() }}</div>
                </div>
              </div>
              
              <button @click="printReceipt"
                class="mt-4 w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Print Receipt
              </button>
            </div>

            <div v-if="!qrCode && !receipt" class="text-gray-500">
              Complete your profile and payment to generate your QR code
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import axios from 'axios'

export default {
  setup() {
    const qrCode = ref(null)
    const receipt = ref(null)

    const fetchQrCode = async () => {
      try {
        const response = await axios.get('/api/qr-code', {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        })
        qrCode.value = response.data.qr_image
      } catch (error) {
        console.error('Failed to fetch QR code:', error)
      }
    }

    const fetchReceipt = async () => {
      try {
        const response = await axios.get('/api/payment-receipt', {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        })
        receipt.value = response.data.receipt
      } catch (error) {
        console.error('Failed to fetch receipt:', error)
      }
    }

    const printReceipt = () => {
      const printWindow = window.open('', '_blank')
      printWindow.document.write(`
        <html>
          <head>
            <title>Conference Payment Receipt</title>
            <style>
              body { font-family: Arial, sans-serif; padding: 20px; }
              .receipt { max-width: 600px; margin: 0 auto; }
              .header { text-align: center; margin-bottom: 30px; }
              .details { margin-top: 20px; }
              .row { display: flex; margin-bottom: 10px; }
              .label { width: 150px; color: #666; }
              .value { flex: 1; font-weight: bold; }
            </style>
          </head>
          <body>
            <div class="receipt">
              <div class="header">
                <h1>NAPPS Conference Payment Receipt</h1>
              </div>
              <div class="details">
                <div class="row">
                  <div class="label">Name:</div>
                  <div class="value">${receipt.value.participant_name}</div>
                </div>
                <div class="row">
                  <div class="label">Ticket Type:</div>
                  <div class="value">${receipt.value.ticket_type}</div>
                </div>
                <div class="row">
                  <div class="label">Amount Paid:</div>
                  <div class="value">₦${receipt.value.amount_paid.toLocaleString()}</div>
                </div>
                <div class="row">
                  <div class="label">Reference:</div>
                  <div class="value">${receipt.value.payment_reference}</div>
                </div>
                <div class="row">
                  <div class="label">Date:</div>
                  <div class="value">${new Date(receipt.value.payment_date).toLocaleDateString()}</div>
                </div>
              </div>
            </div>
          </body>
        </html>
      `)
      printWindow.document.close()
      printWindow.print()
    }

    onMounted(() => {
      fetchQrCode()
      fetchReceipt()
    })

    return {
      qrCode,
      receipt,
      printReceipt
    }
  }
}
</script>
