class DataHandler{
  
 JSONObject data;
 JSONArray users;
 boolean noUsersFound = false;
 String errorString = "";
 
 DataHandler(String _APIRequest){
   //make the http request from the Indexd API
   data = loadJSONObject(APIRequest);
   //check if there is an error. 
   //If so set the noUsersFound boolean to true
   try{errorString = data.getString("error");
   }catch(Exception e){}
   if(errorString != "") noUsersFound = true;
 }
 
 //creates a new UserBall for each user JSON object and places then in an array.
 //when it is finished, it returns the entire array of UserBalls. Called from inside setup.
 UserBall[] loadUsers(){
   JSONArray users = data.getJSONArray("data");
   UserBall[] usersArray = new UserBall[users.size()];
   for(int i = 0; i < users.size(); i++){
     JSONObject userObj = users.getJSONObject(i);
     usersArray[i] = new UserBall(userObj);
   }
   return usersArray;
 }
  
}
