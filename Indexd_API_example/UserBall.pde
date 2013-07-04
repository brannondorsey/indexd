class UserBall extends User{
  
  float x, y, s;
  color userColor = #1d5b89;
  int circlePadding = 20;
  
  int noiseTime;
  int noiseScale = 2;
  
  UserBall(JSONObject user){
    super(user);
    randomizeLocation();
//    x = _x;
//    y = _y;
    noiseTime = int(random(10000000));
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
  
  void display(){
    fill(userColor);
    noStroke();
    ellipse(x, y, s, s);
  }
  
  void wiggle(){
    noiseTime++;
    float direction = random(1);
    float xIncrement = noise(noiseTime)*noiseScale;
    float yIncrement = noise(noiseTime+1000)*noiseScale;
    x = ((direction > .5) ? x+xIncrement : x-xIncrement); 
    y = ((direction > .5) ? y+yIncrement : y-yIncrement);
  }
  
  boolean isOver(int mx, int my){
    return (dist(mx, my, x, y) <= s/2) ? true : false ;
  }
}
