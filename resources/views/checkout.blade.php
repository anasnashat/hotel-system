<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Checkout</title>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            font-size: 1.2rem;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4">
                <div class="card-header bg-white border-0 text-center">
                    <h4 class="mb-0">Complete Your Purchase</h4>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form id="checkout-form" method="post" action="{{ route('stripe.create-charge') }}">
                        @csrf
                        <input type="hidden" name="stripeToken" id="stripe-token-id">

                        <div class="mb-4">
                            <h5 class="text-center">Order Summary</h5>
                            <div class="border p-3 rounded bg-light">
                                @foreach($cartItems as $item)
                                    <div class="d-flex justify-content-between mb-2">
                                        <div>
                                            <h6 class="mb-0">{{ $item->room->name }}</h6>
                                            <small class="text-muted">{{ $item->room->description }}</small>
                                        </div>
                                        <strong>${{ number_format($item->room->price/100, 2) }}</strong>
                                    </div>
                                @endforeach
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold">Total:</span>
                                    <strong>${{ number_format($cartItems->sum(fn($item) => $item->room->price)/100, 2) }}</strong>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="card-element" class="form-label">Payment Information</label>
                            <div id="card-element" class="form-control p-3"></div>
                            <div id="card-errors" role="alert" class="text-danger mt-2"></div>
                        </div>

                        <button id="pay-btn" class="btn btn-primary w-100 py-3" type="button" onclick="createToken()">
                            Pay ${{ number_format($cartItems->sum(fn($item) => $item->room->price)/100, 2) }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
    var stripe = Stripe('{{ env('STRIPE_KEY') }}');
    var elements = stripe.elements();
    var cardElement = elements.create('card', {
        style: {
            base: {
                fontSize: '16px',
                color: '#32325d',
            }
        }
    });
    cardElement.mount('#card-element');

    cardElement.addEventListener('change', function(event) {
        document.getElementById('card-errors').textContent = event.error ? event.error.message : '';
    });

    function createToken() {
        document.getElementById("pay-btn").disabled = true;
        document.getElementById("pay-btn").textContent = "Processing...";

        stripe.createToken(cardElement).then(function(result) {
            if (result.error) {
                document.getElementById("pay-btn").disabled = false;
                document.getElementById("pay-btn").textContent = "Pay ${{ number_format($cartItems->sum(fn($item) => $item->room->price)/100, 2) }}";
                document.getElementById('card-errors').textContent = result.error.message;
            } else {
                document.getElementById("stripe-token-id").value = result.token.id;
                @foreach($cartItems as $item)
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'room_ids[]';
                input.value = '{{ $item->room->id }}';
                document.getElementById('checkout-form').appendChild(input);
                @endforeach
                document.getElementById('checkout-form').submit();
            }
        });
    }
</script>
</body>
</html>
