=== WP Accurate Form Data ===
Contributors: herwp2
Donate link: 
Tags: form validation, real time email validation, physical address validation, email validation, address validation
Requires at least: 3.3
Tested up to: 3.9.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The plugin performs E-mail and Physical Address validations automatically for most wordpress contact forms.

== Description ==

The plugin performs E-mail and Physical Address validations automatically for most wordpress contact or other forms that include an E-mail field and a Street Address field (possible also, Zip, City and Country).
You can perform queries in form with single Address field, or in forms with the whole address split into several fields like Address, City, Zip, Country.
The plugin does a good job at verifying both E-mails (it makes sure the email really exists, not just the syntax of email) and Addresses from almost any country in the world.
It has cool tooltips to indicate the user if the verifications succeeded or failed.
Free registration to get your API key that comes with free verifications package.

To provide this service we send the email and/or the address your users enter in your form to our servers to be verified in real time. So, to make it clear, inside the plugin there is call to our server (which performs the analysis of both emails and physical addresses) and it returns the response to your installed plugin.

== Installation ==

Install as you install any other plugin for wordpress, activate it.
Navigate to Settings and the only thing you need to complete are the "possible names" for Email and Address fields of your wordpress form(s). How do you do that ? Simple, use Chrome, navigate to your wordpress form, place the mouse on the Email, Address, Zip, City or Country field, right click and select "Inspect Element", the browser developer console will open showing you some code, dont fear, its easy. The code will look something like this:

<input class="inputText" type="text" id="yourAddress_10_1" name="yourAddress" value="">

what you need to copy is the value of the "name" property, in this case "yourAddress", then go to the plugin settings and put that value in the "Possible Names for the Address field" setting. Do the same for the rest of fields that your form has (your form does not need to have all the fields). If you want to verify email, the required setting is "Possible Names for the E-mail field" and if you want to verify addresses, the required setting is "Possible Names for the Address field", City, Zip and Country are optional in case you have a form with those fields.
You can specify the values for multiple forms, separating the values with (,).

For questions, suggestions or inquiries, please email: support@accurateformdata.com

== Frequently asked questions ==



== Screenshots ==

Here is a screenshot of it in action:

1. screenshot-1.png
2. screenshot-2.png

== Changelog ==



== Upgrade notice ==



== Author ==

Hern�n Marino

