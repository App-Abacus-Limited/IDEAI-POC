<?php

namespace Keywordrush\AffiliateEgg;

defined('\ABSPATH') || exit;

/**
 * ShopeecommyParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2021 keywordrush.com
 */
require_once dirname(__FILE__) . '/ShopeevnParser.php';

class ShopeecommyParser extends ShopeevnParser {

    protected $canonical_domain = 'https://shopee.com.my';
    protected $currency = 'MYR';

}
