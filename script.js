const $ = jQuery;

class SurCharge {
  constructor() {
    this.isCheckout = $('body').hasClass('woocommerce-checkout');
    this.isCart = $('body').hasClass('woocommerce-cart');
    this.cartSurcharge = $('.cart_totals .cart-collaterals');

    this.events();
  }

  events() {
    this.addInfoMarkup();

    $('body').on('updated_cart_totals', () => {
      this.addInfoMarkup();
    });

    $('body').on('updated_checkout', () => {
      this.addInfoMarkup();
    });
  }

  addInfoMarkup() {
    let el;

    if (this.isCart) {
      el = $('.cart_totals .fee th');
    } else if (this.isCheckout) {
      el = $('#order_review .fee th');
    }

    if (el) {
      el.addClass('surcharge');
      el.append(`<span class="surcharge-icon" style="--color: ${bpData.color}; --bg-color: ${bpData.bgColor};">
        <svg fill="var(--bg-color)" width="100%" height="100%" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;">
            <g transform="matrix(0.584558,0,0,0.655149,-387.803,-328.073)">
                <rect id="Artboard1" x="663.413" y="500.761" width="54.742" height="48.844" style="fill:none;"/>
                <clipPath id="_clip1">
                    <rect id="Artboard11" serif:id="Artboard1" x="663.413" y="500.761" width="54.742" height="48.844"/>
                </clipPath>
                <g clip-path="url(#_clip1)">
                    <g transform="matrix(1.71069,0,0,1.52637,-486.798,-273.773)">
                        <path d="M688.365,507.435C697.196,507.435 704.365,514.605 704.365,523.435C704.365,532.266 697.196,539.435 688.365,539.435C679.534,539.435 672.365,532.266 672.365,523.435C672.365,514.605 679.534,507.435 688.365,507.435ZM690.531,520.821L686.199,520.821L686.199,532.504L690.531,532.504L690.531,520.821ZM688.365,514.367C687.665,514.367 687.085,514.589 686.626,515.034C686.166,515.479 685.936,516.03 685.936,516.686C685.936,517.342 686.166,517.893 686.626,518.338C687.085,518.783 687.665,519.005 688.365,519.005C689.065,519.005 689.645,518.783 690.104,518.338C690.564,517.893 690.793,517.342 690.793,516.686C690.793,516.03 690.564,515.479 690.104,515.034C689.645,514.589 689.065,514.367 688.365,514.367Z"/>
                    </g>
                </g>
            </g>
        </svg>  
        <span class="surcharge-info">${bpData.info}</span>
      </span>`);
    }
  }
}

const surCharge = new SurCharge();
