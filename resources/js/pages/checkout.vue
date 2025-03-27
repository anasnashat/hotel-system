<script>
import { router,useForm  } from '@inertiajs/vue3';

export default {
    
    props: {
        cartItems: {
            type: Array,
            default: () => [] 
        },
        stripeKey: String
    },
    data() {
        return {
            stripe: null,
            cardElement: null,
            processing: false,
            errorMessage: ''
        };
    },
    
    setup(props) {
        // console.log("Inertia Props:", page.props);
        const form = useForm({
            stripeToken: '',
            room_ids: props.cartItems.map(item => item.room_id)
        });

        return { form };
    },
    mounted() {
        console.log("Stripe Key Received:", this.stripeKey);
    if (!this.stripeKey) {
        console.error("Stripe key is missing or empty.");
        return;
    }

    this.stripe = window.Stripe(this.stripeKey);
    const elements = this.stripe.elements();
    this.cardElement = elements.create('card', {
        style: {
            base: {
                fontSize: '16px',
                color: '#32325d',
            }
        }
    });
    this.cardElement.mount('#card-element');

    this.cardElement.addEventListener('change', (event) => {
        this.errorMessage = event.error ? event.error.message : '';
    });
}, methods: {
        createToken() {
            this.processing = true;

            this.stripe.createToken(this.cardElement).then((result) => {
                if (result.error) {
                    this.processing = false;
                    this.errorMessage = result.error.message;
                } else {
                    // Populate form data
                    this.form.stripeToken = result.token.id;
                    this.form.room_ids = this.cartItems.map(item => item.room?.id).filter(id => id);

                    // Send payment request
                    this.form.post('/checkout/create-charge', {
                        onSuccess: () => router.visit('/success'),
                        onError: () => {
                            this.processing = false;
                            this.errorMessage = "Payment failed. Please try again.";
                        }
                    });
                }
            });
        }
    }
    

};
</script>
<template>
    <div class="min-h-screen bg-gray-100 flex items-center justify-center px-4">
        <div class="w-full max-w-lg bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="bg-[#5b5329] text-white text-center py-6">
                <h2 class="text-2xl font-semibold">Complete Your Purchase</h2>
            </div>
            <div class="p-6">
                <div v-if="errorMessage" class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ errorMessage }}</div>

                <!-- Order Summary -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-center mb-3">Order Summary</h3>
                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                        <div v-for="item in cartItems" :key="item.room_id" class="flex justify-between py-2 border-b last:border-none">
                            <div>
                                <p class="text-gray-700 font-medium">{{ item.room.description || 'Unknown Room' }}</p>
                            </div>
                            <p class="text-gray-800 font-semibold">${{ ((item.room?.price || 0) / 100).toFixed(2) }}</p>
                        </div>
                        <div class="flex justify-between text-lg font-semibold mt-4">
                            <span>Total:</span>
                            <span>${{ (cartItems.reduce((sum, item) => sum + (item.room?.price || 0), 0) / 100).toFixed(2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                <form @submit.prevent="createToken">
                    <input type="hidden" name="stripeToken" id="stripe-token-id">
                    
                    <label for="card-element" class="block text-gray-700 font-medium mb-2">Payment Information</label>
                    <div id="card-element" class="bg-white border p-4 rounded-lg shadow-sm"></div>
                    <div id="card-errors" class="text-red-600 mt-2">{{ errorMessage }}</div>

                    <button type="submit" class="mt-6 w-full bg-[#5b5329] hover:bg-[#4a451f] text-white font-bold py-3 rounded-lg shadow-md transition duration-300" :disabled="processing">
                        {{ processing ? 'Processing...' : `Pay $${(cartItems.reduce((sum, item) => sum + (item.room?.price || 0), 0) / 100).toFixed(2)}` }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

