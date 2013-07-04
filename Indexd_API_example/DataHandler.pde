class DataHandler{
 
 JSONObject data;
 JSONArray users;
 boolean loadedSuccessfully;
 
 DataHandler(String _APIRequest){
   data = loadJSONObject(APIRequest);
 }
 
 //called from inside setup.
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
