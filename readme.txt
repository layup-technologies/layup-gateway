=== LayUp payment gateway plugin for woocommerce ===

Contributors: dylanweb980505, zanderrootman
Author URI: https://layup.co.za/
Author: LayUp Dev Team
Tags: woocommerce, payment gateway, South Africa, LayUp
Requires at least: 4.6
Tested up to: 5.4
Stable tag: 1.3.1
Version: 1.3.1
Requires PHP: 5.6
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

The Official LayUp payment gateway plugin for woocommerce.


== Description ==

LayUp provides an automated payment plan solution that integrates seamlessly with your existing Wordpress e-commerce site at checkout via our Woocomerce Plugin. LayUp offers your consumers an alternative option to pay for goods/services by making monthly or weekly, interest-free instalments with no setup costs or contracts. Start accepting payment plans today.

= Advantages =

* Increased Conversions - LayUp converts browsers into shoppers with an interest free payment solution online.
* Increased Basket Size & Repeat Purchase Rates - LayUp customers spend more per transaction over their lifetime, & they return to your site more often to LayUp their purchase.
* Decreased Drop Off at Checkout - LayUp reduces drop off & puts your items within reach of more customers who previously wouldn’t have been able to transact with you.
* Gamification Tools - LayUp gives you the cutting edge over competitors by providing a Gamification tool that incentivises your customers along the payment plan.

= How it Works - Customer =

* At checkout, the customer chooses to pay using LayUp and thereafter is required to pay a small deposit (determined by you, the merchant) in order to secure their chosen items at the advertised price and to activate their payment plan. 
* The customer has the flexibility to determine the number of weeks or months that they would like to pay over, provided that it’s within the duration parameters configured by the merchant. 
* The customer must then fulfil their obligation and only once a payment plan is complete may the goods/services be redeemed.

= How it Works - Merchant =

* Implementing our Woocomerce plugin to your Wodrpress e-commerce site allows for users to checkout using LayUp, an alternative payment solution.
* Orders are put into a partial state on the Woocommerce system to indicate that it's being paid off.
* LayUp allows your customers to pay through card or EFT payments at no additional cost to you.
* LayUp’s technology automatically collects monthly payments & will follow up with your clients to help them reach their goals.
* Orders are updated daily with the payments that have been made via LayUp for the order.
* LayUp allows merchants the ability to update their merchant account details at their convenience.
* Merchants can simply enter their API Keys (to authenticate themselves on the LayUp API) You can fetch this API Key by contacting merchantsupport.
* Should a merchant decide to remove the plugin, the plugin automatically reverts all the changes made to the system.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the woocommerce->Settings->Payments->LayUp screen to configure the plugin
4. ***Please make sure that all your products have SKUs set as the LayUp API requires it to make a purchase.

== Frequently Asked Questions ==

= How does using LayUp benefit me as a merchant? =
* Your customers do not need to complete an application process or enter into a loan agreement.
* There are no hidden fees and no interest is levied on the total value of goods/services purchased.
* Customers are not required to provide in-depth, personal information that would typically cause delays or for them to default entirely.
* Various payment plan terms are offered to the customer ranging from 1 week to 24 months.
* By offering your customers this option, our experience and market research shows that customers are more inclined to buy and/or will likely purchase even more as a result of increased affordability and the flexibility which LayUp provides
* By assisting customers to avoid unwarranted debt, you increase retention and thereby the likelihood of future purchases

= Why should I add LayUp as on option on my online store? =
The LayUp payment solution increases conversion rates, especially for customers who may have otherwise not been able to pay up-front, in full. Our offering reduces drop-off rates by incentivising prospective customers to convert sooner. There is also a far greater possibility of the customer increasing their basket size which provides a sustainable and economical way to retain your customers and, in turn, increasing the likelihood of future purchases.

= How do I sign up as a merchant? =
Simply visit our [website](https://layup.co.za/business/contact-us) and create your customised merchant profile by completing a few, simple steps. Our dedicated, merchant support team will then make contact to verify and activate your account. We look forward to having you on board!

= What reassurance is there that the LayUp solution works? =
LayUp’s bespoke technology is a B2B (and B2C) solution aimed at businesses who are constantly looking for innovative ways to increase sales. LayUp guarantees an affordable and sustainable alternative payment option that will increase sales through existing markets as well as customer retention by helping them avoid unwarranted debt.

= How does LayUp make their money? =
LayUp charges a percentage fee per payment plan created. This includes transactional and banking processing fees to accept online merchant payments. This fee structure will be negotiated on a per contract basis.

= How and when do I get paid? =
The deposit payment is transferred by EFT within 24hrs in order for you to reserve the purchase 24hrs. Depending on the size and volume of your business LayUp will either settle the residual payments weekly/monthly or once the customer has completed their payment plan. This will be confirmed during the on-boarding process.

= Do we have to commit to fixed terms with LayUp Technologies? =
Nope! We don’t believe in contractural commitments. LayUp only charges you when your customers decide to checkout with LayUp. We are so confident of our product/ service that we give you the flexibility to stop using our product at any time.

= What other features does LayUp Offer? =
* LayUp offers automated recurring billing
* Monthly collections and reconciliation
* 24/7 merchant and customer support
* Personalised merchant and consumer dashboards
* Split payment features for users to split the payment plan amongst guests

= What risk is there for me using LayUp? =
There is no risk at all! This is just one of the great features of using LayUp. You will never provide a customer with a product/service until they have paid for it in full and there is no risk of fraudulent payments being received and charge backs occurring.

= My customer wants to modify their payment plan, how do I handle this? =
Yes, sure you can amend your payment plan. Send through the required and approved updates and we will get in touch to process the amendments and adjust the payment plan accordingly.

== Screenshots ==

1. LayUp payment gateway settings in Woocommerce.
2. LayUp merchant settings in Woocommerce.
3. LayUp product settings.
4. LayUp product date settings, add multiple dates.
5. LayUp payment plans on Woocommerce My Account page.
6. LayUp estimate payment plan on single product page.
7. LayUp estimate payment plan on product archive/category pages.

== Changelog ==
= 1.3.0 =* Added custom woocommerce email for when a Layup order status changes to "Placed".* Fixed woocommerce emails not working.* Added support for Woocommerce 4.0
= 1.2.2 =

* Optimised the way existing products are updated with estimate payment plan to stop timeout on plugin activation.

= 1.2.1 =

* fix merchant ID always showing invalid.

= 1.2.0 =

* Reordered LayUp Settings.
* Added admin notice to sign up for a merchant account.
* Changed configuration prompt text.
* Moved all setting field descriptions from tool tips to underneath each field.

= 1.1.2 =

* New subdomain added.
* Added LayUp logo to the plugin.

= 1.1.0 =

* Added: Auto update products when plugin is activated.
* Added: Checkout notice if products in cart have the disable layup option enabled.
* Added: Notify URL
* Fixed: UI Bug where estimated payment was in the wrong place in the product loop

= 1.0.1 =
* Fixed redirect

= 1.0.0 =
* Initial Release
