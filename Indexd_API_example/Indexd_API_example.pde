import java.util.Arrays;

String APIRequest = "http://localhost:8888/api/api.php?state=california&order_by=likes&flow=DESC&limit=15";

DataHandler DataHand;
UserBall[] users;
InfoDisplay infoDisplay;

int maxSize = 50;
int minSize = 20;
int alphaIncrement = int(255/5);

color backgroundColor = #1d5b89; 
color largeEllipseColor = #ef7878;

void setup(){
  size(displayWidth, displayHeight);
  background(backgroundColor);
  smooth();
  noStroke();
  DataHand = new DataHandler(APIRequest);
  users = DataHand.loadUsers();
  infoDisplay = new InfoDisplay();
  calcSizes();
}

void draw(){
  background(backgroundColor);
  drawLargeEllipse();
  drawConnections();
  for(int i = 0; i < users.length; i++){
    if(users[i].isOver(mouseX, mouseY)){
      users[i].wiggle();
    }
    users[i].display();
  }
}

void mousePressed(){
  for(int i = 0; i < users.length; i++){
    if(users[i].isOver(mouseX, mouseY)){
      infoDisplay.display(users[i]);
    }
  }
}

void drawLargeEllipse(){
  fill(largeEllipseColor);
  ellipse(width/2, height/2, height, height);
  
}

//draws the connections between each user using media, tags, and location
void drawConnections(){
  int lineWeight = 2;
  color lineColor = #1d5b89;
  int highlightColor = 255;
  
  for(int i = 0; i < users.length; i++){
    for(int j = 0; j < users.length; j++){
      ArrayList<String> secondUsersMedia= new ArrayList<String>(Arrays.asList(users[j].media));
      ArrayList<String> secondUsersTags= new ArrayList<String>(Arrays.asList(users[j].tags));
      int alpha = 0;
      //set the transparency of the connection lines according to media
      alpha = getConnectionAlpha(alpha, users[i].media, secondUsersMedia);
      //do the same for tags
      alpha = getConnectionAlpha(alpha, users[i].tags, secondUsersTags);

      //if there was any kind of connection
      if(alphaIncrement != 0){
        alpha = (alpha > 255) ? 255 : alpha;
        color displayColor = (users[i].isOver(mouseX, mouseY) || users[j].isOver(mouseX, mouseY) ? highlightColor : lineColor);
        //draw the connection line
        strokeWeight(lineWeight);
        stroke(displayColor, alpha);
        line(users[i].x, users[i].y, users[j].x, users[j].y);
      } 
    }
  }
}

int getConnectionAlpha(int alpha, String[] userList, ArrayList<String> secondUserList){
    for(int k = 0; k <userList.length; k++){
      String firstUsersMedia = userList[k];
      //if the the one of the i users media is inside of the j user's
      if(secondUserList.contains(firstUsersMedia)){
        alpha += alphaIncrement; //found a connection so break out of the media test loop
      }
   }
   return alpha; //if connections were found return the original alpha
}

//finds the minimum and maximum like count of all returned users
void calcSizes(){
  int minLikes = 100000000; //set a very high minLike
  int maxLikes = 0;
  //calculate the most and least likes of the data set
  for(int i = 0; i < users.length; i++){
    minLikes = min(minLikes, users[i].likes);
    maxLikes = max(maxLikes, users[i].likes);
  }
  
  //set each UserBalls size
  for(int i = 0; i < users.length; i++){
    int s = (int) map(users[i].likes, minLikes, maxLikes, minSize, maxSize);
    users[i].setSize(s);
  }
}
