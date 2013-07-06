class UserBall extends User{
  //This class handles all of the methods assosciated with the
  //interactive balls that represent each user.
  //It extends the User class so that UserBall can have
  //access to the API column properties of the User class (i.e. first_name, url, etc...).
  
  float x, y, s, offsetX, offsetY;
  color userColor = #1d5b89;
  int circlePadding = 20;
  boolean dragging = false;
  boolean rollover = false;
  
  UserBall(JSONObject user){
    //call the User constructor
    super(user); 
    //randomly place the UserBall inside of the large red ellipse
    randomizeLocation();  
  }
  
  //sets the location of the UserBall inside of the area of the large red ellipse.
  //called inside this constructor
  void randomizeLocation(){
    
    // only needs to be done once (radius of circle to choose points in)
    float r = height/2-circlePadding;
    float rSquared = r*r;
    
    // actually pick a random point within circle
    x = random(-r,r);
    y = random(-1,1)*sqrt(rSquared-x*x);
    
    // move to screen pos
    x += width/2;
    y += height/2;
  }
  
  //assigns the size of the ellipse. Called inside setup.
  void setSize(int _s){
    s = _s;
  }
  
  //draws the UserBall to the screen. Called inside draw.
  void display(int mx, int my){
    color fillColor = (rollover || dragging) ? 255 : userColor;
    fill(fillColor);
    noStroke();
    ellipse(x, y, s, s);
  }
  
  //updates the x and y pos of the UserBall if it is being dragged
  void drag(int mx, int my){
    if(dragging){
      x = mx - offsetX;
      y = my - offsetY;
    }
  }
  
  //assigns the UserBall's offsets if the mouse is over it.
  //Called from inside mousePressed.
  void clicked(int mx, int my){
    if(isOver(mx, my)){
      dragging = true;
      offsetX  = ((mx > x) ? dist(mx, y, x, y) : -dist(mx, y, x, y));
      offsetY  = ((my > y) ? dist(x, my, x, y) : -dist(x, my, x, y));
    }
  }
  
  //set the UserBall's "rollover" boolean if the mouse is over it
  void rollover(int mx, int my){
    rollover = (isOver(mx, my) ? true : false);
  }
  
  //checks if the mouse is inside of the area of the UserBall.
  //called from inside clicked and rollover. 
  boolean isOver(int mx, int my){
    return (dist(mx, my, x, y) <= s/2) ? true : false ;
  }
  
  //sets the UserBall's "dragging" boolean.
  //called inside mouseReleased
  void stopDragging(){
    dragging = false;
  }
}
