<div class="booking-info-row">
    <span class="icon bp-slot-icon"></span>
    <span class="title">
        {{ __('shop::app.shop.products.slot-duration') }} :

        {{ __('shop::app.shop.products.slot-duration-in-minutes', ['minutes' => $bookingProduct->table_slot->duration]) }}
    </span>
</div>

@inject ('bookingSlotHelper', 'BagistoPackages\Shop\Helpers\TableSlot')

<div class="booking-info-row">
    <span class="icon bp-slot-icon"></span>
    <span class="title">
        {{ __('shop::app.shop.products.today-availability') }}
    </span>

    <span class="value">

        {!! $bookingSlotHelper->getTodaySlotsHtml($bookingProduct) !!}

    </span>

    <div class="toggle" @click="showDaysAvailability = ! showDaysAvailability">
        {{ __('shop::app.shop.products.slots-for-all-days') }}

        <i class="icon" :class="[! showDaysAvailability ? 'arrow-down-icon' : 'arrow-up-icon']"></i>
    </div>

    <div class="days-availability" v-show="showDaysAvailability">

        <table>
            <tbody>
            @foreach ($bookingSlotHelper->getWeekSlotDurations($bookingProduct) as $day)
                <tr>
                    <td>{{ $day['name'] }}</td>

                    <td>
                        @if ($day['slots'] && count($day['slots']))
                        @foreach ($day['slots'] as $slot)
                        {{ $slot['from'] . ' - ' . $slot['to'] }}</br>
                        @endforeach
                        @else
                            <span class="text-danger">{{ __('shop::app.shop.products.closed') }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>
</div>

@include ('shop::products.view.booking.slots', [
        'bookingProduct' => $bookingProduct,
        'title' => __('shop::app.shop.products.book-a-table')
    ])

<div class="control-group">
    <label>{{ __('shop::app.shop.products.special-notes') }}</label>
    <textarea name="booking[note]" class="control" style="width: 100%"/>
</div>
