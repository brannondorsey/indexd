#[Indexd](http://index.io) Public API

The Indexd http API gives developers access to public user data from the Indexd database. Our web API returns all data as valid `json` and is interfaced using `get` parameters in the url of an http request. It is very similar to Facebook's [Graph API](https://developers.facebook.com/docs/reference/api/) and considered a client-server [REST API](http://en.wikipedia.org/wiki/Representational_state_transfer). The following documentation will cover the url parameters used to access Indexd's public data and give a few examples illustrating how the data can be used. If you have never used a web API before or are uncomfortable passing data requests through a url using `get` we suggest reading [this](http://en.wikipedia.org/wiki/Query_string).

##<a id="getting-started"></a>Getting Started

###Formatting a request

Our Indexd database runs on [MySQL](https://en.wikipedia.org/wiki/MySQL) and so the http requests used to return user data are very similar to forming a MySQL `SELECT` query statement. If you have used MySQL before, think of using the Indexd `get` parameters as little pieces of a query. For instance, our `limit`, `order_by`, and `flow` (our nickname for MySQL `ORDER BY`'s `DESC` or `ASC`) parameters translate directly into a MySQL statement on our servers.

####<a id="example-request"></a>Example Request
     http://api.indexd.io?city=Richmond&limit=2&key=…
     
The above request would return the ten newest users with information related to "Richmond". This request is very similar to the way that the search bar works on the Indexd website. 

__Note:__ A valid API key must be provided with each request. Yours can be found in your "account" page on the Indexd website. Requests are limited to a tentative 1000 requests a day so as to keep our servers from getting bogged down. If you would like to request more please [contact us](COME BACK).

####Notable Parameters

- `search` uses a MySQL FULLTEXT search to find the most relevant results in our database to the parameter's value.
- `order_by` returns results ordered by the column name given as the parameter's value.
- `limit` specifies the number of returned results. If not included as a parameter the default value is `25`. Max value is `250`.
- `page` uses a MySQL `OFFSET` to return the contents of a hypothetical "page" of results in the database. Used most effectively when paired with `limit`.

A full list of the Indexd API parameters are specified in the [Parameter Reference](#parameter-reference) link to the section below) section of this Documentation.


###Returned JSON

All user data returned by the Indexd API is wrapped in a `json` object named `data`. If there is an error, or no results are found, an `error` object with a corresponding error message will be returned __instead__ of a `data` object. 

Inside the `data` object is an array of user objects that are returned as a result of the url parameters that will be outlined shortly.

```json
{
    "data": [
        {
            "id": "840",
            "first_name": "Lynn",
            "last_name": "Bennett",
            "url": "http://www.lynnbennett.com",
            "email": "lynnbennett@mailinator.com",
            "city": "Richmond",
            "state": "Virginia",
            "country": "us",
            "zip": "23242",
            "lat": "37.5313",
            "lon": "-77.4161",
            "datetime_joined": "2013-07-01T19:33:26+0200",
            "description": "is autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feug",
            "media": "printmaking",
            "tags": "adobe creative suite, large format, typography, 1960s, youngarts",
            "likes": "798"
        },
        {
            "id": "659",
            "first_name": "Tiffany",
            "last_name": "Brooks",
            "url": "http://www.tiffanybrooks.com",
            "email": "tiffanybrooks@mailinator.com",
            "city": "Richmond",
            "state": "Virginia",
            "country": "us",
            "zip": "23240",
            "lat": "37.5242",
            "lon": "-77.4932",
            "datetime_joined": "2013-07-01T19:32:52+0200",
            "description": "onsequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum ",
            "media": "interior design",
            "tags": "processing, emotional, modern, animation, dslr, 1960s, 3d",
            "likes": "902"
        }
     ]
 }
```

__Note:__ The `data` object always contains an array of `user` objects even if there is only one result.

The API allows developers access to each user's:

`id`, `first_name`, `last_name`, `url`, `email`, `city`, `state`, `country`, `zip`, `lat` (latitude), `lon` (longitude), `datetime_joined`, `description`, `media`, `tags`, and `likes` (known on Indexd as "bookmarks"). 

These user object properties correspond to the column names in our MySQL database.

__Note:__ the values of the user's `media` and `tags` properties are returned as a comma-space delimited list.

##Examples

Because the Indexd API outputs data using `JSON` the results of an API http request can be loaded into a project written in almost any popular language. We have chosen to provide brief code examples using `PHP`, however, these code snippets outline the basics of loading and using user data and easily apply to another language. 

###Using the Data

```php
<?php
$city = "Baltimore";
$state = "Maryland";
$media = "Sculpture";

$http_request = "http://localhost:8888/api/api.php?city=". $city
. "&state=" . $state . "&media=" . $media;
	
$json_string = file_get_contents($http_request);
$jsonObj = json_decode($json_string);
	
//loop through each user object inside of the "data" array
foreach($jsonObj->data as $user){
   //do something with each result inside of here...
   //for example, print some of their info to the browser
   echo "This user's first name is " . $user->first_name . "<br/>";
   echo "This user's last name is " . $user->last_name . "<br/>";
   echo "This user's website is " . $user->url . "<br/>";
   echo "<br/>";
}
?>
```

###Error Handling

Often requests to the Indexd API return no results because no users were found that met the request's criteria. For this reason it is important to know how to handle our API `errors`. Currently there are only three error messages that our API will ever output.

The `JSON` that is returned in these instances are:

- `{"error": "no results found"}`
- `{"error": "API key is invalid or was not provided"}`
- `{"error": "API hit limit reached"}`

Handling `errors` is simple. All that you need to do is check if the `error` property exists in the resulting `JSON` object. If it does execute the code for when an error is present. Otherwise, continue with the program because the request returned at least one user.

```php
<?php 
$city = "Baltimore";
$state = "Maryland";
$media = "Sculpture";

$http_request = "http://localhost:8888/api/api.php?city=". $city
 . "&state=" . $state . "&media=" . $media;

$json_string = file_get_contents($http_request);
$jsonObj = json_decode($json_string);
	
//check for an error
if(isset($jsonObj->error)){
	//code for handling the error goes in here...
	//for example, print the error message to the browser
	echo $jsonObj->error;

}else{
	//execute the code for when user objects are returned…
	//for example, list the ids of the resulting users
	foreach($jsonObj->data as $user){
		echo "User number " . $user->id . " was selected <br/>";
	}
}
?>
```
	

###Processing Example

We have included an example [Processing](http://processing.org) project in this repository to illustrate how the API might be used. The sketch represents user data from the Indexd API as small interactive balls. In the example applet, lines are drawn between two users if they share similar media or tags. The more similarities two users share the more opaque the connection lines drawn between them become. This creates an interactive relational "web" by which to explore the data set. When a user is moused over, their information appears on screen and all lines connecting to it are highlighted.

![Processing Example Image](images/processing_example.png)

The example source code is included for download. The files are heavily commented and although they uses some advanced OOP techniques, the example is relatively straightforward and was created to help developers understand how to program using the Indexd API. 

##<a id="parameter-reference"></a>Parameter Reference

This section documents in detail all of the Indexd API parameters currently available. 

###API Key Parameter

Each Indexd API request must include a unique API key identifying the user making the request. To keep our servers running smoothly each user is limited to 1000 requests a day. Users can find their personal key in their "Account" page of the Indexd website.

Parameter __key:__ `key`

Parameter __value:__ unique 40 character `sha1` key

__Example__: 

     http://api.indexd.io?last_name=Renolds&key=8a98253d8b01d4cf8c3fe183ef0862fa69a67b2e
     
__Note:__ Failing to include a valid API or making more than 1000 requests in a day will throw a `error` object in place of a `data` object.
     


###Column Parameters
Column parameters allow you to query any user's public data for a specific value where the url parameter key is specified to be the user's column in our database. Column parameters can be stacked for more specific queries.

Parameter __keys:__ Column name (i.e. `first_name`) to perform query on.

Parameter __values:__ 
Desired lookup `string`, `int`, or `float` that corresponds to the column name in the database as specified by the parameter's key.

__Example:__

      http://api.indexd.io?city=Richmond&state=Virginia&order_by=datetime_joined&limit=10&key=...
      
This example piggybacks off of the [example request](#example-request) used in the [Getting Started](#getting-started) section of this documentation. This request would yield more accurate results if the developer were looking for users who live in Richmond, Virginia. The previous method would have given results where the user's name, media, tags, etc… included Richmond.


__Notes:__ The column parameter's are overridden if a `search` parameter is specified. 

###Search Parameter
The `search` parameter uses a  MySQL `FULLTEXT` [Match()… Against()…](http://dev.mysql.com/doc/refman/5.5/en/fulltext-search.html#function_match) search to find the most relevant results to the searched string. This is the exact method that the search bar on the Indexd website uses. 

Parameter __key:__ `search`

Parameter __value:__ desired query `string`

__Example:__

	http://api.indexd.io?search=sculpture&limit=100&key=...

__Notes:__ `search` results are automatically ordered by relevancy, or if relevancy is found to be arbitrary, by `likes`. The `order_by` parameter cannot be used when the `search` parameter is specified. More on why below…

Default Match()…Against()… MySQL statements search databases using a 50% similarity threshold. This means that if a searched string appears in more than half of the rows in the database the search will ignore it. Because it is possible that many users will have similar tags, we have built Indexd to automatically re-search `IN BOOLEAN MODE` if no results are found in the first try. If results are found in the second search they are ordered by `likes`.

###Exact Parameter

The exact parameter is used in conjunction with the column parameters and specifies whether or not their values are queried with relative or exact accuracy. If not included in the url request the `exact` parameter defaults to `false`.

Parameter __key:__ `exact`

Parameter __values:__ `TRUE	` and `FALSE`

__Example:__

	http://api.indexd.io?media=design&exact=TRUE&limit=100&key=...

This request will limit the returned results to users whose media includes __only__ design. If the `exact` parameter was not specified, or was set to `FALSE`, the same request could also return users whose media were interior design and graphic design, or users who have more media listed in addition to design. Unless you are looking for user's with only one result specific for a column it is suggested to leave `exact` set to `FALSE`.
	
__Notes:__ `exact`'s values are case insensitive.

###Exclude Parameter

The exclude parameter is used in conjunction with the column parameters to exclude one or more users from a query.


Parameter __key:__ `exclude`

Parameter __values:__ a comma-delimited list of excluded user's `id`'s

__Example:__

	http://api.indexd.io?tags=contemporary&exclude=5,137,1489&limit=50&key=...

This example will return the first 50 users other than numbers `5`, `137`, and `1489` whose tags include contemporary 

###Order By Parameter

This parameter is used with the column parameters to sort the returned users by the specified value. If `order_by` is not specified its value defaults to `last_name`. Order by cannot be used when the `search` parameter is specified.

Parameter __key:__ `order_by`

Parameter __value:__ Column name (i.e. `first_name`) to order by

__Example:__

	http://api.indexd.io?tags=contemporary&order_by=country&limit=50&key=...

This request returns the first 50 users whose tags include "contemporary" ordered alphabetically by country.

__Notes:__ If the value of `order_by` is an `int` or a `float` the default `flow` of `ASC` will order the results from lowest specified column value to highest specified column value. If the value of `order_by` is set to number of `likes` for instance, `flow` should be set to `DESC`.

###Flow Parameter

This parameter specifies the MySQL `ASC` and `DESC` options that follow the `ORDER BY` statement. If `flow` is not specified it defaults to `ASC`.

Parameter __key:__ `flow`

Parameter __value:__ `ASC` or `DESC`

__Example:__

	http://api.indexd.io?tags=contemporary&order_by=likes&flow=DESC&key=...
	
This request specifies that the results should be ordered in a `DESC` fashion.
		
__Notes:__ `flow`'s values are case insensitive.

###Limit Parameter

The `limit` parameter works similarly to MySQL `LIMIT`. It specifies the max number of users to be returned. The default value, if unspecified is `25`. The max value of results that can be returned in one request is `250`.

Parameter __key:__ `limit`

Parameter __value:__ `int` between `1-250`

__Example:__

	http://api.indexd.io?city=Baltimore&limit=5&key=...

Returns the first five users from Baltimore alphabetically by last name.

	
###Page Parameter

The page parameter is used to keep track of what set (or page) of results are returned. This is similar to the [MySQL OFFSET statement](http://dev.mysql.com/doc/refman/5.0/en/select.html). If not specified the page value will default to `1`.

Parameter __key:__ `page`

Parameter __value:__ `int` greater than `0`

__EXAMPLE:__ 

	http://api.indexd.io?search=ancient%20architecture&limit=7&page=3&order_by=id&flow=desckey=...
	
This request will return the 3rd "page" of `search` results. 

For instance, if all users had the tag "ancient architecture", setting `page=1` would return users with id's `1-7`, setting `page=2` would yield `8-14`, etc…

__Note:__ The MySQL `OFFSET` is calculated server side by multiplying the value of `limit` by the value of `page` minus one. 

###Count Only Parameter

The `count only` parameter differs from all of the other Indexd API parameters as it __does not__ return an array of user objects. Instead, it returns a single object as the first element in the `data` array. This object has only one property, `count`, where the corresponding `int` value describes the number of results returned by the rest of the url parameters. If the `count_only` parameter is not specified the default value is `FALSE`. When `count_only` is set to `TRUE` the request will __only__ evaluate and return the number of results found by the rest of the url parameters and the request will not return any user data.

Parameter __key:__ `count_only`

Parameter __values:__ `	TRUE` or `FALSE`

__EXAMPLE:__

     //request
     http://api.indexd.io?media=film&count_only=true&key=...
     
     //returns
     {
      "data":[
        {
        "count":"701"
        }]
     }
     
This request returns the number of users who have specified "film" in their media list.

__Note:__ The value of `count_only` is case insensitive.

##License and Credit

The Indexd Public API is developed and maintained by [Brannon Dorsey](http://github.com/brannondorsey) and [Kevin Zweerink](http://github.com/kevinzweerink) and is published under the [MIT License](license.txt). If you notice any bugs, have any questions, or would like to help us with development please submit an issue or pull request, write about it on our wiki, or [contact us](COME BACK).

