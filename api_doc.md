#API Documentation

Example: api.php?media=painting&city=richmond&state=virginia&order\_by=last\_name


##Column Parameters

__keys__

+ first_name
+ last_name
+ url
+ email
+ city
+ state
+ country	
+ datetime_joined
+ description
+ media
+ tags (comma delimited)

__values__ match each key and include desired user information

_note:_ column parameters can stack.

##Limit Parameters

__keys__

+ limit

__values__

+ integer from [1 - 250]

_note:_ if parameter is unspecified value will default to 25.

##Order By Parameters

__keys__

+ order_by

__values__ are desired column names. 

_note:_ If parameter is unspecified order by defaults to first column parameter.

##Flow Parameters

__keys__

+ flow

__values__

+ ASC
+ DESC

_note:_ If parameter is unspecified flow defaults to order ascending. case insensitive.