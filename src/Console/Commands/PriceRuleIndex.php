<?php

namespace BagistoPackages\Shop\Console\Commands;

use Illuminate\Console\Command;
use BagistoPackages\Shop\Helpers\CatalogRuleIndex;

class PriceRuleIndex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:price-rule:index';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically updates catalog rule price index information (eg. rule_price)';

    /**
     * CatalogRuleIndex object
     *
     * @var CatalogRuleIndex
     */
    protected $catalogRuleIndexHelper;

    /**
     * Create a new command instance.
     *
     * @param CatalogRuleIndex $catalogRuleIndexHelper
     */
    public function __construct(CatalogRuleIndex $catalogRuleIndexHelper)
    {
        $this->catalogRuleIndexHelper = $catalogRuleIndexHelper;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->catalogRuleIndexHelper->reindexComplete();
    }
}
