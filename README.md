# PayPal and Payflow 3D Secure Example

This is a quick example I whipped up to show how to integrate Cardinal Cruise Standard with PayPal.

You can read more [here](https://www.paypal.com/uk/webapps/mpp/psd2), but the long and short of it is this: you only need to integrate 3D Secure if both your acquirer and the issuer are in the European Economic Area (EEA).  If you're a PayPal merchant, then this only applies to you if you're a UK merchant using Website Payments Pro.  If you're a merchant in any other country, or if you're using a PayPal-hosted checkout product (such as Express Checkout or Hosted Sole Solution), then you don't need to integrate 3D Secure directly -- PayPal will take care of it for you.

## Getting Started

You'll need to create a `config.php` file.  Look at `config.sample.php` for instructions.

If you're using Website Payments Pro (which will be 99% of UK merchants), look at the example files in the `paypal` folder.

If you're using Payflow, look at the example files in the `payflow` folder.

## Further Reading

- [Cardinal Cruise documentation](https://cardinaldocs.atlassian.net/wiki/spaces/CC/pages/131806/Getting+Started)
- [PayPal's 3D Secure documentation](https://developer.paypal.com/docs/classic/paypal-payments-pro/integration-guide/security-features/#3-d-secure-uk-only)