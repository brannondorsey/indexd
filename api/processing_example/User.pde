class User {
  //This class translates a JSON user object from the API
  //into a class in Processing. In this example it is the
  //super class for UserBall.
  
  int id;
  String firstName;
  String lastName;
  String url;
  String city;
  String state;
  String country;
  int zip;
  float lat, lon;
  String datetimeJoined;
  String description;
  String[] media;
  String[] tags;
  int likes;

  User(JSONObject user) {
    id = user.getInt("id");
    firstName = user.getString("first_name");
    lastName = user.getString("last_name");
    url = user.getString("url");
    city = user.getString("city");
    state = user.getString("state");
    country = user.getString("country");
    zip = user.getInt("zip");
    lat = user.getFloat("lat");
    lon = user.getFloat("lon");
    datetimeJoined = user.getString("datetime_joined");
    description = user.getString("description");
    String mediaString = user.getString("media");
    media = split(mediaString, ", ");
    String tagsString = user.getString("tags");
    tags = split(tagsString, ", ");
    likes = user.getInt("likes");
  }
}

