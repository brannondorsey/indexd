import java.util.Arrays;
//change the APIRequest string to change the data you get. This could easily be dynamically generated.
String APIRequest = "http://localhost:8888/api/api.php?state=virginia&exact=true&order_by=likes&flow=desc&limit=10";

DataHandler dataHand;
UserBall[] users; //holds each user
InfoDisplay infoDisplay;

//min and max sizes of the UserBalls
int maxSize = 50;
int minSize = 20;

int alphaIncrement = int(255/7);

int overUserIndex = 0; //holds the index of the 

color backgroundColor = #1d5b89; 
color largeEllipseColor = #ef7878;

void setup() {
  size(displayWidth, displayHeight); 
  background(backgroundColor);
  smooth();
  noStroke();
  //the DataHand class holds the methods for making the API Request
  //and loading the JSON into the User class
  dataHand = new DataHandler(APIRequest);
  
  //load the user data into the array of UserBalls if there is no error
  if(!dataHand.noUsersFound){
    users = dataHand.loadUsers();
     //find out the minum and maximum "likes" count of the data set. 
     //Then set each UserBall's size according to their number of likes.
     calcSizes();
  }
  
  //InfoDisplay class handles the 
  infoDisplay = new InfoDisplay(0, 0, width, 170);
 
}

void draw() {
  //draw the blue background
  background(backgroundColor); 
  
  //draws red background circle
  fill(largeEllipseColor); 
  ellipse(width/2, height/2, height, height); 
  
  //if no users were found display the error message 
  if (dataHand.noUsersFound) infoDisplay.displayError(dataHand.errorString);
  //otherwise run the rest of the program
  else{
    //draw lines between UserBalls if they have a common media, tags, or a location
    drawConnections();
    
    //holds wether or not the mouse is over a user.
    //If it is set to true later we will show the user's data 
    boolean overUser = false;
  
    //loop through each UserBall...
    for (int i = 0; i < users.length; i++) {
      
      //check if the mouse is over this one.
      //If it is set it's "rollover" property to true
      users[i].rollover(mouseX, mouseY);
      
      //draw this UserBall to the screen
      users[i].display(mouseX, mouseY);
      
      //check if the UserBall needs to be dragged.
      //If so, drag it.
      users[i].drag(mouseX, mouseY);
      
      //if this UserBall is being hovered over
      //or being dragged
      if (users[i].rollover ||
        users[i].dragging) {
        overUser = true;
        
        //remember the location of this UserBall
        //to show it's data later
        overUserIndex = i;  
      }
    }//do all of that again for each UserBall...
    
    //display the user's info if a mouse was a UserBall
    if (overUser) infoDisplay.display(users[overUserIndex]); 
  }
}

void mousePressed() {
  //loop through all UserBall's and check if the mouse is over one. 
  //if it is then set it's offsets
  if(!dataHand.noUsersFound){
    for (int i = 0; i < users.length; i++) {
      users[i].clicked(mouseX, mouseY);
    }
  }
}

void mouseReleased() {
  //set all UserBall's dragging properties to false
  if(!dataHand.noUsersFound){
    for (int i = 0; i < users.length; i++) {
      users[i].stopDragging();
    }
  }
}

//automatically launch the program in fullscreen mode
boolean sketchFullScreen() {
  return true;
}

//draw the connections between each user using media, tags, and location. Called inside draw.
void drawConnections() {
  int lineWeight = 2;
  color lineColor = #1d5b89;
  int highlightColor = 255;

  //loop through all of the UserBalls
  for (int i = 0; i < users.length; i++) {
    //and check each one against all of the other UserBalls
    for (int j = 0; j < users.length; j++) {
      //turn the jth UserBall's media and tags array's into Java ArrayLists to have access to the contains function 
      ArrayList<String> secondUsersMedia= new ArrayList<String>(Arrays.asList(users[j].media));
      ArrayList<String> secondUsersTags= new ArrayList<String>(Arrays.asList(users[j].tags));
      
      //begin the connection lines totally transparent
      int alpha = 0;
      
      //add opacity to the connection line if the ith UserBall and the jth user media
      alpha = getConnectionAlpha(alpha, users[i].media, secondUsersMedia);
      
      //do the same for the tags
      alpha = getConnectionAlpha(alpha, users[i].tags, secondUsersTags);

      //if the alpha value is no longer zero because there were matches found
      //between the user's media or tags, draw the connection line
      if (alphaIncrement != 0) {
        
        //if the alpha is greater than 255 set it to 255
        alpha = (alpha > 255) ? 255 : alpha;
        
        //if the mouse is over either the ith piece or the jth piece 
        //set the display color of the lines to the highlight color
        color displayColor = ((users[i].rollover || users[i].dragging) ||
          (users[j].rollover || users[j].dragging) ? highlightColor : lineColor);
          
        //draw the connection line between the ith UserBall and the jth UserBall
        strokeWeight(lineWeight);
        stroke(displayColor, alpha);
        line(users[i].x, users[i].y, users[j].x, users[j].y);
      }
    }//check the ith UserBall against all of the rest of the jth UserBalls
  }//do it all again for the next the next UserBall in the users array
}

//returns an alpha value according to the number of matches it findes between
//the String array and String ArrayList it is passed. Called inside of drawConnections().
int getConnectionAlpha(int alpha, String[] userList, ArrayList<String> secondUserList) {
  for (int k = 0; k <userList.length; k++) {
    String firstUsersList = userList[k];
    //if one of the ith user's media is inside of the jth user's media increase the alpha value
    if (secondUserList.contains(firstUsersList)) {
      alpha += alphaIncrement; //found a connection so break out of the media test loop
    }
  }//do the same for each item in the userList
  return alpha; //return the new alpha value (even if it is unchanged)
}

//finds the minimum and maximum "like" count of all returned users
//and then assign each UserBall in the users array a size accordingly
void calcSizes() {
  int minLikes = 100000000; //set a very high minLike
  int maxLikes = 0;
  
  //calculate the most and least likes of the data set
  for (int i = 0; i < users.length; i++) {
    minLikes = min(minLikes, users[i].likes);
    maxLikes = max(maxLikes, users[i].likes);
  }

  //set each UserBalls ellipse size
  for (int i = 0; i < users.length; i++) {
    int s = (int) map(users[i].likes, minLikes, maxLikes, minSize, maxSize);
    users[i].setSize(s);
  }
}

