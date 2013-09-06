##Todo
- __Add Account page w/ editable profile page and badge stuff -Kevin Zweerink__ *****
- Working front end organizations input forms ****
- BUG. Cannot sign out after having just changed password from the account page.****
- Add International Support ***
- Implement bookmarks ***
- add logo favicon ***
- make a distinct visited color or something to notify users which links they have already seen. ***
- update contact us on API doc ***
- get contact us email… perhaps info@indexd.io ***
- Add about page -Kevin Zweerink ***
- no results page handling ***
- Responsive Stuff -Kevin Zweerink **
- give api its own repo **
- Make sure no echo statements will ever show up topside **
- Verified system to easily check for real peeps on our site *
- Add API Error Handling *
- Implement Welcome page following registration with badge info -Kevin Zweerink *


##Todo for API Builder lib migration
- Remove InsertUpdate class
- Optimize ContentOutput class (should act as front end/back end proxy on all pages where content is delivered)
- Make an `api_config.inc.php` include file that has our database setup.
- Remove all instances of `API::query_results_as_array_of_JSON_objs()`
- Remove all instances of `API::output_objects()`
- Remove all instances of `API::get_error)()`
- Completely remove the old API and Database classes and replace them with the new ones
- Remove `Database::init_connection()` because it is included in the API constructor because it will be inside of the `api_config.inc.php`.

##Things to talk about
- Maybe actually display the badges they could use. From an interaction standpoint this could provide a faster way for a user to select the badge that works best for them. 


##Completed

- add front end form validation on sign in page and "email already exists" message on registration page. see includes/email_exists_test.php **** 
- handle front end zip lookup fails ***
- Front end bookmark icons (that are actually single input form submit buttons) ****
- Search relevancy recursion
- Make sure API & PrivateAPI ONLY output UTF-8 JSON
- Dynamically change options for signed in vs. not
- Sign Out functional
- Check emails on registration for duplicates
- Email Confirmation
- Add logout
- Tweak Header -Kevin Zweerink
- Design Badge -Kevin Zweerink
- Fill DB w/ meaningful data for testing
- make media list links individually clickable as well. like tags.
- Test API
- add API documentation
- Tweak home page -Kevin Zweerink
- dont allow users to sign up with an email that already exists in the db -Brannon
- handle zipcode lookup failures by not allowing users to register
- Make login page bring you to your profile
- make exclude API parameter accept comma-delimeted list of ids
- Add API link (to the github page)  -Kevin Zweerink 
- Tweak listing page -Kevin Zweerink
- Change how tags are searched from listing page -Kevin Zweerink
- Add description to register page (tooltips?) PLACEHOLDERS **
- fix search text color (to hard to see PLACEHOLDER) ****
- Reset Password -Brannon Dorsey ****
- change "url" in register page to "website" ****
- capitalize both words in multi word tags and media?
NO: no caps on tags and caps every word on media ****
- update everything on github from AWU to indexd ***
- Implement API key call limits ***
- Add front end reset password email input on sign in page ****
- Make API not output anything if no params ****
- Make passwords Bycript ****
- Add organizations to db and algorithm and api ****
- Add relational algorithm parameter to API ***
