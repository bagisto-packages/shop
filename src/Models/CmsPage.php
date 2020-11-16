<?php

namespace BagistoPackages\Shop\Models;

use BagistoPackages\Shop\Eloquent\TranslatableModel;
use BagistoPackages\Shop\Contracts\CmsPage as CmsPageContract;

class CmsPage extends TranslatableModel implements CmsPageContract
{
    protected $fillable = ['layout'];

    public $translatedAttributes = [
        'content',
        'meta_description',
        'meta_title',
        'page_title',
        'meta_keywords',
        'html_content',
        'url_key',
    ];

    protected $with = ['translations'];

    /**
     * Get the channels.
     */
    public function channels()
    {
        return $this->belongsToMany(ChannelProxy::modelClass(), 'cms_page_channels');
    }
}
