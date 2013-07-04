class InfoDisplay{
  
  int x, y, h, w;
  PFont font;
  
  InfoDisplay(){
    font = loadFont("franklin_gothic.ttf");
  }
  
  //sets up the info display box size and location. Called inside setup.
  void setBox(int _x, int _y, int _h, int _w){
    x = _x;
    y = _y;
    h = _h;
    w = _w;
  }
  
  void display(UserBall user){
    int textX = x;
    int textY = y;
    int marginTop = 40;
    int marginLeft = 15;
    int nameSize = 18;
    textY += marginTop;
    textY += marginLeft;
    
    //colors
    color boxColor = 255;
    color nameColor = user.userColor;
    
    //draw background box
    fill(boxColor);
    rect(x, y, w, h);
    
    //draw user's name
    fill(nameColor);
    textFont(font, nameSize);
    text(user.firstName + " " +user.lastName, textX, textY);
  }
  
}
