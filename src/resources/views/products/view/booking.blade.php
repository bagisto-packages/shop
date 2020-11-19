@if ($product->type == 'booking')
    @if ($bookingProduct = app('\BagistoPackages\Shop\Repositories\BookingProductRepository')->findOneByField('product_id', $product->product_id))

        @push('css')
            <link rel="stylesheet" href="{{ theme_asset('css/default-booking.css') }}">
        @endpush

        <booking-information></booking-information>

        @push('scripts')
            <script type="text/x-template" id="booking-information-template">
                <div class="booking-information">
                    @if ($bookingProduct->location != '')
                        <div class="booking-info-row">
                            <span class="icon bp-location-icon"></span>
                            <span class="title">{{ __('shop::app.shop.products.location') }}</span>
                            <span class="value">{{ $bookingProduct->location }}</span>
                            <a href="https://maps.google.com/maps?q={{ $bookingProduct->location }}" target="_blank">View
                                on Map</a>
                        </div>
                    @endif

                    @include ('shop::products.view.booking.' . $bookingProduct->type, ['bookingProduct' => $bookingProduct])
                </div>
            </script>

            <script>
                Vue.component('booking-information', {
                    template: '#booking-information-template',

                    inject: ['$validator'],

                    data: function () {
                        return {
                            showDaysAvailability: false
                        }
                    }
                });
            </script>

        @endpush

    @endif

@endif
