@extends($layout)

@section('title', __('superadmin::lang.subscription'))

@section('content')

<!-- Main content -->
<section class="content">

	@include('superadmin::layouts.partials.currency')

	<div class="box box-success">
        <div class="box-header">
            <h3 class="box-title">@lang('superadmin::lang.pay_and_subscribe')</h3>
        </div>
	
        <div class="box-body">
    		<div class="col-md-8">
        		<h3>
        			{{$package->name}}

        			(<span class="display_currency" data-currency_symbol="true">{{$package->price}}</span>

					<small>
						/ {{$package->interval_count}} {{ucfirst($package->interval)}}
					</small>)
        		</h3>
        		<ul>
					<li>
						@if($package->location_count == 0)
							@lang('superadmin::lang.unlimited')
						@else
							{{$package->location_count}}
						@endif

						@lang('business.business_locations')
					</li>

					<li>
						@if($package->user_count == 0)
							@lang('superadmin::lang.unlimited')
						@else
							{{$package->user_count}}
						@endif

						@lang('superadmin::lang.users')
					</li>

					<li>
						@if($package->product_count == 0)
							@lang('superadmin::lang.unlimited')
						@else
							{{$package->product_count}}
						@endif

						@lang('superadmin::lang.products')
					</li>

					<li>
						@if($package->invoice_count == 0)
							@lang('superadmin::lang.unlimited')
						@else
							{{$package->invoice_count}}
						@endif

						@lang('superadmin::lang.invoices')
					</li>

					@if($package->trial_days != 0)
						<li>
							{{$package->trial_days}} @lang('superadmin::lang.trial_days')
						</li>
					@endif
				</ul>

				<ul class="list-group">
					@foreach($gateways as $k => $v)
						<div class="list-group-item">
							<b>@lang('superadmin::lang.pay_via', ['method' => $v])</b>
							
							<div class="row" id="paymentdiv_{{$k}}">
								@php 
									$view = 'superadmin::subscription.partials.pay_'.$k;
								@endphp
								@includeIf($view)
							</div>
						</div>
					@endforeach
					<div class="list-group-item">
						<!-- Create divs for the payment fields -->
						<div id="cardNumber" class="inputField"></div>
						<p></p>
						<div id="expiryDate" class="inputField"></div>
						<p></p>
						<div id="cvv" class="inputField"></div>
						<p></p>
						<!-- Add a payment button -->
						<button id="payNow" type="button">>Pay now</button>
					</div>
				</ul>
			</div>
        </div>
    </div>
</section>
@endsection
@section('javascript')
<script src="https://hosted.paysafe.com/js/v1/latest/paysafe.min.js"></script>
<script type="text/javascript">
      // Base64-encoded version the Single-Use Token API key.
      // Create the key below by concatenating the API username and password
      // separated by a colon and Base 64 encoding the result
      var API_KEY = 'dGVzdF9lbWZvdXJhZG1pbjpCLXFhMi0wLTY0NTEyMDAyLTAtMzAyYjAyMTQzM2JjOTBmMGM4ZTIyZmMxYjNmMWQ4NTFkZDI2ZDc5ODRhM2ZjY2U5MDIxMzZmN2EyYWEyY2ZiYWFmNzdkMzFkNzY3YjBkODVmM2E3ZTI5NDQ4';
      var options = {
        // You must provide currencyCode to the Paysafe JS SDK to enable the Payment API integration
        currencyCode: 'USD',
        // select the Paysafe test / sandbox environment
        environment: 'TEST',
        // Provide a cards merchant toaccount if you have more than one configured for that same API key
        //   accounts: {
        //   default: 0000000000,
        // },
        // set the CSS selectors to identify the payment field divs above
        // set the placeholder text to display in these fields
        fields: {
          cardNumber: {
            selector: '#cardNumber',
            placeholder: 'Card number',
            separator: ' ',
          },
          expiryDate: {
            selector: '#expiryDate',
            placeholder: 'Expiry date',
          },
          cvv: {
            selector: '#cvv',
            placeholder: 'CVV',
            optional: false,
          },
        },
      };
      // initalize the hosted iframes using the SDK setup function
      let demoInstance;
      paysafe.fields.setup(API_KEY, options).then(instance => {
          console.log('Setup instance completed.');
          demoInstance = instance;
          return instance.show();
        }).then(paymentMethods => {
            if (paymentMethods.card && !paymentMethods.card.error) {
              // When the customer clicks Pay Now,
              // call the SDK tokenize function to create
              // a single-use payment token corresponding to the card details entered
              	document.getElementById('payNow').addEventListener('click', function(event) {
                    var tokenizationOptions = {
                      amount: 1000,
                      transactionType: 'PAYMENT',
                      paymentType: 'CARD',
                      merchantRefNum: 'merchant-ref-num-' + new Date().getTime(),
                      customerDetails: {
                        billingDetails: {
                          country: 'US',
                          zip: '90210',
                          street: 'Oak Fields 6',
                          city: 'ca',
                          state: 'CA',
                        },
                      },
                    };
                    demoInstance.tokenize(tokenizationOptions).then(result => {
                      // write the Payment token value to the browser console
                      console.log(result.token);
                    }).catch(error => {
                      // display the tokenization error in dialog window
                      alert(JSON.stringify(error));
                    });
                  
				});
			}
		});
	
    </script>
@endsection