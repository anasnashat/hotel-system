<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Room Reservation Payment</title>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Complete Your Reservation</h4>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form id="checkout-form" method="post" action="{{ route('stripe.create-charge') }}">
                        @csrf
                        <input type="hidden" name="stripeToken" id="stripe-token-id">
                        <input type="hidden" name="room_id" value="{{ $room->id }}">

                        <div class="mb-4">
                            <h5>Reservation Details</h5>
                            <div class="border p-3 rounded">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Room:</span>
                                    <strong>{{ $room->description }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Price:</span>
                                    <strong>${{ number_format($room->price/100, 2) }}</strong>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="accompany_number" class="form-label">Number of Guests</label>
                            <input type="number" class="form-control" id="accompany_number" name="accompany_number" min="1" required>
                        </div>

                        <div class="mb-4">
                            <label for="card-element" class="form-label">Payment Information</label>
                            <div id="card-element" class="form-control p-3"></div>
                            <div id="card-errors" role="alert" class="text-danger mt-2"></div>
                        </div>

                        <button id="pay-btn" class="btn btn-primary w-100 py-3" type="button" onclick="createToken()">
                            Pay ${{ number_format($room->price/100, 2) }}
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

    // Display card errors
    cardElement.addEventListener('change', function(event) {
        var displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });

    function createToken() {
        document.getElementById("pay-btn").disabled = true;
        document.getElementById("pay-btn").textContent = "Processing...";

        stripe.createToken(cardElement).then(function(result) {
            if (result.error) {
                document.getElementById("pay-btn").disabled = false;
                document.getElementById("pay-btn").textContent = "Pay ${{ number_format($room->price/100, 2) }}";
                document.getElementById('card-errors').textContent = result.error.message;
            } else {
                document.getElementById("stripe-token-id").value = result.token.id;
                document.getElementById('checkout-form').submit();
            }
        });
    }
</script>
</body>
</html>
