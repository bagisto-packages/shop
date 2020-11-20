<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    Route::prefix('paypal/standard')->group(function () {
        Route::get('/redirect', 'StandardController@redirect')->name('paypal.standard.redirect');
        Route::get('/success', 'StandardController@success')->name('paypal.standard.success');
        Route::get('/cancel', 'StandardController@cancel')->name('paypal.standard.cancel');
    });

    Route::prefix('paypal/smart-button')->group(function () {
        Route::get('/details', 'SmartButtonController@details')->name('paypal.smart_button.details');
        Route::post('/save-order', 'SmartButtonController@saveOrder')->name('paypal.smart_button.save_order');
    });
});

Route::get('paypal/standard/ipn', 'StandardController@ipn')->name('paypal.standard.ipn');

Route::group(['middleware' => ['web', 'locale', 'theme', 'currency']], function () {
    Route::get('/booking-slots/{id}', 'BookingProductController@index')->name('booking_product.slots.index');

    Route::get('/', 'HomeController@index')->name('home.index');
    Route::get('/subscribe', 'SubscriptionController@subscribe')->name('subscribe');
    Route::get('/unsubscribe/{token}', 'SubscriptionController@unsubscribe')->name('unsubscribe');
    Route::get('/search', 'SearchController@index')->name('search.index');
    Route::post('/upload-search-image', 'HomeController@upload')->name('image.search.upload');
    Route::get('get/countries', 'CountryStateController@getCountries')->name('get.countries');
    Route::get('get/states/{country}', 'CountryStateController@getStates')->name('get.states');
    Route::get('checkout/cart', 'CartController@index')->name('checkout.cart.index');
    Route::post('checkout/cart/coupon', 'CartController@applyCoupon')->name('checkout.cart.coupon.apply');
    Route::delete('checkout/cart/coupon', 'CartController@removeCoupon')->name('checkout.coupon.remove.coupon');
    Route::post('checkout/cart/add/{id}', 'CartController@add')->name('cart.add');
    Route::get('checkout/cart/remove/{id}', 'CartController@remove')->name('cart.remove');
    Route::post('/checkout/cart', 'CartController@updateBeforeCheckout')->name('checkout.cart.update');
    Route::get('/checkout/cart/remove/{id}', 'CartController@remove')->name('checkout.cart.remove');
    Route::get('/checkout/onepage', 'OnepageController@index')->name('checkout.onepage.index');
    Route::get('/checkout/summary', 'OnepageController@summary')->name('checkout.summary');
    Route::post('/checkout/save-address', 'OnepageController@saveAddress')->name('checkout.save-address');
    Route::post('/checkout/save-shipping', 'OnepageController@saveShipping')->name('checkout.save-shipping');
    Route::post('/checkout/save-payment', 'OnepageController@savePayment')->name('checkout.save-payment');
    Route::post('/checkout/save-order', 'OnepageController@saveOrder')->name('checkout.save-order');
    Route::get('/checkout/success', 'OnepageController@success')->name('checkout.success');
    Route::get('move/wishlist/{id}', 'CartController@moveToWishlist')->name('movetowishlist');
    Route::get('/downloadable/download-sample/{type}/{id}', 'ProductController@downloadSample')->name('downloadable.download_sample');
    Route::get('/reviews/{slug}', 'ReviewController@show')->name('reviews.index');
    Route::get('/product/{slug}/review', 'ReviewController@create')->name('reviews.create');
    Route::post('/product/{slug}/review', 'ReviewController@store')->name('reviews.store');
    Route::get('/product/{id}/{attribute_id}', 'ProductController@download')->defaults('_config', [
        'view' => 'shop.products.index'
    ])->name('product.file.download');

    //customer routes starts here
    Route::prefix('customer')->group(function () {
        Route::get('/forgot-password', 'ForgotPasswordController@create')->name('customer.forgot-password.create');
        Route::post('/forgot-password', 'ForgotPasswordController@store')->name('customer.forgot-password.store');
        Route::get('/reset-password/{token}', 'ResetPasswordController@create')->name('customer.reset-password.create');
        Route::post('/reset-password', 'ResetPasswordController@store')->name('customer.reset-password.store');
        Route::get('login', 'SessionController@show')->name('customer.session.index');
        Route::post('login', 'SessionController@create')->name('customer.session.create');
        Route::get('register', 'RegistrationController@show')->name('customer.register.index');
        Route::post('register', 'RegistrationController@create')->name('customer.register.create');
        Route::get('/verify-account/{token}', 'RegistrationController@verifyAccount')->name('customer.verify');
        Route::get('/resend/verification/{email}', 'RegistrationController@resendVerificationEmail')->name('customer.resend.verification-email');
        Route::post('/customer/exist', 'OnepageController@checkExistCustomer')->name('customer.checkout.exist');
        Route::post('/customer/checkout/login', 'OnepageController@loginForCheckout')->name('customer.checkout.login');

        Route::group(['middleware' => ['customer']], function () {
            Route::get('logout', 'SessionController@destroy')->name('customer.session.destroy');
            Route::get('wishlist/add/{id}', 'WishlistController@add')->name('customer.wishlist.add');
            Route::get('wishlist/remove/{id}', 'WishlistController@remove')->name('customer.wishlist.remove');
            Route::get('wishlist/removeall', 'WishlistController@removeAll')->name('customer.wishlist.removeall');
            Route::get('wishlist/move/{id}', 'WishlistController@move')->name('customer.wishlist.move');

            Route::prefix('account')->group(function () {
                Route::get('profile', 'CustomerController@index')->name('customer.profile.index');
                Route::get('profile/edit', 'CustomerController@edit')->name('customer.profile.edit');
                Route::post('profile/edit', 'CustomerController@update')->name('customer.profile.store');
                Route::post('profile/destroy', 'CustomerController@destroy')->name('customer.profile.destroy');

                Route::get('addresses', 'AddressController@index')->name('customer.address.index');
                Route::get('addresses/create', 'AddressController@create')->name('customer.address.create');
                Route::post('addresses/create', 'AddressController@store')->name('customer.address.store');
                Route::get('addresses/edit/{id}', 'AddressController@edit')->name('customer.address.edit');
                Route::put('addresses/edit/{id}', 'AddressController@update')->name('customer.address.update');
                Route::get('addresses/default/{id}', 'AddressController@makeDefault')->name('make.default.address');
                Route::get('addresses/delete/{id}', 'AddressController@destroy')->name('address.delete');

                Route::get('wishlist', 'WishlistController@index')->name('customer.wishlist.index');
                Route::get('orders', 'OrderController@index')->name('customer.orders.index');

                Route::get('downloadable-products', 'DownloadableProductController@index')->name('customer.downloadable_products.index');
                Route::get('downloadable-products/download/{id}', 'DownloadableProductController@download')->name('customer.downloadable_products.download');

                Route::get('orders/view/{id}', 'OrderController@view')->name('customer.orders.view');
                Route::get('orders/print/{id}', 'OrderController@print')->name('customer.orders.print');
                Route::get('/orders/cancel/{id}', 'OrderController@cancel')->name('customer.orders.cancel');

                Route::get('reviews', 'CustomerController@reviews')->name('customer.reviews.index');
                Route::get('reviews/delete/{id}', 'ReviewController@destroy')->name('customer.review.delete');
                Route::get('reviews/all-delete', 'ReviewController@deleteAll')->name('customer.review.deleteall');
            });
        });
    });

    Route::get('page/{slug}', 'PagePresenterController@presenter')->name('cms.page');

    Route::get('/comparison', 'ComparisonController@getComparisonList')->name('product.compare');
    Route::put('/comparison', 'ComparisonController@addCompareProduct')->name('customer.product.add.compare');
    Route::delete('/comparison', 'ComparisonController@deleteComparisonProduct')->name('customer.product.delete.compare');
    Route::get('/items-count', 'ComparisonController@getItemsCount')->name('product.item-count');
    Route::get('/detailed-products', 'ComparisonController@getDetailedProducts')->name('product.details');

    Route::group(['middleware' => ['customer']], function () {
        Route::get('/customer/account/comparison', 'ComparisonController@getCustomerComparisonList')->name('customer.product.compare');
    });


    Route::fallback('ProductsCategoriesProxyController@index')->name('productOrCategory.index');
});
