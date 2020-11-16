<?php

namespace BagistoPackages\Shop\Providers;

use BagistoPackages\Shop\Tree;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Validator;
use BagistoPackages\Shop\Models\Customer;
use Konekt\Concord\BaseBoxServiceProvider;
use BagistoPackages\Ui\ViewRenderEventManager;
use BagistoPackages\Shop\View\Compilers\BladeCompiler;

class ModuleServiceProvider extends BaseBoxServiceProvider
{
    protected $models = [
        \BagistoPackages\Shop\Models\Channel::class,
        \BagistoPackages\Shop\Models\CoreConfig::class,
        \BagistoPackages\Shop\Models\Country::class,
        \BagistoPackages\Shop\Models\CountryTranslation::class,
        \BagistoPackages\Shop\Models\CountryState::class,
        \BagistoPackages\Shop\Models\CountryStateTranslation::class,
        \BagistoPackages\Shop\Models\Currency::class,
        \BagistoPackages\Shop\Models\CurrencyExchangeRate::class,
        \BagistoPackages\Shop\Models\Locale::class,
        \BagistoPackages\Shop\Models\Slider::class,
        \BagistoPackages\Shop\Models\SubscribersList::class,

        \BagistoPackages\Shop\Models\Attribute::class,
        \BagistoPackages\Shop\Models\AttributeFamily::class,
        \BagistoPackages\Shop\Models\AttributeGroup::class,
        \BagistoPackages\Shop\Models\AttributeOption::class,
        \BagistoPackages\Shop\Models\AttributeOptionTranslation::class,
        \BagistoPackages\Shop\Models\AttributeTranslation::class,

        \BagistoPackages\Shop\Models\BookingProduct::class,
        \BagistoPackages\Shop\Models\BookingProductDefaultSlot::class,
        \BagistoPackages\Shop\Models\BookingProductAppointmentSlot::class,
        \BagistoPackages\Shop\Models\BookingProductEventTicket::class,
        \BagistoPackages\Shop\Models\BookingProductEventTicketTranslation::class,
        \BagistoPackages\Shop\Models\BookingProductRentalSlot::class,
        \BagistoPackages\Shop\Models\BookingProductTableSlot::class,
        \BagistoPackages\Shop\Models\Booking::class,

        \BagistoPackages\Shop\Models\CartRule::class,
        \BagistoPackages\Shop\Models\CartRuleTranslation::class,
        \BagistoPackages\Shop\Models\CartRuleCustomer::class,
        \BagistoPackages\Shop\Models\CartRuleCoupon::class,
        \BagistoPackages\Shop\Models\CartRuleCouponUsage::class,

        \BagistoPackages\Shop\Models\CatalogRule::class,
        \BagistoPackages\Shop\Models\CatalogRuleProduct::class,
        \BagistoPackages\Shop\Models\CatalogRuleProductPrice::class,

        \BagistoPackages\Shop\Models\Category::class,
        \BagistoPackages\Shop\Models\CategoryTranslation::class,

        \BagistoPackages\Shop\Models\Cart::class,
        \BagistoPackages\Shop\Models\CartAddress::class,
        \BagistoPackages\Shop\Models\CartItem::class,
        \BagistoPackages\Shop\Models\CartPayment::class,
        \BagistoPackages\Shop\Models\CartShippingRate::class,

        \BagistoPackages\Shop\Models\Customer::class,
        \BagistoPackages\Shop\Models\CustomerCompareProduct::class,
        \BagistoPackages\Shop\Models\CustomerAddress::class,
        \BagistoPackages\Shop\Models\CustomerGroup::class,
        \BagistoPackages\Shop\Models\Wishlist::class,

        \BagistoPackages\Shop\Models\Admin::class,
        \BagistoPackages\Shop\Models\Role::class,

        \BagistoPackages\Shop\Models\InventorySource::class,

        \BagistoPackages\Shop\Models\TaxCategory::class,
        \BagistoPackages\Shop\Models\TaxMap::class,
        \BagistoPackages\Shop\Models\TaxRate::class,

        \BagistoPackages\Shop\Models\Product::class,
        \BagistoPackages\Shop\Models\ProductAttributeValue::class,
        \BagistoPackages\Shop\Models\ProductFlat::class,
        \BagistoPackages\Shop\Models\ProductImage::class,
        \BagistoPackages\Shop\Models\ProductInventory::class,
        \BagistoPackages\Shop\Models\ProductOrderedInventory::class,
        \BagistoPackages\Shop\Models\ProductReview::class,
        \BagistoPackages\Shop\Models\ProductSalableInventory::class,
        \BagistoPackages\Shop\Models\ProductDownloadableSample::class,
        \BagistoPackages\Shop\Models\ProductDownloadableLink::class,
        \BagistoPackages\Shop\Models\ProductGroupedProduct::class,
        \BagistoPackages\Shop\Models\ProductBundleOption::class,
        \BagistoPackages\Shop\Models\ProductBundleOptionTranslation::class,
        \BagistoPackages\Shop\Models\ProductBundleOptionProduct::class,
        \BagistoPackages\Shop\Models\ProductCustomerGroupPrice::class,

        \BagistoPackages\Shop\Models\Order::class,
        \BagistoPackages\Shop\Models\OrderItem::class,
        \BagistoPackages\Shop\Models\DownloadableLinkPurchased::class,
        \BagistoPackages\Shop\Models\OrderAddress::class,
        \BagistoPackages\Shop\Models\OrderPayment::class,
        \BagistoPackages\Shop\Models\OrderComment::class,
        \BagistoPackages\Shop\Models\Invoice::class,
        \BagistoPackages\Shop\Models\InvoiceItem::class,
        \BagistoPackages\Shop\Models\Shipment::class,
        \BagistoPackages\Shop\Models\ShipmentItem::class,
        \BagistoPackages\Shop\Models\Refund::class,
        \BagistoPackages\Shop\Models\RefundItem::class,

        \BagistoPackages\Shop\Models\CmsPage::class,
        \BagistoPackages\Shop\Models\CmsPageTranslation::class
    ];

    public function boot()
    {
        parent::boot();

        $router = $this->app['router'];

        config()->set('database.connections.mysql.strict', false);

        config()->set('auth.providers', array_merge(config('auth.providers'), [
            'customer' => [
                'driver' => 'eloquent',
                'model' => Customer::class
            ]
        ]));

        config()->set('auth.guards', array_merge(config('auth.guards'), [
            'customer' => [
                'driver' => 'session',
                'provider' => 'customer'
            ]
        ]));

        $this->app->bind(\Illuminate\Contracts\Debug\ExceptionHandler::class, \BagistoPackages\Shop\Exceptions\Handler::class);

        $router->aliasMiddleware('theme', \BagistoPackages\Shop\Http\Middleware\Theme::class);
        $router->aliasMiddleware('locale', \BagistoPackages\Shop\Http\Middleware\Locale::class);
        $router->aliasMiddleware('currency', \BagistoPackages\Shop\Http\Middleware\Currency::class);
        $router->aliasMiddleware('admin', \BagistoPackages\Shop\Http\Middleware\Bouncer::class);
        $router->aliasMiddleware('customer', \BagistoPackages\Shop\Http\Middleware\RedirectIfNotCustomer::class);

        Validator::extend('slug', 'BagistoPackages\Shop\Contracts\Validations\Slug@passes');
        Validator::extend('code', 'BagistoPackages\Shop\Contracts\Validations\Code@passes');
        Validator::extend('decimal', 'BagistoPackages\Shop\Contracts\Validations\Decimal@passes');

        \BagistoPackages\Shop\Models\SliderProxy::observe(\BagistoPackages\Shop\Observers\SliderObserver::class);
        \BagistoPackages\Shop\Models\ProductProxy::observe(\BagistoPackages\Shop\Observers\ProductObserver::class);
        \BagistoPackages\Shop\Models\CategoryProxy::observe(\BagistoPackages\Shop\Observers\CategoryObserver::class);

        Paginator::defaultView('shop::partials.pagination');
        Paginator::defaultSimpleView('shop::partials.pagination');

        Event::listen('bagisto.shop.layout.body.after', static function (ViewRenderEventManager $viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('shop::blade.tracer.style');
        });

        Event::listen('bagisto.admin.layout.head', static function (ViewRenderEventManager $viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('shop::blade.tracer.style');
        });

        Event::listen('checkout.order.save.after', 'BagistoPackages\Shop\Listeners\Order@afterPlaceOrder');
        Event::listen('checkout.order.save.after', 'BagistoPackages\Shop\Listeners\Order@manageCartRule');
        Event::listen('checkout.cart.collect.totals.before', 'BagistoPackages\Shop\Listeners\Cart@applyCartRules');
        Event::listen('catalog.product.update.after', 'BagistoPackages\Shop\Listeners\Product@createProductRuleIndex');
        Event::listen('catalog.attribute.create.after', 'BagistoPackages\Shop\Listeners\ProductFlat@afterAttributeCreatedUpdated');
        Event::listen('catalog.attribute.update.after', 'BagistoPackages\Shop\Listeners\ProductFlat@afterAttributeCreatedUpdated');
        Event::listen('catalog.attribute.delete.before', 'BagistoPackages\Shop\Listeners\ProductFlat@afterAttributeDeleted');
        Event::listen('catalog.product.create.after', 'BagistoPackages\Shop\Listeners\ProductFlat@afterProductCreatedUpdated');
        Event::listen('catalog.product.update.after', 'BagistoPackages\Shop\Listeners\ProductFlat@afterProductCreatedUpdated');

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'shop');

        $this->composeView();

        $this->publishes([
            __DIR__ . '/../Resources/config/elastic.scout_driver.php' => config_path('elastic.scout_driver.php'),
            __DIR__ . '/../Resources/config/db-blade-compiler.php' => config_path('db-blade-compiler.php'),
            __DIR__ . '/../Resources/config/elastic.client.php' => config_path('elastic.client.php'),
            __DIR__ . '/../Resources/config/imagecache.php' => config_path('imagecache.php'),
            __DIR__ . '/../Resources/config/themes.php' => config_path('themes.php'),
            __DIR__ . '/../Resources/config/scout.php' => config_path('scout.php'),
        ]);

        $this->publishes([
            __DIR__ . '/../Resources/views' => resource_path('views/vendor/themes/default'),
            __DIR__ . '/../Resources/lang' => resource_path('lang/vendor/shop'),
        ]);

        $this->publishes([
            __DIR__ . '/../../publishable/assets' => public_path('themes/default/assets'),
        ], 'public');
    }

    public function register()
    {
        $this->registerConfig();

        $this->registerCore();
        $this->registerCart();
        $this->registerBouncer();
        $this->registerPayment();
        $this->registerShipping();

        $this->registerCommands();
        $this->registerBladeCompiler();

        parent::register();
    }

    protected function registerRoutes($routes): void
    {
        $path = __DIR__ . '/../Resources/routes';

        $routeFiles = collect(File::glob($path . '/*.php'))
            ->map(function ($file) {
                return File::name($file);
            })
            ->all();

        foreach ($routeFiles as $file) {
            Route::group([
                'middleware' => ['web'],
                'as' => $this->shortName() . '.',
                'namespace' => sprintf('%s\\%s', $this->getNamespaceRoot(), str_replace('/', '\\', $this->convention->controllersFolder())),
            ], sprintf('%s/%s.php', $path, $file));
        }
    }


    protected function composeView()
    {
        view()->composer('shop::customers.account.partials.sidemenu', function ($view) {
            $tree = Tree::create();

            foreach (config('menu.customer') as $item) {
                $tree->add($item, 'menu');
            }

            $tree->items = core()->sortItems($tree->items);

            $view->with('menu', $tree);
        });
    }

    protected function registerConfig()
    {
        $this->mergeConfigFrom(dirname(__DIR__) . '/Resources/config/scout.php', 'scout');
        $this->mergeConfigFrom(dirname(__DIR__) . '/Resources/config/system.php', 'core');
        $this->mergeConfigFrom(dirname(__DIR__) . '/Resources/config/themes.php', 'themes');
        $this->mergeConfigFrom(dirname(__DIR__) . '/Resources/config/carriers.php', 'carriers');
        $this->mergeConfigFrom(dirname(__DIR__) . '/Resources/config/menu.php', 'menu.customer');
        $this->mergeConfigFrom(dirname(__DIR__) . '/Resources/config/imagecache.php', 'imagecache');
        $this->mergeConfigFrom(dirname(__DIR__) . '/Resources/config/productTypes.php', 'product_types');
        $this->mergeConfigFrom(dirname(__DIR__) . '/Resources/config/paymentMethods.php', 'paymentmethods');
        $this->mergeConfigFrom(dirname(__DIR__) . '/Resources/config/db-blade-compiler.php', 'db-blade-compiler');
        $this->mergeConfigFrom(dirname(__DIR__) . '/Resources/config/elastic.client.php', 'elastic.client');
        $this->mergeConfigFrom(dirname(__DIR__) . '/Resources/config/elastic.scout_driver.php', 'elastic.scout_driver');
    }

    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \BagistoPackages\Shop\Console\Commands\Install::class,
                \BagistoPackages\Shop\Console\Commands\ExchangeRateUpdate::class,
                \BagistoPackages\Shop\Console\Commands\BookingCron::class,
                \BagistoPackages\Shop\Console\Commands\PriceRuleIndex::class,
                \BagistoPackages\Shop\Console\Commands\PriceUpdate::class,
                \BagistoPackages\Shop\Console\Commands\GenerateProducts::class,
            ]);
        }
    }

    public function registerBladeCompiler()
    {
        $this->app->singleton('blade.compiler', function ($app) {
            return new BladeCompiler($app['files'], $app['config']['view.compiled']);
        });
    }

    protected function registerCore()
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('core', \BagistoPackages\Shop\Facades\Core::class);

        $this->app->singleton('core', function () {
            return app()->make(\BagistoPackages\Shop\Core::class);
        });
    }

    protected function registerCart()
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('cart', \BagistoPackages\Shop\Facades\Cart::class);

        $this->app->singleton('cart', function () {
            return new \BagistoPackages\Shop\Facades\Cart();
        });

        $this->app->bind('cart', \BagistoPackages\Shop\Cart::class);
    }

    protected function registerBouncer()
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('Bouncer', \BagistoPackages\Shop\Facades\Bouncer::class);

        $this->app->singleton('bouncer', function () {
            return new \BagistoPackages\Shop\Bouncer();
        });
    }

    protected function registerPayment()
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('payment', \BagistoPackages\Shop\Facades\Payment::class);

        $this->app->singleton('payment', function () {
            return new \BagistoPackages\Shop\Payment();
        });
    }

    protected function registerShipping()
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('shipping', \BagistoPackages\Shop\Facades\Shipping::class);

        $this->app->singleton('shipping', function () {
            return new \BagistoPackages\Shop\Shipping();
        });
    }
}
