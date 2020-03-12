<?php

namespace App\Shop\Carts;

use Gloudemans\Shoppingcart\Cart;
use Gloudemans\Shoppingcart\CartItem;
use Illuminate\Events\Dispatcher;
use Illuminate\Session\SessionManager;

use function config;

class ShoppingCart extends Cart
{
    public static $defaultCurrency;
    
    /**
     * @var SessionManager
     */
    protected $session;
    
    /**
     * @var Dispatcher
     */
    protected $event;
    
    /**
     * ShoppingCart constructor.
     */
    public function __construct()
    {
        $this->session = $this->getSession();
        $this->event   = $this->getEvents();
        
        parent::__construct($this->session, $this->event);
        
        self::$defaultCurrency = config('cart.currency');
    }
    
    /**
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getSession()
    {
        return app()->make('session');
    }
    
    /**
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getEvents()
    {
        return app()->make('events');
    }
    
    /**
     * Get the total price of the items in the cart.
     *
     * @param  int     $decimals
     * @param  string  $decimalPoint
     * @param  string  $thousandSeparator
     * @param  float   $shipping
     * @return string
     */
    public function total($decimals = null, $decimalPoint = null, $thousandSeparator = null, $shipping = 0.00)
    {
        $content = $this->getContent();
        
        $total = $content->reduce(
            static function ($total, CartItem $cartItem) {
                return $total + ($cartItem->qty * $cartItem->priceTax);
            },
            0
        );
        
        $grandTotal = $total + $shipping;
        
        return number_format($grandTotal, $decimals, $decimalPoint, $thousandSeparator);
    }
}
