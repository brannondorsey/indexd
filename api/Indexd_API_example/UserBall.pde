class UserBall extends User{
  
  float x, y, s, offsetX, offsetY;
  color userColor = #1d5b89;
  int circlePadding = 20;
  boolean dragging = false;
  boolean rollover = false;
  
  
  UserBall(JSONObject user){
    super(user);
    randomizeLocation();
  }
  
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
  
  //assigns the size of the ellipse. called inside setup.
  void setSize(int _s){
    s = _s;
  }
  
  void display(int mx, int my){
    color fillColor = (rollover || dragging) ? 255 : userColor;
    fill(fillColor);
    noStroke();
    ellipse(x, y, s, s);
  }
  
  void drag(int mx, int my){
    if(dragging){
      x = mx - offsetX;//(offsetX-s/2)+mx;
      y = my - offsetY;
    }
  }
  
  void clicked(int mx, int my){
    if(isOver(mx, my)){
      dragging = true;
      offsetX  = ((mx > x) ? dist(mx, y, x, y) : -dist(mx, y, x, y));
      offsetY  = ((my > y) ? dist(x, my, x, y) : -dist(x, my, x, y));
    }
  }
  
  void rollover(int mx, int my){
    rollover = (isOver(mx, my) ? true : false);
  }
  
  boolean isOver(int mx, int my){
    return (dist(mx, my, x, y) <= s/2) ? true : false ;
  }
  
  void stopDragging(){
    dragging = false;
  }
}
